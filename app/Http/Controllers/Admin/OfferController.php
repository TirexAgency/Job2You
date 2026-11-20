<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Source;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Modération et suppression des offres collectées.
 */
class OfferController extends Controller
{
    /**
     * Statuts disponibles pour une offre.
     *
     * @var array<string, string>
     */
    public const STATUS_LABELS = [
        'active' => 'Active',
        'expired' => 'Expirée',
        'archived' => 'Archivée',
    ];

    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {}

    /**
     * Liste les offres avec filtres de modération.
     */
    public function index(Request $request): View
    {
        $offers = Offer::query()
            ->with('source')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('source_id'), fn ($query) => $query->where('source_id', $request->integer('source_id')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = Offer::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.offers.index', [
            'offers' => $offers,
            'sources' => Source::query()->orderBy('name')->get(['id', 'name']),
            'statuses' => self::STATUS_LABELS,
            'stats' => [
                'active' => (int) ($stats['active'] ?? 0),
                'expired' => (int) ($stats['expired'] ?? 0),
                'archived' => (int) ($stats['archived'] ?? 0),
            ],
        ]);
    }

    /**
     * Modère une offre en changeant son statut.
     */
    public function updateStatus(Request $request, Offer $offer): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(self::STATUS_LABELS))],
        ]);

        $previousStatus = $offer->status;
        $offer->update(['status' => $validated['status']]);

        $this->activityLogger->adminAction(
            'Modération offre',
            "« {$offer->title} » : {$previousStatus} → {$validated['status']}",
        );

        return redirect()
            ->back()
            ->with('success', "Statut de l’offre « {$offer->title} » mis à jour.");
    }

    /**
     * Supprime définitivement une offre.
     */
    public function destroy(Offer $offer): RedirectResponse
    {
        $title = $offer->title;
        $offer->delete();

        $this->activityLogger->adminAction('Suppression offre', "« {$title} »");

        return redirect()
            ->route('admin.offers.index')
            ->with('success', "Offre « {$title} » supprimée avec succès.");
    }
}
