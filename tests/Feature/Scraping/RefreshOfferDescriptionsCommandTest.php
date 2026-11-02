<?php

namespace Tests\Feature\Scraping;

use App\Models\Offer;
use App\Models\Source;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RefreshOfferDescriptionsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_reloads_description_with_sections_and_bullets(): void
    {
        $description = '<p>Introduction du poste.</p>'
            .'<p><strong>Missions principales</strong></p>'
            .'<ul><li><p>Première mission.</p></li><li><p>Deuxième mission.</p></li></ul>'
            .'<p><strong>Profil recherché</strong></p>'
            .'<ul><li><p>Bac+2.</p></li></ul>';

        $jsonLd = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => 'Développeur Laravel',
            'description' => $description,
            'employmentType' => 'FULL_TIME',
            'jobLocation' => [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => 'Ivandry',
                    'addressLocality' => 'Antananarivo',
                    'addressCountry' => 'MG',
                ],
            ],
        ], JSON_UNESCAPED_UNICODE);

        $html = '<!DOCTYPE html><html><head><script type="application/ld+json">'
            .$jsonLd
            .'</script></head><body><script>window.data = {"type_contrat":"CDI"};</script></body></html>';

        Http::preventStrayRequests();
        Http::fake([
            'https://asako.mg/annonces/test' => Http::response($html, 200, ['Content-Type' => 'text/html']),
        ]);

        $source = Source::factory()->create([
            'name' => 'Asako.mg',
            'collector_key' => 'rss_asako_mg',
            'base_url' => 'https://asako.mg/emploi',
            'active' => true,
        ]);

        $offer = Offer::factory()->forSource($source)->create([
            'external_id' => 'test',
            'source_url' => 'https://asako.mg/annonces/test',
            'description' => null,
            'location' => null,
            'contract_type' => null,
        ]);

        $this->artisan('offers:refresh-descriptions', ['source' => (string) $source->id])
            ->expectsOutputToContain('Terminé')
            ->assertExitCode(0);

        $offer = $offer->fresh();

        $this->assertSame(
            "Introduction du poste.\n\n## Missions principales\n- Première mission.\n- Deuxième mission.\n\n## Profil recherché\n- Bac+2.",
            $offer->description
        );
        $this->assertSame('Ivandry, Antananarivo, Madagascar', $offer->location);
        $this->assertSame('CDI', $offer->contract_type);
    }
}
