<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    protected $model = Skill::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 0;
        $skills = [
            'PHP', 'Laravel', 'Symfony', 'JavaScript', 'TypeScript', 'React', 'Vue.js',
            'Node.js', 'Python', 'Django', 'Java', 'Spring Boot', 'Go', 'Rust',
            'SQL', 'PostgreSQL', 'MySQL', 'MongoDB', 'Redis', 'Docker', 'Kubernetes',
            'AWS', 'GCP', 'Azure', 'Git', 'CI/CD', 'Linux', 'Nginx', 'Apache',
            'GraphQL', 'REST API', 'Microservices', 'Testing', 'TDD', 'Clean Code',
        ];

        $name = $skills[$counter % count($skills)];
        $counter++;

        return [
            'name' => $name,
            'normalized_name' => mb_strtolower($name),
        ];
    }
}