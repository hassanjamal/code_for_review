<?php

namespace Tests\Feature;

use App\Client;
use App\Document;
use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreTextDocumentsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTenant();
    }

    /** @test */
    public function the_store_text_document_route_is_note_accessible_to_guests_or_staff_without_create_permissions()
    {
        $validFormParams = $this->validFormParams();

        // Guest Login should redirect.
        $this->post(routeForTenant('clients.text-documents.store', [1]), $validFormParams)
             ->assertRedirect(routeForTenant('login'));

        $staff = factory(Staff::class)->create();
        $client = factory(Client::class)->create();

        // Staff login without permissions should 403.
        $this->actingAs($staff)->post(routeForTenant('clients.text-documents.store', $client), $validFormParams)
             ->assertStatus(403);
    }

    /** @test */
    public function a_text_document_is_created_when_store_route_is_hit_by_staff_with_permission_and_proper_form_fields()
    {
        [$staff, $client] = $this->makeStaffAndClient();
        $staff->givePermissionTo('documents:create');

        // Staff login with permissions should create a document.
        $this->actingAs($staff)->from(routeForTenant('clients.documents.create', $client))
             ->post(routeForTenant('clients.text-documents.store', $client), [
                 'name' => 'test name',
                 'type' => 'text',
                 'content' => 'test content',
             ])->assertRedirect(routeForTenant('documents.show', Document::first()));

        $this->assertDatabaseHas('documents', [
            'client_id' => $client->id,
            'staff_id' => $staff->id,
            'name' => 'test name',
            'type' => 'text',
            'content' => 'test content',
            'key' => null,
            'original_name' => null,
            'content_type' => null,
        ]);
    }

    /** @test */
    public function validation_rules_for_storing_text_documents()
    {
        [$staff, $client] = $this->makeStaffAndClient();
        $staff->givePermissionTo('documents:create');

        // The name field is required and must be a string with max length 255 chars.
        $this->actingAs($staff)->post($this->getStoreRoute($client), $this->validFormParams(['name' => '']))
             ->assertSessionHasErrors('name');

        $this->actingAs($staff)->post($this->getStoreRoute($client), $this->validFormParams(['name' => ['array']]))
             ->assertSessionHasErrors('name');

        $this->actingAs($staff)
             ->post($this->getStoreRoute($client), $this->validFormParams(['name' => Str::random(256)]))
             ->assertSessionHasErrors('name');

        // The content field is nullable and must be a string.
        $this->actingAs($staff)
             ->post($this->getStoreRoute($client), $this->validFormParams(['content' => '']))
             ->assertSessionHasErrors('content');
        $this->actingAs($staff)
             ->post($this->getStoreRoute($client), $this->validFormParams(['content' => ['array']]))
             ->assertSessionHasErrors('content');
    }

    private function validFormParams($overrides = [])
    {
        return [
            'name' => $overrides['name'] ?? 'test name',
            'type' => $overrides['type'] ?? 'file',
            'content' => $overrides['content'] ?? 'test content',
        ];
    }

    protected function makeStaffAndClient()
    {
        return [
            factory(Staff::class)->create(),
            factory(Client::class)->create(),
        ];
    }

    protected function getStoreRoute($client)
    {
        return routeForTenant('clients.text-documents.store', $client);
    }
}
