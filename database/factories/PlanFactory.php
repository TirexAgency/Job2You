<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->bothify('plan-####'),
            'price' => fake()->randomElement([0, 15000, 85000]),
            'duration_days' => fake()->randomElement([0, 30, 90]),
            'sms_quota' => fake()->numberBetween(0, 100),
            'cv_parsing_enabled' => fake()->boolean(),
            'active' => true,
        ];
    }

    /**
     * Indicate that the plan is the free formula.
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'free',
            'price' => 0,
            'duration_days' => 0,
            'sms_quota' => 2,
            'cv_parsing_enabled' => false,
            'active' => true,
        ]);
    }

    /**
     * Indicate that the plan is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }
}
