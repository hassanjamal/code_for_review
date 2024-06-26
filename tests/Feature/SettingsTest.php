<?php

namespace Tests\Feature;

use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_settings_route_is_not_accessible_to_guests()
    {
        $this->createTenant();

        $this->get(routeForTenant('settings'))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function the_settings_route_is_accessible_to_authenticated_users()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->create();

        $this->actingAs($staff)->get(routeForTenant('settings'))
            ->assertOk();
    }
}
