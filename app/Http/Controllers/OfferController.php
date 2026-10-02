<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $query = Offer::with(['source', 'offerSkills.skill'])
            ->active()
            ->recent();

        // Filtre par recherche
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

        $offers = $query->paginate(12);

        // Récupérer les types de contrat uniques pour le filtre
        $contractTypes = Offer::active()
            ->select('contract_type')
            ->distinct()
            ->pluck('contract_type');

        return view('offers.index', compact('offers', 'contractTypes'));
    }

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
}
