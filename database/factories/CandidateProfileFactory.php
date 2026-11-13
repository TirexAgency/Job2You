<?php

namespace Database\Factories;

use App\Models\CandidateProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CandidateProfile>
 */
class CandidateProfileFactory extends Factory
{
    protected $model = CandidateProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'desired_jobs' => $this->faker->randomElement([
                'Développeur Full Stack',
                'Développeur Backend',
                'Développeur Frontend',
                'DevOps Engineer',
                'Data Scientist',
                'Product Manager',
            ]),
            'location' => $this->faker->city(),
            'city' => $this->faker->city(),
            'region' => $this->faker->region(),
            'mobility' => $this->faker->randomElement(['local', 'regional', 'national', 'remote']),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'experience_level' => $this->faker->randomElement(['junior', 'mid', 'senior']),
            'education_level' => $this->faker->randomElement(['bac', 'bac+2', 'bac+3', 'bac+5', 'doctorat']),
            'contract_type' => $this->faker->randomElement(['CDI', 'CDD', 'Freelance', 'Stage', 'Alternance', 'any']),
            'desired_salary' => $this->faker->numberBetween(25000, 80000),
            'sectors' => [],
            'alerts_enabled' => $this->faker->boolean(),
            'profile_source' => 'manual',
        ];
    }
}
