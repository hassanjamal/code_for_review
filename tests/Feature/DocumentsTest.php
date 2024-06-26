<?php

namespace Tests\Feature;

use App\Client;
use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_create_page_is_not_viewable_by_guests()
    {
        $this->createTenant();

        $this->assertTrue(auth()->guest());

        $this->get(routeForTenant('clients.documents.create', ['fake-id']))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function the_create_page_is_viewable_by_authenticated_staff_with_proper_permissions()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->create();
        $client = factory(Client::class)->create(['property_id' => $staff->property_id]);

        $this->actingAs($staff)->get(routeForTenant('clients.documents.create', $client))->assertStatus(404);

        $staff->givePermissionTo(['documents:create', 'clients:view-from-all-properties']);
        $this->actingAs($staff)->get(routeForTenant('clients.documents.create', $client))->assertOk()
             ->assertComponentIs('Documents/Create')->assertPropValue('client', function (
                $propClient
            ) use ($client) {
                 return $propClient['id'] === $client->id;
             });
    }

    // /** @test */
    // public function the_show_page_is_viewable_by_authenticated_users_with_proper_permissions()
    // {
    //     $this->createTenant();
    //     $staff = factory(Staff::class)->state('staff')->create();
    //     $document = factory(Document::class)->create();
    //
    //     $this->actingAs($staff)->get(routeForTenant('documents.show', [$document]))->assertComponentIs('Documents/Show')
    //          ->assertHasProp('document', $document);
    //
    //     $unAuthorizedUser = factory(Staff::class)->create();
    //     $this->actingAs($unAuthorizedUser)->get(routeForTenant('documents.show', [$document]))->assertStatus(403);
    // }
    //
    // /** @test */
    // public function the_edit_page_is_not_viewable_by_guests()
    // {
    //     $this->createTenant();
    //
    //     $this->assertTrue(auth()->guest());
    //     $document = factory(Document::class)->create();
    //
    //     $this->get(routeForTenant('documents.edit', [$document]))->assertRedirect(routeForTenant('login'));
    // }
    //
    // /** @test */
    // public function the_edit_page_is_viewable_by_authenticated_staff()
    // {
    //     $this->createTenant();
    //     $staff = factory(Staff::class)->create();
    //     $staff->givePermissionTo(['documents:create', 'documents:view-all']);
    //
    //     $document = factory(Document::class)->create();
    //
    //     $this->actingAs($staff)->get(routeForTenant('documents.edit', [$document]))->assertOk()
    //          ->assertComponentIs('Documents/Edit');
    // }
}
