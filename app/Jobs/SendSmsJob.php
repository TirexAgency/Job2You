<?php

namespace App\Jobs;

use App\Exceptions\SmsProviderException;
use App\Models\SmsLog;
use App\Models\User;
use App\Services\Sms\SmsProvider;
use App\Services\Sms\SmsQuotaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Envoie un SMS via la queue et trace son statut dans sms_logs.
 *
 * La clé d'idempotence empêche qu'une même alerte soit envoyée deux fois.
 */
class SendSmsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Nombre maximal de tentatives.
     */
    public int $tries = 3;

    /**
     * Délais progressifs entre les tentatives (secondes).
     *
     * @var array<int, int>
     */
    public array $backoff = [10, 60, 300];

    /**
     * Timeout du job (secondes).
     */
    public int $timeout = 30;

    /**
     * Durée du verrou d'unicité (secondes).
     */
    public int $uniqueFor = 3600;

    public function __construct(
        public int $userId,
        public ?int $offerId,
        public string $recipient,
        public string $message,
        public string $idempotencyKey,
    ) {
        $this->onQueue('sms');
    }

    /**
     * Exécute l'envoi du SMS.
     */
    public function handle(SmsProvider $provider, SmsQuotaService $quota): void
    {
        $user = User::find($this->userId);

        if (! $user) {
            Log::warning('SendSmsJob: Utilisateur introuvable', ['user_id' => $this->userId]);

            return;
        }

        $log = $this->findOrCreateLog($user);

        if (in_array($log->status, [SmsLog::STATUS_SENT, SmsLog::STATUS_FAILED], true)) {
            return;
        }

        if (! $log->quota_consumed && ! $quota->reserve($user, $log)) {
            $log->update([
                'status' => SmsLog::STATUS_FAILED,
                'error_message' => 'Quota SMS épuisé.',
            ]);

            Log::info('SendSmsJob: Quota SMS épuisé', [
                'user_id' => $user->id,
                'sms_log_id' => $log->id,
            ]);

            return;
        }

        $log->increment('attempts');

        $result = $provider->send($this->recipient, $this->message, $this->idempotencyKey);

        if (! $result->success) {
            throw new SmsProviderException($result->errorMessage ?? 'Échec inconnu du fournisseur SMS.');
        }

        $log->update([
            'status' => SmsLog::STATUS_SENT,
            'provider_reference' => $result->providerReference,
            'provider_status' => $result->providerStatus,
            'error_message' => null,
            'sent_at' => now(),
        ]);

        Log::info('SendSmsJob: SMS envoyé', [
            'user_id' => $user->id,
            'sms_log_id' => $log->id,
            'provider_reference' => $result->providerReference,
            'provider_status' => $result->providerStatus,
        ]);
    }

    /**
     * Libère le quota et marque le SMS en échec après épuisement des tentatives.
     */
    public function failed(?Throwable $exception): void
    {
        $log = SmsLog::where('idempotency_key', $this->idempotencyKey)->first();

        if (! $log || $log->status === SmsLog::STATUS_SENT) {
            return;
        }

        app(SmsQuotaService::class)->release($log);

        $log->update([
            'status' => SmsLog::STATUS_FAILED,
            'error_message' => Str::limit($exception?->getMessage() ?? 'Échec de l’envoi du SMS.', 1000),
        ]);

        Log::error('SendSmsJob: Échec définitif', [
            'user_id' => $this->userId,
            'sms_log_id' => $log->id,
            'error' => $exception?->getMessage(),
        ]);
    }

    /**
     * Identifiant unique du job (déduplication à la mise en file).
     */
    public function uniqueId(): string
    {
        return $this->idempotencyKey;
    }

    /**
     * Tags pour le monitoring Horizon.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return [
            'sms',
            'user:'.$this->userId,
            'offer:'.($this->offerId ?? 'none'),
        ];
    }

    /**
     * Récupère le journal existant ou le crée une seule fois.
     */
    private function findOrCreateLog(User $user): SmsLog
    {
        $attributes = [
            'user_id' => $user->id,
            'offer_id' => $this->offerId,
            'recipient' => $this->recipient,
            'message' => $this->message,
            'provider' => 'tirex',
            'status' => SmsLog::STATUS_PENDING,
        ];

        try {
            return SmsLog::firstOrCreate(
                ['idempotency_key' => $this->idempotencyKey],
                $attributes,
            );
        } catch (UniqueConstraintViolationException) {
            return SmsLog::where('idempotency_key', $this->idempotencyKey)->firstOrFail();
        }
    }
}
