<?php

namespace Tests\Feature;

use App\Location;
use App\PlatformAPI\Mindbody\FakeMindbodyGateway;
use App\PlatformAPI\PlatformGateway;
use App\Property;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @see \App\Http\Controllers\VerifyMindbodySiteOwnershipController */
class VerifyMindbodyApiConnectionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        app()->instance(PlatformGateway::class, new FakeMindbodyGateway);
    }
    /** @test */
    public function the_route_is_not_accessible_to_guests()
    {
        $this->createTenant();

        $this->post(routeForTenant('mindbody.verify-ownership'), ['api_identifier' => '-99787'])
            ->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function can_determine_that_a_connected_api_property_is_verified()
    {
        $this->withoutExceptionHandling();
        $this->createTenant();
        $user = factory(User::class)->create();
        $property = factory(Property::class)->state('not-verified')->create(['api_identifier' => -99787]);

        $this->actingAs($user)
            ->from(routeForTenant('properties.index'))
            ->post(routeForTenant('mindbody.verify-ownership'), $this->validLoginParams($property))
            ->assertRedirect(routeForTenant('properties.index'))
            ->assertSessionHas('success', 'Property API connection is verified.');

        $this->assertTrue($property->fresh()->verified);
    }

    /** @test */
    public function syncs_mindbody_locations_action_is_called_when_verified_and_locations_are_stored()
    {
        $this->createTenant();

        $user = factory(User::class)->create();
        $property = factory(Property::class)->create(['api_identifier' => -99787]); // This site is connected.
        Location::all()->each->delete(); // Properties are automatically created with 2 locations matching the API. Delete them.

        $this->assertCount(0, Location::all());

        $this->actingAs($user)
            ->from(routeForTenant('properties.index'))
            ->post(routeForTenant('mindbody.verify-ownership'), $this->validLoginParams($property))
            ->assertRedirect(routeForTenant('properties.index'));


        $this->assertGreaterThan(0, Location::all()->count());
    }

    /** @test */
    public function it_puts_the_site_in_the_tenants_data_field()
    {
        $this->createTenant();

        $user = factory(User::class)->create();
        $property = factory(Property::class)->create(['api_identifier' => -99787, 'meta' => ['verified' => false]]); // This site is connected.
        Location::all()->each->delete(); // Properties are automatically created with 2 locations matching the API. Delete them.

        $this->assertCount(0, Location::all());

        $this->actingAs($user)
            ->from(routeForTenant('properties.index'))
            ->post(routeForTenant('mindbody.verify-ownership'), $this->validLoginParams($property))
            ->assertRedirect(routeForTenant('properties.index'));

        $this->assertTrue(array_key_exists('mb:-99787', tenant()->data));
    }

    /** @test */
    public function adds_app_access_token_when_verified()
    {
        $this->createTenant();
        $user = factory(User::class)->create();
        $property = factory(Property::class)->create(['api_identifier' => -99787]);

        $this->assertNull($property->getMeta('access_token'));

        $this->actingAs($user)
            ->from(routeForTenant('properties.edit', $property))
            ->post(routeForTenant('mindbody.verify-ownership'), $this->validLoginParams($property))
            ->assertRedirect(route('properties.index'));

        $property = $property->fresh();
        $this->assertTrue($property->verified);
        $this->assertEquals('access-token', $property->getMeta('access_token'));
    }

    /** @test */
    public function can_determine_if_a_given_user_does_not_own_a_property()
    {
        $this->createTenant();
        $user = factory(User::class)->state('super-admin')->create();
        $property = factory(Property::class)->state('not-verified')->create(['api_identifier' => 'not-connected']); // This property is not connected.

        $this->actingAs($user)
            ->from(routeForTenant('properties.edit', $property))
            ->post(routeForTenant('mindbody.verify-ownership'), [
                'api_identifier' => $property->api_identifier,
                'username' => 'invalid',
                'password' => 'invalid',
            ])
            ->assertRedirect(routeForTenant('properties.edit', $property))
            ->assertSessionHas('error', 'Verification failed. Check the site id and your site OWNER credentials.');

        $this->assertFalse($property->fresh()->verified);
    }

    /** @test */
    public function the_api_identifier_is_required()
    {
        $this->createTenant();
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->post(routeForTenant('mindbody.verify-ownership'), [
                'api_identifier' => '',
            ])->assertSessionHasErrors('api_identifier');
    }

    /**
     * @param $property
     * @return array
     */
    protected function validLoginParams($property): array
    {
        return [
            'api_identifier' => $property->api_identifier,
            'username' => 'owner',
            'password' => env('OWNER_PASSWORD'),
        ];
    }
}
