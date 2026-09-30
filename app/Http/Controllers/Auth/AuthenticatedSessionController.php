<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
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
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        // Message générique pour éviter l'énumération d'emails
        $errorMessage = 'Identifiants incorrects ou compte non vérifié.';

        // Vérifier si l'utilisateur existe et a vérifié son email
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Auth::attempt($credentials, $remember)) {
            return back()->withErrors(['email' => $errorMessage]);
        }

        // Vérifier si l'email est vérifié
        if (! $user->hasVerifiedEmail()) {
            Auth::logout();

            return back()->withErrors(['email' => 'Veuillez vérifier votre email avant de vous connecter.']);
        }

        // Vérifier le statut du compte
        if ($user->status !== 'active') {
            Auth::logout();

            return back()->withErrors(['email' => 'Votre compte n\'est pas actif. Contactez l\'administrateur.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    /**
     * Déconnexion complète.
     *
     * Étapes :
     * 1. Auth::logout() — déconnecte l'utilisateur
     * 2. Session::invalidate() — détruit toutes les données de session
     * 3. Session::regenerateToken() — nouveau token CSRF
     * 4. Suppression du cookie remember_token
     * 5. Redirection vers /login avec message de succès
     */
    public function destroy(Request $request): RedirectResponse
    {
        // 1. Déconnecter l'utilisateur
        Auth::logout();

        // 2. Invalider la session (détruit toutes les données)
        $request->session()->invalidate();

        // 3. Régénérer le token CSRF
        $request->session()->regenerateToken();

        // 4. Supprimer le cookie remember_token
        Cookie::queue(Cookie::forget('remember_web_'.sha1(User::class)));

        // 5. Rediriger vers /login avec message de succès
        return redirect()->route('login')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
