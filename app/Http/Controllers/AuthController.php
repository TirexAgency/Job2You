<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Affiche le formulaire d'inscription.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Traite la demande d'inscription.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        // Les données sont déjà validées par RegisterRequest
        $validated = $request->validated();

        // Création de l'utilisateur
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'candidate', // Rôle par défaut
            'status' => 'active',
            'plan' => 'free',
            'sms_quota' => 2, // 2 SMS gratuits (RG01)
            'sms_sent' => 0,
        ]);

        // Connexion automatique après inscription
        Auth::login($user);

        return redirect()->route('home')
            ->with('success', 'Votre compte a été créé avec succès ! Bienvenue sur Job2You.');
    }
}
