<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationPromptController extends Controller
{
    /**
     * Affiche la page de vérification email personnalisée.
     */
    public function __invoke(Request $request): RedirectResponse|\Illuminate\View\View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('home'))
                    : view('auth.verify-email');
    }
}
