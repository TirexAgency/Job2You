<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'normalized_name',
    ];

    public function candidateSkills()
    {
        return $this->hasMany(CandidateSkill::class);
    }

    public function offerSkills()
    {
        return $this->hasMany(OfferSkill::class);
    }
}
