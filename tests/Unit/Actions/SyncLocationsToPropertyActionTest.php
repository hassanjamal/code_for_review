<?php

namespace Tests\Unit\Actions;

use App\Actions\SyncLocationsToPropertyAction;
use App\Exceptions\PropertyNotVerifiedException;
use App\Location;
use App\PlatformAPI\Mindbody\FakeMindbodyGateway;
use App\PlatformAPI\PlatformGateway;
use App\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @see SyncLocationsToPropertyAction */
class SyncLocationsToPropertyActionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        app()->instance(PlatformGateway::class, new FakeMindbodyGateway);
    }

    /** @test */
    public function if_property_is_not_verified_an_exception_is_thrown()
    {
        $this->createTenant();

        $property = factory(Property::class)->state('not-verified')->create();

        $this->assertFalse($property->verified);

        $this->expectException(PropertyNotVerifiedException::class);

        app(SyncLocationsToPropertyAction::class)
            ->execute($property->id);
    }

    /** @test */
    public function a_new_locations_are_created_for_the_property()
    {
        $this->createTenant();

        $property = factory(Property::class)->create();
        $property->locations->each->delete(); // Properties are created with 2 locations by default.

        $this->assertCount(0, Location::all());

        app(SyncLocationsToPropertyAction::class)->execute($property->id);

        $locations = Location::get();

        $this->assertGreaterThan(0, $locations->count());

        $expected = $this->getExpectedLocationValuesForProperty($property);

        $this->assertEmpty(array_diff($expected[0]->toArray(), $locations[0]->toArray()));
        $this->assertEmpty(array_diff($expected[1]->toArray(), $locations[1]->toArray()));
    }

    /** @test */
    public function if_a_location_already_exists_it_will_be_updated()
    {
        $this->createTenant();

        // Create a property
        $property = factory(Property::class)->create(['api_identifier' => -99787]);

        // Sync the locations.
        app(SyncLocationsToPropertyAction::class)->execute($property->id);

        // Fake a change to a location to determine if it is update later.
        $locations = Location::get();
        $originalName = $locations->first()->name;
        $locations->first()->update(['name' => 'New Name']);

        $this->assertCount(2, $locations);
        $this->assertEquals('New Name', $locations->first()->name);

        // Sync again to test the system does not create new locations.
        app(SyncLocationsToPropertyAction::class)->execute($property->id);

        $locations = Location::get();

        // This asserts that a new location was not created.
        $this->assertCount(2, $locations);

        // This asserts that the location was updated.
        $this->assertEquals($originalName, $locations->first()->name);
    }

    protected function getExpectedLocationValuesForProperty($property)
    {
        $gatewayLocations = (new FakeMindbodyGateway())->getLocations('-99787');

        return $gatewayLocations->each(function ($location) use ($property) {
            $location->property_id = $property->id;
        });
    }
}
