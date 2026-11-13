<?php

namespace App\Http\Middleware;

use App\Models\Plan;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vérifie que la formule active de l'utilisateur donne accès à une fonctionnalité.
 */
class EnsurePlanFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Accès non autorisé.');
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        if (! $user->hasPlanFeature($feature)) {
            abort(403, 'Votre formule actuelle ne donne pas accès à '.$this->featureLabel($feature).'.');
        }

        return $next($request);
    }

    /**
     * Retourne un libellé lisible pour une fonctionnalité.
     */
    private function featureLabel(string $feature): string
    {
        return match ($feature) {
            Plan::FEATURE_CV_PARSING => 'l’analyse de CV',
            Plan::FEATURE_SMS_ALERTS => 'les alertes SMS',
            default => 'cette fonctionnalité',
        };
    }
}
