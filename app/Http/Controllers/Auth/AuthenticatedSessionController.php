<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Message générique pour éviter l'énumération d'emails
        $errorMessage = 'Identifiants incorrects ou compte non vérifié.';

        // Vérifier si l'utilisateur existe et a vérifié son email
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if (!$user || !Auth::attempt($credentials)) {
            return back()->withErrors(['email' => $errorMessage]);
        }

        // Vérifier si l'email est vérifié
        if (!$user->hasVerifiedEmail()) {
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
     * Déconnexion.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
