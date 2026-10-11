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
            ['name' => 'Emploi.mg', 'key' => 'rss_emploi_mg', 'url' => 'https://www.emploi.mg/rss/informatique'],
            ['name' => 'Madacareers', 'key' => 'rss_madacareers', 'url' => 'https://www.madacareers.com/rss/it'],
            ['name' => 'Jobmada', 'key' => 'rss_jobmada', 'url' => 'https://www.jobmada.com/rss/tech'],
            ['name' => 'Tanitjobs', 'key' => 'rss_tanitjobs', 'url' => 'https://www.tanitjobs.com/rss/informatique'],
            ['name' => 'MiarakaJobs', 'key' => 'rss_miarakajobs', 'url' => 'https://www.miarakajobs.mg/rss/it'],
            ['name' => 'Facebook Emploi MG IT', 'key' => 'rss_fb_mg_it', 'url' => 'https://www.facebook.com/groups/emploi.madagascar.it/rss'],
            ['name' => 'Asako.mg', 'key' => 'rss_asako_mg', 'url' => 'https://asako.mg/emploi'],
            ['name' => 'PortalJob.mg', 'key' => 'rss_portaljob_mg', 'url' => 'https://portaljob.mg'],
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
     * Source RSS Madagascar active.
     */
    public function rss(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'RSS Test Madagascar',
            'collector_key' => 'rss_emploi_mg_test',
            'base_url' => 'https://www.emploi.mg/rss/informatique',
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