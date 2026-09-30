<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Affiche le formulaire de connexion.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Traite la tentative de connexion.
     * Protection contre l'énumération d'emails : message générique.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Déconnexion complète.
     *
     * Étapes :
     * 1. Auth::logout() — déconnecte l'utilisateur
     * 2. Session::invalidate() — détruit toutes les données de session
     * 3. Session::regenerateToken() — nouveau token CSRF
     * 4. Le guard invalide le cookie et renouvelle le remember_token.
     * 5. Redirection vers /login avec message de succès.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // 1. Déconnecter l'utilisateur
        Auth::logout();

        // 2. Invalider la session (détruit toutes les données)
        $request->session()->invalidate();

        // 3. Régénérer le token CSRF
        $request->session()->regenerateToken();

        // Le guard Laravel supprime le cookie recaller et renouvelle le remember_token.
        return redirect()->route('login')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
