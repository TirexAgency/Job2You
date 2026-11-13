<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    public const FEATURE_SMS_ALERTS = 'sms_alerts';

    public const FEATURE_CV_PARSING = 'cv_parsing';

    protected $fillable = [
        'name',
        'price',
        'duration_days',
        'sms_quota',
        'cv_parsing_enabled',
        'active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
        'cv_parsing_enabled' => 'boolean',
    ];

    /**
     * Relation avec les abonnements.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Relation avec les paiements.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Indique si le plan est gratuit.
     */
    public function isFree(): bool
    {
        return (float) $this->price <= 0;
    }

    /**
     * Indique si la formule donne accès à une fonctionnalité.
     */
    public function hasFeature(string $feature): bool
    {
        return match ($feature) {
            self::FEATURE_SMS_ALERTS => (int) $this->sms_quota > 0,
            self::FEATURE_CV_PARSING => (bool) $this->cv_parsing_enabled,
            default => false,
        };
    }

    /**
     * Liste des avantages affichés sur les pages publiques et candidat.
     *
     * @return array<int, string>
     */
    public function featureList(): array
    {
        $features = [
            'Accès à toutes les offres d’emploi',
            'Recommandations personnalisées',
        ];

        $features[] = $this->sms_quota > 0
            ? $this->sms_quota.' alertes SMS par période'
            : 'Aucune alerte SMS incluse';

        if ($this->cv_parsing_enabled) {
            $features[] = 'Analyse de CV par IA';
        }

        if (! $this->isFree()) {
            $features[] = 'Recommandations prioritaires';
            $features[] = 'Support prioritaire';
        }

        return $features;
    }

    /**
     * Filtre les formules actives.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }
}
