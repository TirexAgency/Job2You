<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'skill_id',
        'required',
        'weight',
    ];

    protected $casts = [
        'required' => 'boolean',
        'weight' => 'decimal:2',
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}
