<?php

namespace Tests\Feature;

use App\Staff;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateTemplatesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_create_page_is_not_viewable_by_guests()
    {
        $this->createTenant();

        $this->assertTrue(auth()->guest());

        $this->get(routeForTenant('templates.create'))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function the_create_page_is_viewable_by_authenticated_users_with_proper_permissions()
    {
        $this->createTenant();

        $user = factory(User::class)->create();
        $staff = factory(Staff::class)->create();

        // No permissions to access page granted.
        $this->actingAs($user)->get(routeForTenant('templates.create'))->assertStatus(403);

        $this->actingAs($staff)->get(routeForTenant('templates.create'))->assertStatus(403);

        // Now authorize the users to access the page.
        $user->givePermissionTo('templates:create');
        $staff->givePermissionTo('templates:create');

        $this->actingAs($user)->get(routeForTenant('templates.create'))->assertOk()->assertComponentIs('Templates/Create');

        $this->actingAs($staff)->get(routeForTenant('templates.create'))->assertOk()->assertComponentIs('Templates/Create');
    }

    /** @test */
    public function the_store_route_is_not_available_to_guests()
    {
        $this->createTenant();

        $this->assertTrue(auth()->guest());

        $this->get(routeForTenant('templates.store', ['content' => 'foo']))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function the_store_route_is_available_to_staff_with_permissions()
    {
        $this->createTenant();

        // No permissions
        $staff = factory(Staff::class)->create();
        $this->actingAs($staff)->post(routeForTenant('templates.store', []))->assertStatus(403);

        // Correct permissions.
        $staff->givePermissionTo('templates:create');
        $this->actingAs($staff)->from(routeForTenant('templates.create'))->post(routeForTenant('templates.store', [
            'name' => 'template name',
            'content' => 'foo',
        ]))->assertRedirect(routeForTenant('templates.index'));
    }

    /** @test */
    public function a_template_is_created_by_the_auth_user_if_all_is_good()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();
        $staff->givePermissionTo('templates:create');

        $this->actingAs($staff)->post(routeForTenant('templates.store', [
            'name' => 'testing',
            'default_short_name' => 'short name',
            'default_group_name' => 'group',
            'content' => "<a href='#'>hello world</a>",
        ]));

        $this->assertDatabaseHas('templates', [
            'creator_id' => $staff->id,
            'creator_type' => $staff->getMorphClass(),
            'default_short_name' => 'short name',
            'default_group_name' => 'group',
            'name' => 'testing',
            'content' => "<a href='#'>hello world</a>",
        ]);
    }

    /** @test */
    public function the_content_field_is_required_and_must_be_a_string()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();
        $staff->givePermissionTo('templates:create');

        $this->actingAs($staff)->post(routeForTenant('templates.store', [
            'name' => 'testing',
            'content' => null,
        ]))->assertSessionHasErrors('content');

        $this->actingAs($staff)->post(routeForTenant('templates.store', [
            'name' => 'testing',
            'content' => ['not a string'],
        ]))->assertSessionHasErrors('content');
    }

    /** @test */
    public function the_name_field_is_required_and_must_be_a_string()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();
        $staff->givePermissionTo('templates:create');

        $this->actingAs($staff)->post(routeForTenant('templates.store', [
            'name' => null,
            'content' => 'content',
        ]))->assertSessionHasErrors('name');

        $this->actingAs($staff)->post(routeForTenant('templates.store', [
            'name' => ['not a string'],
            'content' => 'content',
        ]))->assertSessionHasErrors('name');
    }

    /** @test */
    public function the_short_name_field_must_be_a_string_and_limited_to_15_chars()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();
        $staff->givePermissionTo('templates:create');

        $this->actingAs($staff)->post(routeForTenant('templates.store', [
            'name' => 'name',
            'content' => 'content',
            'default_short_name' => Str::random(16),
        ]))->assertSessionHasErrors('default_short_name');

        $this->actingAs($staff)->post(routeForTenant('templates.store', [
            'name' => 'name',
            'content' => 'content',
            'default_short_name' => ['foo'],
        ]))->assertSessionHasErrors('default_short_name');
    }

    /** @test */
    public function the_group_name_field_must_be_a_string_and_limited_to_15_chars()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();
        $staff->givePermissionTo('templates:create');

        $this->actingAs($staff)->post(routeForTenant('templates.store', [
            'name' => 'name',
            'content' => 'content',
            'default_group_name' => Str::random(16), // Must be 15 chars or less.
        ]))->assertSessionHasErrors('default_group_name');

        $this->actingAs($staff)->post(routeForTenant('templates.store', [
            'name' => 'name',
            'content' => 'content',
            'default_group_name' => ['foo'], // Must be a string.
        ]))->assertSessionHasErrors('default_group_name');
    }
}
