<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Suivi des paiements et remboursement manuel.
 */
class PaymentController extends Controller
{
    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {}

    /**
     * Liste les paiements avec filtres et indicateurs.
     */
    public function index(Request $request): View
    {
        $payments = Payment::query()
            ->with(['user', 'plan'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search): void {
                    $query->where('payload_reference', 'like', "%{$search}%")
                        ->orWhere('provider_reference', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('to')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statusCounts = Payment::query()
            ->selectRaw('status, COUNT(*) as total, COALESCE(SUM(amount), 0) as revenue')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $stats = [
            'paid_total' => (float) ($statusCounts[Payment::STATUS_PAID]->revenue ?? 0),
            'paid' => (int) ($statusCounts[Payment::STATUS_PAID]->total ?? 0),
            'pending' => (int) ($statusCounts[Payment::STATUS_PENDING]->total ?? 0),
            'failed' => (int) ($statusCounts[Payment::STATUS_FAILED]->total ?? 0),
            'refunded' => (int) ($statusCounts[Payment::STATUS_REFUNDED]->total ?? 0),
        ];

        return view('admin.payments.index', [
            'payments' => $payments,
            'stats' => $stats,
            'statuses' => Payment::statusLabels(),
        ]);
    }

    /**
     * Marque un paiement confirmé comme remboursé.
     */
    public function refund(Payment $payment): RedirectResponse
    {
        if (! $payment->isPaid()) {
            return redirect()
                ->back()
                ->with('error', 'Seuls les paiements confirmés peuvent être remboursés.');
        }

        $payment->update([
            'status' => Payment::STATUS_REFUNDED,
            'refunded_at' => now(),
        ]);

        $this->activityLogger->adminAction(
            'Remboursement paiement',
            'Paiement #'.$payment->id.' ('.number_format((float) $payment->amount, 0, ',', ' ').' Ar)',
        );

        return redirect()
            ->back()
            ->with('success', 'Le paiement a été marqué comme remboursé.');
    }
}
