<?php

namespace Tests\Feature;

use App\Client;
use App\Staff;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function is_not_visible_to_guest()
    {
        $this->createTenant();

        $this->get(routeForTenant('clients.show', [1]))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function is_not_accessible_to_authenticated_users_without_proper_permissions()
    {
        $this->createTenant();
        $user = factory(User::class)->create();
        $client = factory(Client::class)->create();

        $this->actingAs($user)->get(routeForTenant('clients.show', [$client->id]))
            ->assertStatus(404);
    }

    /** @test */
    public function is_not_accessible_to_authenticated_staff_without_proper_permissions()
    {
        $this->createTenant();
        $user = factory(Staff::class)->create();
        $client = factory(Client::class)->create();

        $this->actingAs($user)->get(routeForTenant('clients.show', [$client->id]))
            ->assertStatus(404);
    }


    /** @test */
    public function has_visits_passed_to_the_view_as_props()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->state('staff')->create();

        $client = factory(Client::class)->create(['property_id' => $staff->property_id]);

        $this->actingAs($staff)->get(routeForTenant('clients.show', $client))
            ->assertOk()
            ->assertComponentIs('Clients/Show')
            ->assertHasProp('client')
            ->assertHasProp('alerts')
            ->assertHasProp('visits');
    }
}
