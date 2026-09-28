<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'duration_days',
        'sms_quota',
        'cv_parsing_enabled',
        'active',
    ];

    protected $casts = [
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
}
