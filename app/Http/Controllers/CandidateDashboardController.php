<?php

namespace App\Http\Controllers;

use App\Models\JobMatch;
use App\Models\Offer;
use App\Models\Setting;
use App\Services\MatchingService;
use App\Services\Sms\SmsQuotaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CandidateDashboardController extends Controller
{
    public function __invoke(Request $request, MatchingService $matchingService, SmsQuotaService $smsQuotaService): View
    {
        $user = $request->user();
        $profile = $user->candidateProfile()->with(['experiences', 'educations', 'candidateSkills.skill'])->first();

        // Les matchings sont normalement calculés par la commande ou après le
        // scraping. On les initialise ici pour éviter un tableau de bord vide
        // lorsqu'un candidat vient de compléter son profil.
        if ($profile && JobMatch::where('user_id', $user->id)->doesntExist()) {
            $matchingService->computeForProfile($profile);
        }

        $alertThreshold = (float) Setting::get('matching.alert_threshold', 0.7);
        $subscription = $user->activeSubscriptions()->with('plan')->first();
        $smsQuota = $smsQuotaService->quotaLimit($user);
        $smsRemaining = max($smsQuotaService->remaining($user), 0);

        // Statistiques du candidat
        $stats = [
            'matches_total' => JobMatch::where('user_id', $user->id)->count(),
            'matches_above_threshold' => JobMatch::where('user_id', $user->id)
                ->where('score', '>=', $alertThreshold)
                ->count(),
            'matches_this_week' => JobMatch::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subWeek())
                ->count(),
            'sms_remaining' => $smsRemaining,
            'sms_quota' => $smsQuota,
            'sms_used' => max($smsQuota - $smsRemaining, 0),
            'plan' => $subscription?->plan?->name ?? $user->plan,
            'plan_display' => $user->planDisplayName(),
            'subscription' => $subscription,
        ];

        // Recommandations (matchings au-dessus du seuil). Si aucune offre
        // n'atteint encore le seuil, on affiche les meilleures correspondances
        // pour que la section ne reste pas vide.
        $recommendations = $matchingService->getRecommendations($user->id, 10);
        $recommendationsAreFallback = false;

        if ($recommendations->isEmpty() && $profile) {
            $recommendations = JobMatch::with(['offer.source', 'offer.offerSkills.skill'])
                ->where('user_id', $user->id)
                ->orderByDesc('score')
                ->limit(10)
                ->get();
            $recommendationsAreFallback = $recommendations->isNotEmpty();
        }

        // Toutes les offres (pour l'onglet "Toutes les offres")
        $allOffers = Offer::with('source')
            ->active()
            ->orderByDesc('published_at')
            ->paginate(6);

        // Offres similaires basées sur le profil
        $similarOffers = collect();
        if ($profile) {
            $similarOffers = Offer::with('source')
                ->active()
                ->where('id', '!=', $recommendations->pluck('offer_id')->toArray())
                ->where(function ($q) use ($profile) {
                    $q->where('title', 'like', "%{$profile->desired_jobs}%")
                        ->orWhere('location', 'like', "%{$profile->city}%")
                        ->orWhere('contract_type', $profile->contract_type);
                })
                ->limit(6)
                ->get();
        }

        return view('candidate.dashboard', compact(
            'profile',
            'stats',
            'recommendations',
            'recommendationsAreFallback',
            'allOffers',
            'similarOffers'
        ));
    }
}
