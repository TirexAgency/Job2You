<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            return User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => 'candidate',
                'status' => 'pending',
                'plan' => 'free',
                'sms_quota' => 2,
                'sms_sent' => 0,
            ]);
        });

        Auth::login($user);

        return redirect()->route('verification.notice')
            ->with('success', 'Votre compte a été créé ! Vérifiez votre email pour activer votre compte.');
    }

    /**
     * Vérifie l'email de l'utilisateur.
     */
    public function verifyEmail(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Vérifier si l'email est déjà vérifié
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home')
                ->with('info', 'Votre email est déjà vérifié.');
        }

        // Marquer l'email comme vérifié
        $user->markEmailAsVerified();

        // Mise à jour du statut de pending à active
        $user->status = 'active';
        $user->save();

        return redirect()->route('home')
            ->with('success', 'Votre email a été vérifié avec succès ! Bienvenue sur Job2You.');
    }
}
