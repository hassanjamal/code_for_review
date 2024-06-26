<?php

namespace Tests\Feature;

use App\Staff;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplatesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_index_page_is_not_viewable_by_guests()
    {
        $this->createTenant();

        $this->assertTrue(auth()->guest());

        $this->get(routeForTenant('templates.index'))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function the_index_page_is_not_viewable_by_authenticated_users_without_proper_permissions()
    {
        $this->createTenant();
        $user = factory(User::class)->create(); // Cannot view templates

        $this->actingAs($user)->get(routeForTenant('templates.index'))->assertStatus(403);
    }

    /** @test */
    public function the_index_page_is_viewable_by_authenticated_users_with_proper_permissions()
    {
        $this->createTenant();
        $user = factory(User::class)->state('super-admin')->create();
        $user->givePermissionTo('templates:create');

        $this->actingAs($user)->get(routeForTenant('templates.index'))->assertOk();
    }

    /** @test */
    public function the_index_page_is_not_viewable_by_authenticated_staff_without_proper_permissions()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->create();

        $this->actingAs($staff)->get(routeForTenant('templates.index'))->assertStatus(403);
    }

    /** @test */
    public function the_index_page_is_viewable_by_authenticated_staff_with_proper_permissions()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->create();
        $staff->givePermissionTo('templates:create');

        $this->actingAs($staff)->get(routeForTenant('templates.index'))->assertOk();
    }
}
