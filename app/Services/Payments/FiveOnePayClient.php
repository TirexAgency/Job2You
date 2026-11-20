<?php

namespace App\Services\Payments;

use App\Exceptions\FiveOnePayException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Client HTTP de l'API FiveOne Pay.
 *
 * Documentation : https://api.fiveonepay.com/v1
 * Authentification : Authorization: Bearer sk_…
 */
class FiveOnePayClient
{
    /**
     * Crée un paiement et retourne la réponse de FiveOne Pay.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     *
     * @throws FiveOnePayException
     */
    public function createPayment(array $payload, string $idempotencyKey): array
    {
        try {
            $response = $this->client()
                ->withHeaders(['Idempotency-Key' => $idempotencyKey])
                ->post('/payments', $payload);
        } catch (ConnectionException $exception) {
            throw new FiveOnePayException(
                'Impossible de joindre FiveOne Pay. Veuillez réessayer dans un instant.',
                previous: $exception,
            );
        }

        if ($response->failed()) {
            throw new FiveOnePayException(
                $this->errorMessage($response),
                $response->status(),
            );
        }

        $data = $response->json();

        if (! is_array($data) || empty($data['id']) || empty($data['payment_url'])) {
            throw new FiveOnePayException('FiveOne Pay a renvoyé une réponse inattendue.');
        }

        return $data;
    }

    /**
     * Récupère l'état courant d'un paiement.
     *
     * @return array<string, mixed>
     *
     * @throws FiveOnePayException
     */
    public function findPayment(string $paymentId): array
    {
        try {
            $response = $this->client()->get('/payments/'.rawurlencode($paymentId));
        } catch (ConnectionException $exception) {
            throw new FiveOnePayException(
                'Impossible de joindre FiveOne Pay. Veuillez réessayer dans un instant.',
                previous: $exception,
            );
        }

        if ($response->failed()) {
            throw new FiveOnePayException(
                $this->errorMessage($response),
                $response->status(),
            );
        }

        $data = $response->json();

        if (! is_array($data) || empty($data['status'])) {
            throw new FiveOnePayException('FiveOne Pay a renvoyé une réponse inattendue.');
        }

        return $data;
    }

    /**
     * Vérifie la signature HMAC-SHA256 d'un webhook sur le corps brut.
     */
    public function hasValidWebhookSignature(string $rawBody, ?string $signature): bool
    {
        $secret = (string) config('services.fiveonepay.webhook_secret');
        $signature = trim((string) $signature);

        if ($secret === '' || $signature === '') {
            return false;
        }

        if (str_starts_with($signature, 'sha256=')) {
            $signature = substr($signature, 7);
        }

        return hash_equals(hash_hmac('sha256', $rawBody, $secret), $signature);
    }

    /**
     * Construit le client HTTP avec la configuration de l'application.
     *
     * @throws FiveOnePayException
     */
    private function client(): PendingRequest
    {
        $baseUrl = rtrim((string) config('services.fiveonepay.base_url'), '/');
        $secret = (string) config('services.fiveonepay.secret');

        if ($baseUrl === '' || $secret === '') {
            throw new FiveOnePayException('La configuration FiveOne Pay est incomplète (FIVEONEPAY_API_URL ou FIVEONEPAY_API_KEY).');
        }

        return Http::baseUrl($baseUrl)
            ->withToken($secret)
            ->acceptJson()
            ->connectTimeout((int) config('services.fiveonepay.connect_timeout', 5))
            ->timeout((int) config('services.fiveonepay.timeout', 15));
    }

    /**
     * Extrait un message lisible depuis une réponse d'erreur FiveOne Pay.
     */
    private function errorMessage(Response $response): string
    {
        $payload = $response->json();

        if (is_array($payload) && array_key_exists('message', $payload)) {
            $message = $payload['message'];

            if (is_array($message)) {
                $message = implode(' ', array_filter($message, 'is_string'));
            }

            if (is_string($message) && $message !== '') {
                return $message;
            }
        }

        return 'FiveOne Pay a refusé la requête (HTTP '.$response->status().').';
    }
}
