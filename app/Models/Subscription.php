<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'starts_at',
        'ends_at',
        'sms_remaining',
        'sms_reset_at',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'sms_remaining' => 'integer',
        'sms_reset_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le plan.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Relation avec les SMS envoyés sur cet abonnement.
     */
    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class);
    }

    /**
     * Indique si l'abonnement est actuellement actif.
     */
    public function isActive(): bool
    {
        return $this->status === 'active'
            && (! $this->ends_at || $this->ends_at->isFuture());
    }

    /**
     * Nombre de SMS consommés sur le quota de la formule.
     */
    public function smsUsed(): int
    {
        $quota = (int) ($this->plan?->sms_quota ?? 0);

        return max($quota - (int) $this->sms_remaining, 0);
    }

    /**
     * Pourcentage du quota SMS consommé.
     */
    public function smsUsagePercentage(): int
    {
        $quota = (int) ($this->plan?->sms_quota ?? 0);

        if ($quota <= 0) {
            return 0;
        }

        return (int) round(min(max($this->smsUsed() / $quota, 0), 1) * 100);
    }

    /**
     * Nombre de jours restants avant expiration.
     */
    public function daysRemaining(): ?int
    {
        if (! $this->ends_at) {
            return null;
        }

        return $this->ends_at->isFuture()
            ? (int) ceil(now()->diffInDays($this->ends_at, false))
            : 0;
    }
}
