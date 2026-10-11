<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Offer>
 */
class OfferFactory extends Factory
{
    protected $model = Offer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titles = [
            'Développeur PHP/Laravel',
            'Développeur Full Stack (React/Node)',
            'Ingénieur DevOps',
            'Data Scientist',
            'Chef de Projet Technique',
            'Développeur Frontend (Vue.js)',
            'Architecte Logiciel',
            'Développeur Mobile (Flutter/React Native)',
            'Ingénieur QA / Test Automatisation',
            'Lead Developer',
        ];

        $companies = [
            'TechCorp', 'Innovatech', 'Digital Solutions', 'WebFactory',
            'DataFlow', 'CloudNine', 'SoftEdge', 'CodeCraft',
            'NextGen IT', 'CyberSec',
        ];

        $locations = [
            'Paris', 'Lyon', 'Marseille', 'Toulouse', 'Bordeaux',
            'Lille', 'Nantes', 'Strasbourg', 'Montpellier', 'Rennes',
            'Remote', 'Île-de-France', 'Provence-Alpes-Côte d\'Azur',
        ];

        $contractTypes = ['CDI', 'CDD', 'Freelance', 'Stage', 'Alternance', 'Temps partiel'];

        $title = fake()->randomElement($titles);
        $company = fake()->randomElement($companies);
        $location = fake()->randomElement($locations);
        $contractType = fake()->randomElement($contractTypes);

        $publishedAt = fake()->dateTimeBetween('-60 days', 'now');
        $expiresAt = fake()->optional(0.7)->dateTimeBetween($publishedAt, '+90 days');

        $fingerprint = hash('sha256', implode('|', [
            mb_strtolower($title),
            mb_strtolower($company),
            mb_strtolower($location),
            mb_strtolower($contractType),
        ]));

        // Weighted random for status: active=80%, expired=15%, archived=5%
        $statusRand = fake()->numberBetween(1, 100);
        $status = match (true) {
            $statusRand <= 80 => 'active',
            $statusRand <= 95 => 'expired',
            default => 'archived',
        };

        return [
            'source_id' => Source::factory(),
            'external_id' => fake()->unique()->regexify('[A-Z0-9]{10,20}'),
            'title' => $title,
            'company' => $company,
            'location' => $location,
            'description' => fake()->optional(0.9)->paragraphs(3, true),
            'contract_type' => $contractType,
            'source_url' => 'https://example.com/jobs/'.fake()->uuid(),
            'published_at' => $publishedAt,
            'expires_at' => $expiresAt,
            'fingerprint' => $fingerprint,
            'status' => $status,
        ];
    }

    /**
     * Offre active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'published_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'expires_at' => fake()->optional(0.8)->dateTimeBetween('now', '+60 days'),
        ]);
    }

    /**
     * Offre expirée.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'expires_at' => fake()->dateTimeBetween('-60 days', '-1 day'),
        ]);
    }

    /**
     * Offre archivée.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
            'published_at' => fake()->dateTimeBetween('-1 year', '-6 months'),
        ]);
    }

    /**
     * Offre avec fingerprint spécifique (pour tests de déduplication).
     */
    public function withFingerprint(string $fingerprint): static
    {
        return $this->state(fn (array $attributes) => [
            'fingerprint' => $fingerprint,
        ]);
    }

    /**
     * Offre pour une source spécifique.
     */
    public function forSource(Source $source): static
    {
        return $this->state(fn (array $attributes) => [
            'source_id' => $source->id,
        ]);
    }
}
