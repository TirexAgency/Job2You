<?php

namespace App\Services\Sms;

use App\DTO\SmsSendResult;
use App\Exceptions\SmsProviderException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Client HTTP du fournisseur TirexSMS.
 *
 * Documentation de l'endpoint :
 * POST /api/v1/send-sms
 * Authorization: Bearer <clé API>
 * { "to": "+261340000000", "message": "...", "sender": "Job2You" }
 */
class TirexSmsClient implements SmsProvider
{
    /**
     * Envoie un SMS via l'API TirexSMS.
     *
     * @throws SmsProviderException
     */
    public function send(string $recipient, string $message, ?string $idempotencyKey = null): SmsSendResult
    {
        $url = (string) config('services.sms.url');
        $apiKey = (string) config('services.sms.key');

        if ($url === '' || $apiKey === '') {
            throw new SmsProviderException('La configuration SMS est incomplète (SMS_API_URL ou SMS_API_KEY).');
        }

        try {
            $request = Http::withToken($apiKey)
                ->acceptJson()
                ->connectTimeout(3)
                ->timeout((int) config('services.sms.timeout', 15));

            if ($idempotencyKey !== null && $idempotencyKey !== '') {
                $request = $request->withHeaders(['Idempotency-Key' => $idempotencyKey]);
            }

            $response = $request->post($url, [
                'to' => $recipient,
                'message' => $message,
                'sender' => (string) config('services.sms.sender', config('app.name')),
            ]);
        } catch (ConnectionException $exception) {
            throw new SmsProviderException(
                'Impossible de joindre le fournisseur SMS.',
                previous: $exception,
            );
        }

        if ($response->failed()) {
            throw new SmsProviderException(
                $this->errorMessage($response),
                $response->status(),
            );
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            return SmsSendResult::success(providerStatus: 'sent');
        }

        if (array_key_exists('success', $payload) && ! filter_var($payload['success'], FILTER_VALIDATE_BOOLEAN)) {
            throw new SmsProviderException(
                $this->payloadErrorMessage($payload, 'Le fournisseur SMS a refusé l’envoi.'),
            );
        }

        return SmsSendResult::success(
            providerReference: $this->providerReference($payload),
            providerStatus: $this->providerStatus($payload),
        );
    }

    /**
     * Extrait le message d'erreur d'une réponse HTTP en échec.
     */
    private function errorMessage(Response $response): string
    {
        $payload = $response->json();

        if (is_array($payload)) {
            return $this->payloadErrorMessage($payload, 'Erreur fournisseur SMS (HTTP '.$response->status().').');
        }

        return 'Erreur fournisseur SMS (HTTP '.$response->status().').';
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function payloadErrorMessage(array $payload, string $fallback): string
    {
        $message = data_get($payload, 'error.message')
            ?? data_get($payload, 'message')
            ?? data_get($payload, 'error')
            ?? $fallback;

        return is_string($message) && $message !== '' ? $message : $fallback;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function providerReference(array $payload): ?string
    {
        $reference = data_get($payload, 'data.id')
            ?? data_get($payload, 'data.sms_id')
            ?? data_get($payload, 'data.message_id')
            ?? data_get($payload, 'sms_id')
            ?? data_get($payload, 'message_id')
            ?? data_get($payload, 'reference');

        return is_scalar($reference) ? (string) $reference : null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function providerStatus(array $payload): ?string
    {
        $status = data_get($payload, 'data.status')
            ?? data_get($payload, 'status')
            ?? 'sent';

        return is_scalar($status) ? (string) $status : null;
    }
}
