<?php

namespace App\Models;

use App\Notifications\ResetPasswordCustom;
use App\Notifications\VerifyEmailCustom;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * Envoie la notification de vérification d'email personnalisée.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailCustom);
    }

    /**
     * Envoie la notification de réinitialisation de mot de passe personnalisée.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordCustom($token));
    }

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'photo_path',
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
     * URL publique de la photo de profil, ou null si le fichier est absent.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo_path) {
            return null;
        }

        return Storage::disk('public')->exists($this->photo_path)
            ? Storage::disk('public')->url($this->photo_path)
            : null;
    }

    /**
     * Relation avec les abonnements.
     */
    public function candidateProfile(): HasOne
    {
        return $this->hasOne(CandidateProfile::class);
    }

    public function cvParses(): HasMany
    {
        return $this->hasMany(CvParse::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
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
     * Retourne l'abonnement actif le plus récent avec sa formule.
     */
    public function activeSubscription(): ?Subscription
    {
        return $this->activeSubscriptions()->with('plan')->latest('starts_at')->first();
    }

    /**
     * Retourne la formule active de l'utilisateur.
     */
    public function currentPlan(): ?Plan
    {
        return $this->activeSubscription()?->plan;
    }

    /**
     * Vérifie si la formule active donne accès à une fonctionnalité.
     */
    public function hasPlanFeature(string $feature): bool
    {
        return $this->currentPlan()?->hasFeature($feature) ?? false;
    }

    /**
     * Libellé de la formule actuelle pour l'affichage.
     */
    public function planDisplayName(): string
    {
        $plan = $this->currentPlan();

        if ($plan) {
            return $plan->isFree() ? 'Gratuite' : ucfirst($plan->name);
        }

        return $this->plan === 'free' || ! $this->plan
            ? 'Gratuite'
            : ucfirst((string) $this->plan);
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
        return $this->getSmsRemaining();
    }

    public function getSmsRemaining(): int
    {
        $subscriptions = $this->activeSubscriptions()->with('plan')->get();

        if ($subscriptions->isNotEmpty()) {
            return max($subscriptions->sum(fn (Subscription $subscription) => $subscription->smsRemaining()), 0);
        }

        return max((int) $this->sms_quota - (int) $this->sms_sent, 0);
    }

    public function getSmsQuotaLimit(): int
    {
        $subscriptions = $this->activeSubscriptions()->with('plan')->get();

        if ($subscriptions->isNotEmpty()) {
            return max($subscriptions->sum(fn (Subscription $subscription) => $subscription->smsQuota()), 0);
        }

        return max((int) $this->sms_quota, 0);
    }

    /**
     * Libellés français des rôles.
     *
     * @return array<string, string>
     */
    public static function roleLabels(): array
    {
        return [
            'candidate' => 'Candidat',
            'admin' => 'Administrateur',
            'recruiter' => 'Recruteur',
        ];
    }

    /**
     * Libellé français du rôle de l'utilisateur.
     */
    public function getRoleLabelAttribute(): string
    {
        return self::roleLabels()[$this->role ?? ''] ?? 'Utilisateur';
    }

    /**
     * Libellés français des statuts de compte.
     *
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            'pending' => 'En attente',
            'active' => 'Actif',
            'inactive' => 'Inactif',
            'suspended' => 'Suspendu',
        ];
    }

    /**
     * Libellé français du statut du compte.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status ?? ''] ?? ucfirst((string) $this->status);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCandidate(): bool
    {
        return $this->role === 'candidate';
    }

    public function isPremium(): bool
    {
        $plan = $this->currentPlan();

        return $plan ? ! $plan->isFree() : $this->plan !== 'free';
    }

    public function hasFreeSmsRemaining(): bool
    {
        return $this->getSmsRemaining() > 0;
    }
}
