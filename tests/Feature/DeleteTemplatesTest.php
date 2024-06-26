<?php

namespace Tests\Feature;

use App\Staff;
use App\Template;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTemplatesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_route_is_not_accessible_by_guests()
    {
        $this->createTenant();

        $this->assertGuest();

        $this->delete(routeForTenant('templates.destroy', [1]))->assertRedirect(route('login'));
    }

    /** @test */
    public function route_requires_proper_permissions_for_authenticated_users()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();
        $staffTemplate = $staff->createdTemplates()->save(factory(Template::class)->make());

        $user = factory(User::class)->create();
        $userTemplate = $user->createdTemplates()->save(factory(Template::class)->make());

        $this->actingAs($staff)->delete(routeForTenant('templates.destroy', $staffTemplate))->assertStatus(403);
        $this->actingAs($user)->delete(routeForTenant('templates.destroy', $userTemplate))->assertStatus(403);

        $staff->givePermissionTo('templates:delete');
        $user->givePermissionTo('templates:delete');

        $this->actingAs($staff)->delete(routeForTenant('templates.destroy', $staffTemplate))->assertStatus(302);
        $this->actingAs($user)->delete(routeForTenant('templates.destroy', $userTemplate))->assertStatus(302);

        $this->assertCount(0, Template::all());
    }

    /** @test */
    public function users_with_delete_permission_can_only_delete_their_own_templates()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();
        $staffTemplate = $staff->createdTemplates()->save(factory(Template::class)->make());

        $user = factory(User::class)->create();
        $userTemplate = $user->createdTemplates()->save(factory(Template::class)->make());

        $staff->givePermissionTo('templates:delete');
        $user->givePermissionTo('templates:delete');

        // Try to delete each others templates.
        $this->actingAs($user)->delete(routeForTenant('templates.destroy', $staffTemplate))->assertStatus(403);
        $this->actingAs($staff)->delete(routeForTenant('templates.destroy', $userTemplate))->assertStatus(403);

        $this->assertCount(2, Template::all());
    }

    /** @test */
    public function templates_are_soft_deleted_by_default()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();
        $staffTemplate = $staff->createdTemplates()->save(factory(Template::class)->make());

        $staff->givePermissionTo('templates:delete');

        $this->actingAs($staff)->delete(routeForTenant('templates.destroy', $staffTemplate));

        $this->assertCount(0, Template::get());
        $this->assertCount(1, Template::withTrashed()->get());
    }
}
