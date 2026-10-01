<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'role',
        'status',
        'plan',
        'sms_quota',
        'sms_sent',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relation avec les abonnements.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            });
    }

    /**
     * Vérifie si l'utilisateur a un abonnement actif.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscriptions()->exists();
    }

    /**
     * Retourne le quota SMS total de l'utilisateur.
     */
    public function getSmsQuota(): int
    {
        $subscriptions = $this->activeSubscriptions;

        if ($subscriptions->isNotEmpty()) {
            return $subscriptions->sum('sms_remaining');
        }

        return $this->sms_quota;
    }

    public function getSmsRemaining(): int
    {
        $subscriptions = $this->activeSubscriptions;

        if ($subscriptions->isNotEmpty()) {
            return $subscriptions->sum('sms_remaining');
        }

        return max($this->sms_quota - $this->sms_sent, 0);
    }

    public function getSmsQuotaLimit(): int
    {
        $subscriptions = $this->activeSubscriptions;

        if ($subscriptions->isNotEmpty()) {
            return $subscriptions->sum(fn (Subscription $subscription) => $subscription->plan?->sms_quota ?? 0);
        }

        return $this->sms_quota;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPremium(): bool
    {
        return $this->plan !== 'free';
    }

    public function hasFreeSmsRemaining(): bool
    {
        return $this->sms_sent < $this->sms_quota;
    }
}
