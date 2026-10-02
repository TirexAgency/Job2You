<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id',
        'external_id',
        'title',
        'company',
        'location',
        'description',
        'contract_type',
        'source_url',
        'published_at',
        'expires_at',
        'fingerprint',
        'status',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function offerSkills(): HasMany
    {
        return $this->hasMany(OfferSkill::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'offer_skills')
            ->withPivot(['required', 'weight']);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(\App\Models\JobMatch::class);
    }

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('published_at', 'desc');
    }
}
