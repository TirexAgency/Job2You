<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Attributs assignables en masse.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'status',
        'plan',
        'sms_quota',
        'sms_sent',
    ];

    /**
     * Attributs à caster.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Vérifie si l'utilisateur est administrateur.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Vérifie si l'utilisateur est abonné (plan payant).
     */
    public function isPremium(): bool
    {
        return $this->plan !== 'free';
    }

    /**
     * Vérifie s'il reste des SMS gratuits (RG01).
     */
    public function hasFreeSmsRemaining(): bool
    {
        return $this->sms_sent < $this->sms_quota;
    }
}
