<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_notification_index_route_is_not_accessible_to_guests()
    {
        $this->createTenant();

        $this->get(routeForTenant('notifications.index'))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function the_notification_index_route_is_accessible_to_authenticated_users()
    {
        $this->createTenant();
        $user = factory(User::class)->create();

        $this->actingAs($user)->get(routeForTenant('notifications.index'))
            ->assertOk()
            ->assertComponentIs('Notifications/Index');
    }
}
