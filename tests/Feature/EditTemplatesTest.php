<?php

namespace Tests\Feature;

use App\Staff;
use App\Template;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class EditTemplatesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_edit_page_is_not_viewable_by_guests()
    {
        $this->createTenant();

        $this->assertGuest();

        $this->get(routeForTenant('templates.edit', 1))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function the_edit_page_is_viewable_by_authenticated_users_with_proper_permissions()
    {
        $this->createTenant();

        $user = factory(User::class)->create();
        $userTemplate = $user->createdTemplates()->create(['name' => 'name', 'content' => 'content']);

        $staff = factory(Staff::class)->create();
        $staffTemplate = $staff->createdTemplates()->create(['name' => 'name', 'content' => 'content']);

        // No permissions to access page granted.
        $this->actingAs($user)->get(routeForTenant('templates.edit', $userTemplate))->assertStatus(403);

        $this->actingAs($staff)->get(routeForTenant('templates.edit', $staffTemplate))->assertStatus(403);

        // Now authorize the users to access the page.
        $user->givePermissionTo('templates:create');
        $staff->givePermissionTo('templates:create');

        $this->actingAs($user)->get(routeForTenant('templates.edit', $userTemplate))->assertOk()->assertComponentIs('Templates/Edit')->assertHasProp('template');

        $this->actingAs($staff)->get(routeForTenant('templates.edit', $staffTemplate))->assertOk()->assertComponentIs('Templates/Edit')->assertHasProp('template');
    }

    /** @test */
    public function the_update_route_is_not_available_to_guests()
    {
        $this->createTenant();

        $this->assertTrue(auth()->guest());

        $this->put(routeForTenant('templates.update', 1))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function the_update_route_is_available_to_staff_with_permissions()
    {
        $this->createTenant();

        $user = factory(User::class)->create();
        $userTemplate = $user->createdTemplates()->create([
            'name' => 'name user',
            'content' => 'content user',
            'default_short_name' => 'short-name-u',
            'default_group_name' => 'group-name-u',
        ]);

        $staff = factory(Staff::class)->create();
        $staffTemplate = $staff->createdTemplates()->create([
            'name' => 'name staff',
            'content' => 'content staff',
            'default_short_name' => 'short-name-s',
            'default_group_name' => 'group-name-s',
        ]);

        // No permissions to access page granted.
        $this->actingAs($user)->put(routeForTenant('templates.update', $userTemplate))->assertStatus(403);
        $this->actingAs($staff)->put(routeForTenant('templates.update', $staffTemplate))->assertStatus(403);

        // Now authorize the users to access the page.
        $user->givePermissionTo('templates:create');
        $staff->givePermissionTo('templates:create');

        $this->actingAs($user)
            ->from(routeForTenant('templates.edit', $userTemplate))
            ->put(routeForTenant('templates.update', $userTemplate), [
                'name' => 'updated name user',
                'content' => 'updated content user',
                'default_short_name' => 'updated-sn-u',
                'default_group_name' => 'updated-gn-u',
            ])->assertRedirect(routeForTenant('templates.index'));

        $this->actingAs($staff)
            ->from(routeForTenant('templates.edit', $staffTemplate))
            ->put(routeForTenant('templates.update', $staffTemplate), [
                'name' => 'updated name staff',
                'content' => 'updated content staff',
                'default_short_name' => 'updated-sn-s',
                'default_group_name' => 'updated-gn-s',
            ])->assertRedirect(routeForTenant('templates.index'));

        tap($userTemplate->fresh(), function ($template) {
            $this->assertEquals('updated name user', $template->name);
            $this->assertEquals('updated content user', $template->content);
            $this->assertEquals('updated-sn-u', $template->default_short_name);
            $this->assertEquals('UPDATED-GN-U', $template->default_group_name);
        });

        tap($staffTemplate->fresh(), function ($template) {
            $this->assertEquals('updated name staff', $template->name);
            $this->assertEquals('updated content staff', $template->content);
            $this->assertEquals('updated-sn-s', $template->default_short_name);
            $this->assertEquals('UPDATED-GN-S', $template->default_group_name);
        });
    }

    /** @test */
    public function the_content_field_is_required_and_must_be_a_string()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();
        $template = $staff->createdTemplates()->create(factory(Template::class)->make()->toArray());

        $staff->givePermissionTo('templates:create');

        $this->actingAs($staff)->put(routeForTenant('templates.update', $template), [
            'name' => 'testing,',
            'content' => null,
        ])->assertSessionHasErrors('content');

        $this->actingAs($staff)->put(routeForTenant('templates.update', $template), [
            'name' => 'testing',
            'content' => ['not a string'],
        ])->assertSessionHasErrors('content');
    }

    /** @test */
    public function the_name_field_is_required_and_must_be_a_string()
    {
        $this->createTenant();

        [$staff, $template] = $this->createStaffWithTemplate();

        $this->actingAs($staff)->put(routeForTenant('templates.update', $template), [
            'name' => null,
            'content' => 'testing',
        ])->assertSessionHasErrors('name');

        $this->actingAs($staff)->put(routeForTenant('templates.update', $template), [
            'name' => ['not a string'],
            'content' => 'testing',
        ])->assertSessionHasErrors('name');
    }

    /** @test */
    public function the_short_name_field_must_be_a_string_and_limited_to_15_chars()
    {
        $this->createTenant();

        [$staff, $template] = $this->createStaffWithTemplate();

        $this->actingAs($staff)->put(routeForTenant('templates.update', $template), [
            'name' => 'name',
            'content' => 'content',
            'default_short_name' => Str::random(16),
        ])->assertSessionHasErrors('default_short_name');

        $this->actingAs($staff)->put(routeForTenant('templates.store', [
            'name' => 'name',
            'content' => 'content',
            'default_short_name' => ['foo'],
        ]))->assertSessionHasErrors('default_short_name');
    }

    /** @test */
    public function the_group_name_field_must_be_a_string_and_limited_to_15_chars()
    {
        $this->createTenant();

        [$staff, $template] = $this->createStaffWithTemplate();

        $this->actingAs($staff)->put(routeForTenant('templates.update', $template), [
            'name' => 'name',
            'content' => 'content',
            'default_group_name' => Str::random(16), // Must be 15 chars or less.
        ])->assertSessionHasErrors('default_group_name');

        $this->actingAs($staff)->put(routeForTenant('templates.store', [
            'name' => 'name',
            'content' => 'content',
            'default_group_name' => ['foo'], // Must be a string.
        ]))->assertSessionHasErrors('default_group_name');
    }

    /** @test */
    public function cannot_update_the_templates_created_by_another_user()
    {
        $this->createTenant();

        list($staff, $staffTemplate) = $this->createStaffWithTemplate();
        list($otherStaff, $otherStaffTemplate) = $this->createStaffWithTemplate();

        $this->actingAs($otherStaff)->put(routeForTenant('templates.update', $staffTemplate), [
            'name' => 'new name',
            'content' => 'new content',
        ])->assertStatus(403);

        $this->assertDatabaseHas('templates', [
            'name' => $staffTemplate->name,
            'content' => $staffTemplate->content,
        ]);
    }

    protected function createStaffWithTemplate(): array
    {
        $staff = factory(Staff::class)->create();
        $staff->givePermissionTo('templates:create');
        $template = $staff->createdTemplates()->create(factory(Template::class)->make()->toArray());

        return [$staff, $template];
    }
}
