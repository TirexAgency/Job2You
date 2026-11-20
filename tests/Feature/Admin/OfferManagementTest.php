<?php

namespace Tests\Feature\Admin;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_list_and_filter_offers(): void
    {
        Offer::factory()->create(['title' => 'Développeur Laravel', 'status' => 'active']);
        Offer::factory()->create(['title' => 'Offre archivée', 'status' => 'archived']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/offers?status=active&search=Laravel');

        $response->assertOk();
        $response->assertViewIs('admin.offers.index');
        $response->assertSee('Développeur Laravel');
        $response->assertDontSee('Offre archivée');
        $response->assertSee('Offres actives');
    }

    public function test_admin_can_moderate_an_offer(): void
    {
        $offer = Offer::factory()->create(['title' => 'À modérer', 'status' => 'active']);

        $response = $this->actingAs($this->admin)
            ->from('/admin/offers')
            ->patch(route('admin.offers.status', $offer), ['status' => 'archived']);

        $response->assertRedirect('/admin/offers');
        $response->assertSessionHas('success', 'Statut de l’offre « À modérer » mis à jour.');
        $this->assertSame('archived', $offer->fresh()->status);
    }

    public function test_offer_moderation_rejects_unknown_status(): void
    {
        $offer = Offer::factory()->create(['status' => 'active']);

        $response = $this->actingAs($this->admin)
            ->from('/admin/offers')
            ->patch(route('admin.offers.status', $offer), ['status' => 'deleted']);

        $response->assertSessionHasErrors('status');
        $this->assertSame('active', $offer->fresh()->status);
    }

    public function test_admin_can_delete_an_offer(): void
    {
        $offer = Offer::factory()->create(['title' => 'À supprimer']);

        $response = $this->actingAs($this->admin)->delete(route('admin.offers.destroy', $offer));

        $response->assertRedirect('/admin/offers');
        $response->assertSessionHas('success', 'Offre « À supprimer » supprimée avec succès.');
        $this->assertDatabaseMissing('offers', ['id' => $offer->id]);
    }

    public function test_non_admin_cannot_moderate_or_delete_offers(): void
    {
        $offer = Offer::factory()->create(['status' => 'active']);
        $candidate = User::factory()->candidate()->create();

        $this->actingAs($candidate)->get('/admin/offers')->assertForbidden();
        $this->actingAs($candidate)->patch(route('admin.offers.status', $offer), ['status' => 'archived'])->assertForbidden();
        $this->actingAs($candidate)->delete(route('admin.offers.destroy', $offer))->assertForbidden();
    }

    public function test_guest_cannot_access_offers_management(): void
    {
        $this->get('/admin/offers')->assertRedirect('/login');
    }
}
