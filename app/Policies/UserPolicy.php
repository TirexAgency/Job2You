<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Détermine si l'utilisateur peut voir le profil d'un autre utilisateur.
     */
    public function view(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour le profil.
     */
    public function update(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    /**
     * Détermine si l'utilisateur peut supprimer le profil.
     */
    public function delete(User $user, User $model): bool
    {
        // Un admin ne peut pas supprimer son propre compte
        if ($user->isAdmin() && $user->id === $model->id) {
            return false;
        }

        return $user->isAdmin() || $user->id === $model->id;
    }

    /**
     * Détermine si l'utilisateur peut voir la liste de tous les utilisateurs.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Détermine si l'utilisateur peut gérer tous les utilisateurs (admin).
     */
    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }
}
