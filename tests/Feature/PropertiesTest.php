<?php

namespace Tests\Feature;

use App\PlatformAPI\Mindbody\FakeMindbodyGateway;
use App\PlatformAPI\PlatformGateway;
use App\Property;
use App\Staff;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @see \App\Http\Controllers\PropertiesController */
class PropertiesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        app()->instance(PlatformGateway::class, new FakeMindbodyGateway());
    }

    /** @test */
    public function the_index_page_is_not_viewable_by_guests()
    {
        $this->createTenant();

        $this->assertTrue(auth()->guest());

        $this->get(routeForTenant('properties.index'))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function the_index_page_is_not_viewable_by_authenticated_users_without_proper_permissions()
    {
        $this->createTenant();
        $user = factory(User::class)->create(); // Cannot view properties

        $this->actingAs($user)->get(routeForTenant('properties.index'))
            ->assertStatus(403);
    }

    /** @test */
    public function the_index_page_is_viewable_by_authenticated_users_with_proper_permissions()
    {
        $this->createTenant();
        $user = factory(User::class)->state('super-admin')->create();

        $this->actingAs($user)->get(routeForTenant('properties.index'))->assertOk();
    }

    /** @test */
    public function the_index_page_is_not_viewable_by_authenticated_staff_without_proper_permissions()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->create(); // Has no permissions or roles, cannot view properties.

        $this->actingAs($staff)->get(routeForTenant('properties.index'))->assertStatus(403);
    }

    /** @test */
    public function the_index_page_is_viewable_by_authenticated_staff_with_proper_permissions()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->create();
        $staff->givePermissionTo('properties:view-own');

        $this->actingAs($staff)->get(routeForTenant('properties.index'))->assertOk();
    }

    /** @test */
    public function the_index_page_returns_a_list_of_properties_belonging_to_the_tenant()
    {
        $this->createTenant();
        $user = factory(User::class)->state('super-admin')->create();

        factory(Property::class)->create();

        $this->actingAs($user)->get(routeForTenant('properties.index'))
            ->assertOk()
            ->assertPropValue('properties', Property::all()->toArray())
            ->assertComponentIs('Properties/Index');
    }

    /** @test */
    public function the_index_page_only_returns_properties_a_user_has_permission_to_see()
    {
        $this->createTenant();

        $propertyA = factory(Property::class)->create(['api_identifier' => -99787]);
        $propertyB = factory(Property::class)->create(['api_identifier' => 16134]);

        $staff = factory(Staff::class)->create(['property_id' => $propertyA->id]);
        $staff->givePermissionTo('properties:view-own');

        $this->actingAs($staff)->get(routeForTenant('properties.index'))
            ->assertOk()
            ->assertPropValue('properties', Property::all()->toArray())
            ->assertComponentIs('Properties/Index');
    }

    /** @test */
    public function the_create_page_is_not_viewable_by_guests()
    {
        $this->createTenant();

        $this->assertTrue(auth()->guest());

        $this->get(routeForTenant('properties.create'))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function the_create_page_is_viewable_by_authenticated_users()
    {
        $this->createTenant();
        $user = factory(User::class)->state('super-admin')->create();

        $this->actingAs($user)->get(routeForTenant('properties.create'))
            ->assertOk()
            ->assertComponentIs('Properties/Create');
    }

    /** @test */
    public function the_edit_show_page_is_viewable_by_authenticated_users()
    {
        $this->createTenant();
        $user = factory(User::class)->state('super-admin')->create();
        $property = factory(Property::class)->create();

        $this->actingAs($user)->get(routeForTenant('properties.edit', [$property]))
            ->assertComponentIs('Properties/Edit')
            ->assertHasProp('property', $property);
    }

    /** @test */
    public function when_the_property_api_identifier_is_changed_the_activation_code_is_updated()
    {
        $this->createTenant();

        $user = factory(User::class)->state('super-admin')->create();
        $property = factory(Property::class)
            ->state('not-verified')
            ->create([
                'api_identifier' => -99787,
                'activation_code' => 'initial-code',
                'activation_link' => 'initial-link',
            ]); // Verified properties cannot be changed.
        $originalApiId = $property->api_identifier;

        $this->assertEquals('initial-code', $property->activation_code);
        $this->assertEquals('initial-link', $property->activation_link);

        $this->actingAs($user)
             ->from(routeForTenant('properties.index'))
              ->put(routeForTenant('properties.update', $property), ['name' => $property->name, 'api_identifier' => '16134']);

        $this->assertEquals('code-for:16134', $property->fresh()->activation_code);
        $this->assertEquals('link-for:16134', $property->fresh()->activation_link);
    }

    /** @test */
    public function once_property_ownership_is_verified_a_property_api_identifier_cannot_be_changed()
    {
        $this->createTenant();

        $user = factory(User::class)->state('super-admin')->create();
        $property = factory(Property::class)->create(['api_identifier' => -99787, 'activation_code' => 'initial-code', 'activation_link' => 'initial-link']);

        $this->assertTrue($property->verified);

        $this->actingAs($user)->put(routeForTenant('properties.update', $property), ['name' => 'new name', 'api_identifier' => '16134'])
             ->assertRedirect();

        $this->assertEquals('new name', $property->fresh()->name);
        $this->assertEquals('-99787', $property->fresh()->api_identifier);
    }

    /** @test */
    public function when_the_property_api_identifier_is_changed_the_mb_site_id_is_removed_from_tenant()
    {
        $this->createTenant();

        $user = factory(User::class)->state('super-admin')->create();
        $property = factory(Property::class)->state('not-verified')->create(); // Verified properties cannot be changed.
        $originalApiId = $property->api_identifier;

        $this->assertEquals($property->api_identifier, tenant()->get('mb:'.$originalApiId));


        $this->actingAs($user)->put(routeForTenant('properties.update', $property), ['name' => $property->name, 'api_identifier' => 'new-one'])
            ->assertRedirect();

        $this->assertNull(tenant()->get('mb:'.$originalApiId));
    }

    /** @test */
    public function the_edit_show_page_is_not_viewable_for_properties_that_are_not_verified()
    {
        $this->createTenant();
        $user = factory(User::class)->state('super-admin')->create();
        $property = factory(Property::class)->state('not-verified')->create();

        $this->actingAs($user)
            ->from(routeForTenant('properties.edit', $property))
            ->get(routeForTenant('properties.edit', $property))
            ->assertRedirect(routeForTenant('properties.index'))
            ->assertSessionHas('error', 'This property requires verification to continue.');

        $property->update(['verified_at' => now()]);
        $property->refresh();

        $this->actingAs($user)
            ->get(routeForTenant('properties.edit', [$property]))
            ->assertOk();
    }
}
