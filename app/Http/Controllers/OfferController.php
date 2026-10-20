<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OfferController extends Controller
{
    /**
     * Liste paginée des offres avec filtres.
     * Supporte les requêtes AJAX pour recherche dynamique.
     */
    public function index(Request $request)
    {
        $query = $this->buildQuery($request);

        $offers = $query->paginate(12);

        // Récupérer les types de contrat uniques pour le filtre
        $contractTypes = Offer::active()
            ->select('contract_type')
            ->distinct()
            ->pluck('contract_type');

        // Requête AJAX : retourner JSON pour mise à jour partielle
        if ($request->expectsJson() || $request->ajax()) {
            return $this->jsonResponse($offers, $contractTypes);
        }

        return view('offers.index', compact('offers', 'contractTypes'));
    }

    /**
     * Endpoint AJAX pour recherche dynamique (infinite scroll, filtres temps réel).
     */
    public function search(Request $request): JsonResponse
    {
        $query = $this->buildQuery($request);

        $offers = $query->paginate(12);

        $contractTypes = Offer::active()
            ->select('contract_type')
            ->distinct()
            ->pluck('contract_type');

        return $this->jsonResponse($offers, $contractTypes);
    }

    /**
     * Fiche détail d'une offre.
     */
    public function show(Offer $offer)
    {
        $offer->load(['source', 'offerSkills.skill']);

        // Récupérer des offres similaires
        $similarOffers = Offer::with(['source', 'offerSkills.skill'])
            ->active()
            ->where('id', '!=', $offer->id)
            ->where(function ($query) use ($offer) {
                $query->where('title', 'like', "%{$offer->title}%")
                    ->orWhere('company', $offer->company)
                    ->orWhere('location', $offer->location);
            })
            ->limit(3)
            ->get();

        return view('offers.show', compact('offer', 'similarOffers'));
    }

    /**
     * Construit la query de base avec tous les filtres.
     */
    protected function buildQuery(Request $request)
    {
        $query = Offer::with(['source', 'offerSkills.skill'])
            ->active();

        // Filtre par recherche texte (titre, entreprise, description)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtre par localisation
        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->input('location')}%");
        }

        // Filtre par type de contrat
        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->input('contract_type'));
        }

        // Filtre par date de publication
        if ($request->filled('date_from')) {
            $query->whereDate('published_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('published_at', '<=', $request->input('date_to'));
        }

        // Filtre par source
        if ($request->filled('source_id')) {
            $query->where('source_id', $request->input('source_id'));
        }

        // Tri
        $sort = $request->input('sort', 'recent');
        match ($sort) {
            'recent' => $query->recent(),
            'oldest' => $query->oldest('published_at'),
            'title_asc' => $query->orderBy('title', 'asc'),
            'title_desc' => $query->orderBy('title', 'desc'),
            default => $query->recent(),
        };

        return $query;
    }

    /**
     * Prépare la réponse JSON pour les requêtes AJAX.
     */
    protected function jsonResponse($offers, $contractTypes): JsonResponse
    {
        $data = [
            'offers' => $offers->items(),
            'pagination' => [
                'current_page' => $offers->currentPage(),
                'last_page' => $offers->lastPage(),
                'per_page' => $offers->perPage(),
                'total' => $offers->total(),
                'from' => $offers->firstItem(),
                'to' => $offers->lastItem(),
                'has_more_pages' => $offers->hasMorePages(),
            ],
            'contract_types' => $contractTypes,
        ];

        return response()->json($data);
    }
}