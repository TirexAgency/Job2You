<?php

namespace App\Services\Payments;

use App\Exceptions\FiveOnePayException;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionActivated;
use App\Services\Notifications\SafeNotifier;
use App\Services\Subscriptions\SubscriptionService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Orchestre les paiements FiveOne Pay et l'activation des abonnements.
 */
class PaymentService
{
    public function __construct(
        private readonly FiveOnePayClient $client,
        private readonly SubscriptionService $subscriptions,
        private readonly SafeNotifier $notifier,
    ) {}

    /**
     * Indique si les clés FiveOne Pay sont configurées.
     */
    public function isConfigured(): bool
    {
        return (string) config('services.fiveonepay.secret') !== '';
    }

    /**
     * Crée un paiement FiveOne Pay et redirige le candidat vers sa page de paiement.
     *
     * @throws FiveOnePayException
     */
    public function startCheckout(User $user, Plan $plan): Payment
    {
        $reference = 'J2Y-'.Str::upper((string) Str::uuid());
        $phone = $this->normalizePhone($user->phone);
        $operator = $this->operatorForPhone($phone);

        $payment = Payment::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'provider' => 'fiveonepay',
            'provider_reference' => 'pending-'.Str::uuid(),
            'amount' => $plan->price,
            'status' => Payment::STATUS_PENDING,
            'payload_reference' => $reference,
        ]);

        $payload = [
            'amount' => (int) round((float) $plan->price),
            'reference' => $reference,
            'description' => Str::limit('Abonnement '.$plan->name.' — Job2You', 255, ''),
            'callbackUrl' => route('webhooks.fiveonepay'),
            'successUrl' => route('candidate.payment.return', ['payment' => $payment, 'outcome' => 'success']),
            'cancelUrl' => route('candidate.payment.return', ['payment' => $payment, 'outcome' => 'cancel']),
            'expiresInMinutes' => 30,
        ];

        if ($operator !== null && $phone !== null) {
            $payload['operator'] = $operator;
            $payload['payerNumber'] = $phone;
        }

        try {
            $data = $this->client->createPayment($payload, $reference);
        } catch (FiveOnePayException $exception) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'error_message' => Str::limit($exception->getMessage(), 1000, ''),
            ]);

            throw $exception;
        }

        $payment->update([
            'provider_reference' => $data['id'],
            'payment_url' => $data['payment_url'],
            'operator' => $data['operator'] ?? $operator,
            'expires_at' => isset($data['expires_at']) ? Carbon::parse($data['expires_at']) : now()->addMinutes(30),
            'payload' => $data,
        ]);

        return $payment->refresh();
    }

    /**
     * Retourne un paiement en attente encore utilisable pour cette formule.
     */
    public function pendingPaymentFor(User $user, Plan $plan): ?Payment
    {
        return Payment::query()
            ->where('user_id', $user->id)
            ->where('plan_id', $plan->id)
            ->where('status', Payment::STATUS_PENDING)
            ->whereNotNull('payment_url')
            ->where(function ($query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now()->addMinutes(2));
            })
            ->latest()
            ->first();
    }

    /**
     * Interroge FiveOne Pay pour synchroniser l'état local du paiement.
     */
    public function refresh(Payment $payment): Payment
    {
        if ($payment->isPaid() || str_starts_with($payment->provider_reference, 'pending-')) {
            return $payment;
        }

        try {
            $data = $this->client->findPayment($payment->provider_reference);
        } catch (FiveOnePayException $exception) {
            Log::warning('Synchronisation FiveOne Pay impossible.', [
                'payment_id' => $payment->id,
                'message' => $exception->getMessage(),
            ]);

            return $payment;
        }

        $this->applyProviderStatus($payment, $data);

        return $payment->refresh();
    }

    /**
     * Traite un événement webhook FiveOne Pay déjà authentifié.
     *
     * @param  array<string, mixed>  $payload
     */
    public function handleWebhookEvent(string $event, array $payload): void
    {
        if (! in_array($event, ['payment.success', 'payment.expired'], true)) {
            return;
        }

        $data = $payload['data'] ?? null;

        if (! is_array($data)) {
            Log::warning('Webhook FiveOne Pay sans données de paiement.', ['event' => $event]);

            return;
        }

        $expectedStatus = match ($event) {
            'payment.success' => 'SUCCESS',
            'payment.expired' => 'EXPIRED',
            default => null,
        };

        if ($expectedStatus !== null && ($data['status'] ?? null) !== $expectedStatus) {
            Log::warning('Webhook FiveOne Pay incohérent, événement ignoré.', [
                'event' => $event,
                'status' => $data['status'] ?? null,
            ]);

            return;
        }

        $payment = $this->findPayment($data);

        if (! $payment) {
            Log::warning('Webhook FiveOne Pay pour un paiement inconnu.', [
                'event' => $event,
                'payment_id' => $data['id'] ?? null,
                'reference' => $data['reference'] ?? null,
            ]);

            return;
        }

        if (! $this->amountMatches($payment, $data)) {
            return;
        }

        if ($event === 'payment.success') {
            $this->markPaid($payment, $data);
        } elseif ($event === 'payment.expired') {
            $this->markExpired($payment, $data);
        }
    }

    /**
     * Confirme un paiement et active l'abonnement une seule fois.
     *
     * @param  array<string, mixed>  $data
     */
    public function markPaid(Payment $payment, array $data = []): ?Subscription
    {
        $subscription = DB::transaction(function () use ($payment, $data): ?Subscription {
            $lockedPayment = Payment::query()
                ->with(['plan', 'user'])
                ->lockForUpdate()
                ->find($payment->id);

            if (! $lockedPayment || $lockedPayment->isPaid() || ! $lockedPayment->plan || ! $lockedPayment->user) {
                return null;
            }

            $subscription = $this->subscriptions->activate(
                $lockedPayment->user,
                $lockedPayment->plan,
                $lockedPayment,
                notify: false,
            );

            $attributes = [
                'status' => Payment::STATUS_PAID,
                'paid_at' => now(),
                'error_message' => null,
            ];

            if (! empty($data['id'])) {
                $attributes['provider_reference'] = $data['id'];
            }

            if (! empty($data['operator'])) {
                $attributes['operator'] = $data['operator'];
            }

            if ($data !== []) {
                $attributes['payload'] = $data;
            }

            $lockedPayment->update($attributes);

            return $subscription;
        });

        if ($subscription === null) {
            return null;
        }

        $subscription->loadMissing('plan', 'user');
        $this->notifier->send($subscription->user, new SubscriptionActivated($subscription));

        return $subscription;
    }

    /**
     * Marque un paiement comme expiré sans toucher à un abonnement déjà actif.
     *
     * @param  array<string, mixed>  $data
     */
    public function markExpired(Payment $payment, array $data = []): bool
    {
        return DB::transaction(function () use ($payment, $data): bool {
            $lockedPayment = Payment::query()->lockForUpdate()->find($payment->id);

            if (! $lockedPayment || $lockedPayment->isPaid()) {
                return false;
            }

            $attributes = [
                'status' => Payment::STATUS_FAILED,
                'error_message' => 'Le lien de paiement a expiré sans confirmation.',
            ];

            if ($data !== []) {
                $attributes['payload'] = $data;
            }

            $lockedPayment->update($attributes);

            return true;
        });
    }

    /**
     * Applique un statut FiveOne Pay à un paiement local.
     *
     * @param  array<string, mixed>  $data
     */
    private function applyProviderStatus(Payment $payment, array $data): void
    {
        if (! $this->amountMatches($payment, $data)) {
            return;
        }

        $payment->update([
            'payload' => $data,
            'operator' => $data['operator'] ?? $payment->operator,
        ]);

        match ($data['status'] ?? null) {
            'SUCCESS' => $this->markPaid($payment, $data),
            'EXPIRED' => $this->markExpired($payment, $data),
            default => null,
        };
    }

    /**
     * Retrouve le paiement local à partir de l'identifiant ou de la référence FiveOne Pay.
     *
     * @param  array<string, mixed>  $data
     */
    private function findPayment(array $data): ?Payment
    {
        $query = Payment::query()->with(['plan', 'user']);

        if (! empty($data['id'])) {
            $payment = (clone $query)->where('provider_reference', $data['id'])->first();

            if ($payment) {
                return $payment;
            }
        }

        if (! empty($data['reference'])) {
            return $query->where('payload_reference', $data['reference'])->first();
        }

        return null;
    }

    /**
     * Vérifie que le montant confirmé correspond au montant de la formule.
     *
     * @param  array<string, mixed>  $data
     */
    private function amountMatches(Payment $payment, array $data): bool
    {
        if (array_key_exists('currency', $data) && strtoupper((string) $data['currency']) !== 'MGA') {
            Log::error('Devise FiveOne Pay inconnue, paiement ignoré.', [
                'payment_id' => $payment->id,
                'currency' => $data['currency'],
            ]);

            return false;
        }

        if (! array_key_exists('amount', $data)) {
            return true;
        }

        $expected = (int) round((float) $payment->amount);
        $received = (int) $data['amount'];

        if ($expected === $received) {
            return true;
        }

        Log::error('Montant FiveOne Pay incohérent, paiement ignoré.', [
            'payment_id' => $payment->id,
            'expected' => $expected,
            'received' => $received,
        ]);

        return false;
    }

    /**
     * Normalise un numéro malgache au format 0XXXXXXXXX.
     */
    private function normalizePhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === null || $digits === '') {
            return null;
        }

        if (str_starts_with($digits, '261')) {
            $digits = '0'.substr($digits, 3);
        }

        return preg_match('/^0[1-9]\d{8}$/', $digits) === 1 ? $digits : null;
    }

    /**
     * Déduit l'opérateur Mobile Money depuis le préfixe du numéro.
     */
    private function operatorForPhone(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        return match (substr($phone, 0, 3)) {
            '034', '036', '038' => 'MVOLA',
            '032', '037' => 'ORANGE_MONEY',
            '033', '035' => 'AIRTEL_MONEY',
            default => null,
        };
    }
}
