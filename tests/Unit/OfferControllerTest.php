<?php

namespace Tests\Unit;

use App\Models\Offer;
use App\Models\Source;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OfferControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_returns_paginated_offers(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);
        Offer::factory()->count(15)->active()->forSource($source)->create();

        $response = $this->get(route('offers.index'));

        $response->assertStatus(200);
        $response->assertViewIs('offers.index');
        $response->assertViewHas('offers');
        $this->assertEquals(12, $response->viewData('offers')->count());
    }

    #[Test]
    public function index_filters_by_search_keyword(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);
        Offer::factory()->active()->forSource($source)->create(['title' => 'Developpeur Laravel']);
        Offer::factory()->active()->forSource($source)->create(['title' => 'Designer UI/UX']);
        Offer::factory()->active()->forSource($source)->create(['title' => 'Chef de projet']);

        $response = $this->get(route('offers.index', ['search' => 'Laravel']));

        $response->assertStatus(200);
        $this->assertEquals(1, $response->viewData('offers')->count());
        $this->assertStringContainsString('Laravel', $response->viewData('offers')->first()->title);
    }

    #[Test]
    public function index_filters_by_location(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);
        Offer::factory()->active()->forSource($source)->create(['location' => 'Paris']);
        Offer::factory()->active()->forSource($source)->create(['location' => 'Lyon']);
        Offer::factory()->active()->forSource($source)->create(['location' => 'Marseille']);

        $response = $this->get(route('offers.index', ['location' => 'Paris']));

        $response->assertStatus(200);
        $this->assertEquals(1, $response->viewData('offers')->count());
        $this->assertStringContainsString('Paris', $response->viewData('offers')->first()->location);
    }

    #[Test]
    public function index_filters_by_contract_type(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);
        Offer::factory()->active()->forSource($source)->create(['contract_type' => 'CDI']);
        Offer::factory()->active()->forSource($source)->create(['contract_type' => 'CDD']);
        Offer::factory()->active()->forSource($source)->create(['contract_type' => 'Freelance']);

        $response = $this->get(route('offers.index', ['contract_type' => 'CDI']));

        $response->assertStatus(200);
        $this->assertEquals(1, $response->viewData('offers')->count());
        $this->assertEquals('CDI', $response->viewData('offers')->first()->contract_type);
    }

    #[Test]
    public function index_filters_by_date_range(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);
        Offer::factory()->active()->forSource($source)->create(['published_at' => '2026-01-15']);
        Offer::factory()->active()->forSource($source)->create(['published_at' => '2026-02-15']);
        Offer::factory()->active()->forSource($source)->create(['published_at' => '2026-03-15']);

        $response = $this->get(route('offers.index', [
            'date_from' => '2026-02-01',
            'date_to' => '2026-02-28',
        ]));

        $response->assertStatus(200);
        $this->assertEquals(1, $response->viewData('offers')->count());
        $this->assertEquals('2026-02-15', $response->viewData('offers')->first()->published_at->format('Y-m-d'));
    }

    #[Test]
    public function index_returns_contract_types_for_filter(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);
        Offer::factory()->active()->forSource($source)->create(['contract_type' => 'CDI']);
        Offer::factory()->active()->forSource($source)->create(['contract_type' => 'CDD']);
        Offer::factory()->active()->forSource($source)->create(['contract_type' => 'CDI']);

        $response = $this->get(route('offers.index'));

        $response->assertStatus(200);
        $contractTypes = $response->viewData('contractTypes');
        $this->assertCount(2, $contractTypes);
        $this->assertContains('CDI', $contractTypes->toArray());
        $this->assertContains('CDD', $contractTypes->toArray());
    }

    #[Test]
    public function search_endpoint_returns_json(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);
        Offer::factory()->count(5)->active()->forSource($source)->create();

        $response = $this->getJson(route('offers.search'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'offers' => [
                '*' => ['id', 'title', 'company', 'location', 'contract_type', 'published_at']
            ],
            'pagination' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
                'has_more_pages',
            ],
            'contract_types',
        ]);
    }

    #[Test]
    public function search_endpoint_filters_by_search(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);
        Offer::factory()->active()->forSource($source)->create(['title' => 'Developpeur Laravel']);
        Offer::factory()->active()->forSource($source)->create(['title' => 'Designer UI/UX']);

        $response = $this->getJson(route('offers.search', ['search' => 'Laravel']));

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('offers')));
        $this->assertStringContainsString('Laravel', $response->json('offers.0.title'));
    }

    #[Test]
    public function search_endpoint_paginates_results(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);
        Offer::factory()->count(25)->active()->forSource($source)->create();

        $response = $this->getJson(route('offers.search', ['page' => 1]));

        $response->assertStatus(200);
        $this->assertEquals(12, count($response->json('offers')));
        $this->assertEquals(1, $response->json('pagination.current_page'));
        $this->assertEquals(3, $response->json('pagination.last_page'));
        $this->assertTrue($response->json('pagination.has_more_pages'));

        $response2 = $this->getJson(route('offers.search', ['page' => 2]));
        $this->assertEquals(2, $response2->json('pagination.current_page'));
    }

    #[Test]
    public function show_returns_offer_with_skills_and_similar(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);
        $skill = Skill::factory()->create(['name' => 'Laravel']);

        $offer = Offer::factory()->active()->forSource($source)->create();
        // OfferSkill n'a pas de timestamps, utiliser insert direct
        DB::table('offer_skills')->insert([
            'offer_id' => $offer->id,
            'skill_id' => $skill->id,
            'required' => true,
            'weight' => 1.0,
        ]);

        // Offres similaires
        Offer::factory()->active()->forSource($source)->create(['title' => 'Autre offre Laravel', 'company' => $offer->company]);

        $response = $this->get(route('offers.show', $offer));

        $response->assertStatus(200);
        $response->assertViewIs('offers.show');
        $response->assertViewHas('offer');
        $response->assertViewHas('similarOffers');
        $this->assertEquals(1, $response->viewData('offer')->offerSkills->count());
    }

    #[Test]
    public function show_renders_description_in_a_modal_with_sections(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);
        $offer = Offer::factory()->active()->forSource($source)->create([
            'description' => "Introduction du poste.\n\n## Missions principales\n- Mission 1\n- Mission 2",
        ]);

        $response = $this->get(route('offers.show', $offer));

        $response->assertOk();
        $response->assertSee('Voir la description complète');
        $response->assertSee('offerDescriptionModal', false);
        $response->assertSee('<h3 class="offer-description-heading">Missions principales</h3>', false);
        $response->assertSee('<li>Mission 1</li>', false);
        $response->assertSee('<li>Mission 2</li>', false);
    }

    #[Test]
    public function only_active_offers_are_returned(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);
        Offer::factory()->active()->forSource($source)->create();
        Offer::factory()->expired()->forSource($source)->create();
        Offer::factory()->archived()->forSource($source)->create();

        $response = $this->get(route('offers.index'));

        $response->assertStatus(200);
        $this->assertEquals(1, $response->viewData('offers')->count());
    }

    #[Test]
    public function search_with_sort_orders_results(): void
    {
        $source = Source::factory()->create(['collector_key' => 'rss']);
        Offer::factory()->active()->forSource($source)->create(['title' => 'Alpha', 'published_at' => '2026-01-01']);
        Offer::factory()->active()->forSource($source)->create(['title' => 'Beta', 'published_at' => '2026-01-02']);
        Offer::factory()->active()->forSource($source)->create(['title' => 'Gamma', 'published_at' => '2026-01-03']);

        $response = $this->getJson(route('offers.search', ['sort' => 'title_asc']));
        $titles = array_column($response->json('offers'), 'title');
        $this->assertEquals(['Alpha', 'Beta', 'Gamma'], $titles);

        $response = $this->getJson(route('offers.search', ['sort' => 'title_desc']));
        $titles = array_column($response->json('offers'), 'title');
        $this->assertEquals(['Gamma', 'Beta', 'Alpha'], $titles);
    }
}