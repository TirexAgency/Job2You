<?php

namespace Tests\Feature\Scraping;

use App\Models\Source;
use App\Services\Scraping\Collectors\AsakoCollector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AsakoCollectorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_enriches_location_contract_and_sections_from_the_detail_page(): void
    {
        $detailUrl = 'https://asako.mg/annonces/administrateur-achats-550f5ccb';

        $listingJobPosting = [
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'identifier' => ['@type' => 'PropertyValue', 'value' => '550f5ccb'],
            'title' => 'Administrateur Achats',
            'hiringOrganization' => ['@type' => 'Organization', 'name' => 'LearnProgress'],
            'url' => $detailUrl,
        ];

        $detailJobPosting = array_merge($listingJobPosting, [
            'description' => '<p>Introduction.</p><p><strong>Formation</strong>'."\u{00A0}".'</p><ul><li>Bac+2.</li></ul>',
            'employmentType' => 'FULL_TIME',
            'jobLocation' => [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => 'Ivandry',
                    'addressLocality' => 'Antananarivo',
                    'addressRegion' => 'Analamanga',
                    'addressCountry' => 'MG',
                ],
            ],
        ]);

        Http::fake([
            'https://asako.mg/emploi' => Http::response($this->htmlWithJsonLd($listingJobPosting)),
            'https://asako.mg/emploi?page=2' => Http::response('<html></html>'),
            $detailUrl => Http::response(
                $this->htmlWithJsonLd($detailJobPosting)
                .'<script>window.data = {"type_contrat":"CDI"};</script>'
            ),
        ]);

        $source = Source::factory()->create([
            'collector_key' => AsakoCollector::getCollectorKey(),
            'base_url' => 'https://asako.mg/emploi',
            'active' => true,
        ]);

        $offers = (new AsakoCollector)->collect($source);

        $this->assertCount(1, $offers);
        $this->assertSame('Ivandry, Antananarivo, Madagascar', $offers[0]->location);
        $this->assertSame('CDI', $offers[0]->contractType);
        $this->assertSame("Introduction.\n\n## Formation\n- Bac+2.", $offers[0]->description);
    }

    private function htmlWithJsonLd(array $data): string
    {
        return '<html><head><script type="application/ld+json">'
            .json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            .'</script></head><body></body></html>';
    }
}
