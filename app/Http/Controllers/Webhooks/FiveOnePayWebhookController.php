<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\PaymentWebhookEvent;
use App\Services\Payments\FiveOnePayClient;
use App\Services\Payments\PaymentService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Reçoit les webhooks signés de FiveOne Pay.
 */
class FiveOnePayWebhookController extends Controller
{
    /**
     * Vérifie la signature puis traite l'événement de façon idempotente.
     */
    public function __invoke(
        Request $request,
        FiveOnePayClient $client,
        PaymentService $payments,
    ): JsonResponse {
        if ((string) config('services.fiveonepay.webhook_secret') === '') {
            Log::error('Webhook FiveOne Pay reçu sans secret de signature configuré.');

            return response()->json(['message' => 'Webhook non configuré.'], 503);
        }

        $rawBody = $request->getContent();

        if (! $client->hasValidWebhookSignature($rawBody, $request->header('X-FiveOne-Signature'))) {
            Log::warning('Webhook FiveOne Pay refusé : signature invalide.');

            return response()->json(['message' => 'Signature invalide.'], 400);
        }

        $payload = json_decode($rawBody, true);

        if (! is_array($payload) || empty($payload['event'])) {
            return response()->json(['message' => 'Événement invalide.'], 400);
        }

        $event = (string) $payload['event'];
        $eventId = (string) ($request->header('X-FiveOne-Event-Id') ?: ($payload['id'] ?? hash('sha256', $rawBody)));

        try {
            $webhookEvent = PaymentWebhookEvent::query()->firstOrCreate(
                ['event_id' => $eventId],
                ['type' => $event, 'payload' => $payload],
            );
        } catch (UniqueConstraintViolationException) {
            return response()->json(['received' => true, 'duplicate' => true]);
        }

        if (! $webhookEvent->wasRecentlyCreated) {
            return response()->json(['received' => true, 'duplicate' => true]);
        }

        try {
            $payments->handleWebhookEvent($event, $payload);
            $webhookEvent->update(['processed_at' => now()]);
        } catch (Throwable $exception) {
            $webhookEvent->delete();

            Log::error('Échec du traitement du webhook FiveOne Pay.', [
                'event' => $event,
                'event_id' => $eventId,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        return response()->json(['received' => true]);
    }
}
