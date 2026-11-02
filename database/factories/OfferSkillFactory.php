<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\OfferSkill;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OfferSkill>
 */
class OfferSkillFactory extends Factory
{
    protected $model = OfferSkill::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'offer_id' => Offer::factory(),
            'skill_id' => Skill::factory(),
            'required' => $this->faker->boolean(70),
            'weight' => $this->faker->randomFloat(2, 0.5, 1.0),
        ];
    }
}