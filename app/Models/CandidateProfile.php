<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CandidateProfile extends Model
{
    use HasFactory;

    protected $table = 'candidate_profile';

    protected $fillable = [
        'user_id',
        'desired_jobs',
        'location',
        'city',
        'region',
        'mobility',
        'latitude',
        'longitude',
        'experience_level',
        'education_level',
        'contract_preferences',
        'contract_type',
        'desired_salary',
        'sectors',
        'alerts_enabled',
        'profile_source',
    ];

    protected $casts = [
        'sectors' => 'array',
        'alerts_enabled' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'desired_salary' => 'integer',
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

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class)->orderByDesc('start_date');
    }

    public function educations(): HasMany
    {
        return $this->hasMany(Education::class)->orderByDesc('start_date');
    }

    /**
     * Calcule le pourcentage de complétion du profil.
     */
    public function completionPercentage(): int
    {
        $fields = [
            'desired_jobs',
            'city',
            'mobility',
            'experience_level',
            'education_level',
            'contract_type',
            'desired_salary',
        ];

        $filled = 0;
        foreach ($fields as $field) {
            if (! empty($this->$field)) {
                $filled++;
            }
        }

        // Compétences
        if ($this->skills()->exists()) {
            $filled++;
        }

        // Expériences
        if ($this->experiences()->exists()) {
            $filled++;
        }

        // Formations
        if ($this->educations()->exists()) {
            $filled++;
        }

        $total = count($fields) + 3; // +3 pour skills, experiences, educations

        return (int) round(($filled / $total) * 100);
    }
}
