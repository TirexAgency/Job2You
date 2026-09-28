<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        return true; // Tout le monde peut s'inscrire
    }

    /**
     * Règles de validation pour l'inscription.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => [
                'required',
                'string',
                'unique:users,phone',
                // Format Madagascar : +261 34 12 345 67 ou 034 12 345 67
                'regex:/^(?:(?:\+|00)261|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()      // Majuscule + minuscule
                    ->numbers()         // Au moins un chiffre
                    ->symbols(),        // Au moins un symbole
            ],
        ];
    }

    /**
     * Messages d'erreur personnalisés en français.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'phone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'phone.regex' => 'Le numéro de téléphone n\'est pas valide (format Madagascar attendu : +261 34 12 345 67 ou 034 12 345 67).',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.mixed_case' => 'Le mot de passe doit contenir au moins une majuscule et une minuscule.',
            'password.numbers' => 'Le mot de passe doit contenir au moins un chiffre.',
            'password.symbols' => 'Le mot de passe doit contenir au moins un symbole.',
        ];
    }
}
