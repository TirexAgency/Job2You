<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DashboardRedirectController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
            new Middleware('verified'),
        ];
    }

    /**
     * Redirige vers le dashboard approprié selon le rôle.
     */
    public function __invoke(): RedirectResponse
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'candidate') {
            return redirect()->route('candidate.dashboard');
        }

        // Fallback pour autres rôles
        return redirect()->route('dashboard')->with('info', 'Tableau de bord non disponible pour ce rôle.');
    }
}