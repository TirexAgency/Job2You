<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;

/**
 * Seeder pour les sources d'offres d'emploi.
 * Configure les sources utilisées par le système de scraping.
 */
class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sources Asako (JSON-LD / HTML scraping via AsakoCollector)
        $commonSources = [
            // Asako sources (AsakoCollector - JSON-LD/HTML)
            'rss_asako_mg' => [
                'name' => 'Asako.mg',
                'base_url' => 'https://asako.mg/emploi',
            ],
            'rss_portaljob_mg' => [
                'name' => 'PortalJob Madagascar',
                'base_url' => 'https://portaljob.mg/emploi',
            ],
            // RSS Madagascar sources (RssCollector)
            'rss_emploi_mg' => [
                'name' => 'Emploi.mg RSS',
                'base_url' => 'https://emploi.mg/rss.xml',
            ],
            'rss_madacareers' => [
                'name' => 'MadaCareers RSS',
                'base_url' => 'https://madacareers.com/rss.xml',
            ],
            'rss_jobmada' => [
                'name' => 'JobMada RSS',
                'base_url' => 'https://jobmada.com/rss.xml',
            ],
            'rss_tanitjobs' => [
                'name' => 'TanitJobs RSS',
                'base_url' => 'https://tanitjobs.com/rss.xml',
            ],
            'rss_miarakajobs' => [
                'name' => 'MiarakaJobs RSS',
                'base_url' => 'https://miarakajobs.mg/rss.xml',
            ],
            'rss_fb_mg_it' => [
                'name' => 'FB Madagascar IT RSS',
                'base_url' => 'https://www.facebook.com/feeds/page.php?format=rss20&id=123456789',
            ],
            // Test source
            'api' => [
                'name' => 'API Test',
                'base_url' => 'https://api.example.com/jobs',
                'active' => false,
            ],
        ];

        foreach ($commonSources as $key => $data) {
            Source::updateOrCreate(
                ['collector_key' => $key],
                array_merge($data, [
                    'collector_key' => $key,
                    'active' => true,
                ])
            );
        }

        $this->command?->info('Sources créées/mises à jour : ' . Source::count());
    }
}