<?php

namespace Database\Factories;

use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Source>
 */
class SourceFactory extends Factory
{
    protected $model = Source::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $collectors = [
            ['name' => 'RSS Generic', 'key' => 'rss', 'url' => 'https://example.com/feed.rss'],
            ['name' => 'API Generic', 'key' => 'api', 'url' => 'https://api.example.com/jobs'],
            ['name' => 'France Travail', 'key' => 'api_ft', 'url' => 'https://api.francetravail.io/partenaire/offresdemploi/v2/offres/search'],
            ['name' => 'Indeed RSS', 'key' => 'rss_indeed', 'url' => 'https://rss.indeed.com/rss?q=developer&l=Paris'],
            ['name' => 'LinkedIn RSS', 'key' => 'rss_linkedin', 'url' => 'https://www.linkedin.com/jobs/search/rss?keywords=developer&location=France'],
        ];

        $collector = fake()->randomElement($collectors);
        $uniqueSuffix = fake()->unique()->bothify('????##');

        return [
            'name' => $collector['name'].' '.$uniqueSuffix,
            'base_url' => $collector['url'],
            'collector_key' => $collector['key'].'_'.$uniqueSuffix,
            'last_run_at' => fake()->optional(0.7)->dateTimeBetween('-30 days', 'now'),
            'active' => fake()->boolean(80),
        ];
    }

    /**
     * Source RSS active.
     */
    public function rss(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'RSS Test',
            'collector_key' => 'rss_test',
            'base_url' => 'https://example.com/feed.rss',
            'active' => true,
        ]);
    }

    /**
     * Source API active.
     */
    public function api(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'API Test',
            'collector_key' => 'api_test',
            'base_url' => 'https://api.example.com/jobs',
            'active' => true,
        ]);
    }

    /**
     * Source inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }
}
