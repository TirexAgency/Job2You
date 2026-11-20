<?php

namespace App\Http\Controllers;

use App\Exceptions\FiveOnePayException;
use App\Models\Payment;
use App\Models\Plan;
use App\Services\Payments\PaymentService;
use App\Services\Subscriptions\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Gère le paiement d'un abonnement candidat via FiveOne Pay.
 */
class CandidatePaymentController extends Controller
{
    /**
     * Démarre un paiement ou active directement une formule gratuite.
     */
    public function checkout(
        Request $request,
        Plan $plan,
        PaymentService $payments,
        SubscriptionService $subscriptions,
    ): RedirectResponse {
        abort_unless($plan->active, 404);

        if ($plan->isFree()) {
            $subscriptions->activate($request->user(), $plan);

            return redirect()->route('candidate.subscription')->with('status', 'plan-changed');
        }

        if (! $payments->isConfigured()) {
            return $this->checkoutWithoutProvider($request, $plan);
        }

        $pendingPayment = $payments->pendingPaymentFor($request->user(), $plan);

        if ($pendingPayment) {
            return redirect()->away($pendingPayment->payment_url);
        }

        try {
            $payment = $payments->startCheckout($request->user(), $plan);
        } catch (FiveOnePayException $exception) {
            return redirect()
                ->route('candidate.subscription')
                ->with('error', $exception->getMessage());
        }

        return redirect()->away($payment->payment_url);
    }

    /**
     * Retour du candidat depuis la page de paiement hébergée.
     */
    public function returnFromProvider(
        Request $request,
        Payment $payment,
        PaymentService $payments,
    ): RedirectResponse {
        abort_unless($payment->user_id === $request->user()->id, 403);
        abort_unless(in_array($request->query('outcome'), ['success', 'cancel'], true), 404);

        if ($request->query('outcome') === 'success') {
            $payment = $payments->refresh($payment);
        }

        if ($request->query('outcome') === 'cancel' && ! $payment->isPaid()) {
            return redirect()
                ->route('candidate.subscription')
                ->with('info', 'Paiement annulé. Votre formule actuelle reste inchangée.');
        }

        return match ($payment->status) {
            Payment::STATUS_PAID => redirect()->route('candidate.subscription')->with('status', 'plan-changed'),
            Payment::STATUS_FAILED => redirect()->route('candidate.subscription')->with('error', 'Le paiement n’a pas abouti. Vous pouvez réessayer.'),
            default => redirect()->route('candidate.subscription')->with('info', 'Votre paiement est en cours de confirmation. Cette page se met à jour dès réception du webhook FiveOne Pay.'),
        };
    }

    /**
     * Vérifie manuellement l'état d'un paiement en attente.
     */
    public function status(Request $request, Payment $payment, PaymentService $payments): RedirectResponse
    {
        abort_unless($payment->user_id === $request->user()->id, 403);

        $payment = $payments->refresh($payment);

        return match ($payment->status) {
            Payment::STATUS_PAID => redirect()->route('candidate.subscription')->with('status', 'plan-changed'),
            Payment::STATUS_FAILED => redirect()->route('candidate.subscription')->with('error', 'Le paiement n’a pas abouti. Vous pouvez réessayer.'),
            default => redirect()->route('candidate.subscription')->with('info', 'Votre paiement est toujours en attente de confirmation.'),
        };
    }

    /**
     * Affiche la page de simulation locale (développement et tests uniquement).
     */
    public function sandbox(Request $request, Payment $payment): View
    {
        $this->ensureSandboxIsAvailable($payment, $request);

        return view('candidate.payment-sandbox', compact('payment'));
    }

    /**
     * Confirme ou annule un paiement simulé en local.
     */
    public function confirmSandbox(
        Request $request,
        Payment $payment,
        string $outcome,
        PaymentService $payments,
    ): RedirectResponse {
        $this->ensureSandboxIsAvailable($payment, $request);
        abort_unless(in_array($outcome, ['success', 'failed'], true), 404);

        if ($payment->isPaid()) {
            return redirect()->route('candidate.subscription')->with('status', 'plan-changed');
        }

        if ($outcome === 'failed') {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'error_message' => 'Paiement annulé en mode simulation.',
            ]);

            return redirect()->route('candidate.subscription')->with('status', 'payment-failed');
        }

        $payments->markPaid($payment, [
            'id' => $payment->provider_reference,
            'reference' => $payment->payload_reference,
            'amount' => (int) round((float) $payment->amount),
        ]);

        return redirect()->route('candidate.subscription')->with('status', 'plan-changed');
    }

    /**
     * Utilise la simulation locale lorsque FiveOne Pay n'est pas configuré.
     */
    private function checkoutWithoutProvider(Request $request, Plan $plan): RedirectResponse
    {
        if (! app()->isLocal() && ! app()->runningUnitTests()) {
            return redirect()
                ->route('candidate.subscription')
                ->with('error', 'Le paiement en ligne est momentanément indisponible.');
        }

        $payment = Payment::create([
            'user_id' => $request->user()->id,
            'plan_id' => $plan->id,
            'provider' => 'fiveonepay',
            'provider_reference' => 'sandbox-'.Str::uuid(),
            'amount' => $plan->price,
            'status' => Payment::STATUS_PENDING,
            'payload_reference' => (string) Str::uuid(),
        ]);

        return redirect()->route('candidate.payment.sandbox', $payment);
    }

    /**
     * Empêche l'utilisation de la simulation hors développement et tests.
     */
    private function ensureSandboxIsAvailable(Payment $payment, Request $request): void
    {
        abort_unless($payment->user_id === $request->user()->id, 403);
        abort_unless(app()->isLocal() || app()->runningUnitTests(), 404);
    }
}
