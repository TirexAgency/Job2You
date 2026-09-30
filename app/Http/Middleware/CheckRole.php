<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Vérifie que l'utilisateur a le rôle requis.
     * Utilisation : ->middleware('role:admin')
     * L'admin peut accéder à toutes les routes.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user()) {
            abort(403, 'Accès non autorisé.');
        }

        // L'admin peut accéder à toutes les routes
        if ($request->user()->isAdmin()) {
            return $next($request);
        }

        if ($request->user()->role !== $role) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}
