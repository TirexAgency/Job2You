<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CandidateProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'desired_jobs',
        'location',
        'experience_level',
        'education_level',
        'contract_preferences',
        'profile_source',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function candidateSkills(): HasMany
    {
        return $this->hasMany(CandidateSkill::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'candidate_skill')
            ->withPivot(['level', 'weight']);
    }
}
