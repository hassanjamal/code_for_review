<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_dashboard_route_is_not_accessible_to_guests()
    {
        $this->createTenant();

        $this->get(routeForTenant('dashboard'))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function the_dashboard_route_is_accessible_to_authenticated_users()
    {
        $this->createTenant();
        $user = factory(User::class)->create();

        $this->actingAs($user)->get(routeForTenant('dashboard'))
            ->assertOk()
            ->assertComponentIs('Dashboard/Index');
    }
}
