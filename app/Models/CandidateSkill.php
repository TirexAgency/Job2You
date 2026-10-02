<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_profile_id',
        'skill_id',
        'level',
        'weight',
    ];

    protected $casts = [
        'level' => 'integer',
        'weight' => 'decimal:2',
    ];

    public function candidateProfile(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}
