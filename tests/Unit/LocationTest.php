<?php

namespace Tests\Unit;

use App\Location;
use App\Property;
use App\Staff;
use App\Subscription;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_location_belongs_to_a_property()
    {
        $this->createTenant();

        $property = factory(Property::class)->create();
        $location = factory(Location::class)->create(['property_id' => $property->id]);

        $this->assertTrue($property->is($location->property));
    }

    /** @test */
    public function it_can_have_one_subscription()
    {
        $this->createTenant();

        $location = factory(Location::class)->create();

        $subscription = factory(Subscription::class)->create([
            'location_id' => $location->id,
        ]);

        $this->assertTrue($subscription->is($location->subscription));
    }

    /** @test */
    public function when_a_location_is_created_it_active_by_default_unless_specified()
    {
        $this->createTenant();

        $location = factory(Location::class)->create();
        $location->refresh();

        $this->assertTrue($location->isActive);
    }

    /** @test */
    public function can_be_scoped_based_on_visibility_to_a_user()
    {
        $this->createTenant();
        $propertyA = factory(Property::class)->state('no-locations')->create(['api_identifier' => '1234']);
        $propertyB = factory(Property::class)->state('no-locations')->create(['api_identifier' => '5678']);

        $propertyA->locations()->save(factory(Location::class)->make());
        $propertyB->locations()->save(factory(Location::class)->make());

        $superAdmin = factory(User::class)->state('super-admin')->create();

        $visibleLocations = Location::visibleToUser($superAdmin)->get();

        $this->assertCount(2, $visibleLocations);
        $this->assertEquals($propertyA->locations->first()->id, $visibleLocations[0]->id);
        $this->assertEquals($propertyB->locations->first()->id, $visibleLocations[1]->id);
    }

    /** @test */
    public function can_be_scoped_based_on_visibility_to_a_staff_member()
    {
        $this->createTenant();
        $propertyA = factory(Property::class)->state('no-locations')->create(['api_identifier' => '1234']);
        $propertyB = factory(Property::class)->state('no-locations')->create(['api_identifier' => '5678']);

        $propertyA->locations()->save(factory(Location::class)->make());
        $propertyB->locations()->save(factory(Location::class)->make());

        $staff = factory(Staff::class)->state('staff')->create(['property_id' => $propertyA->id]);

        $visibleLocations = Location::visibleToUser($staff)->get();

        $this->assertCount(1, $visibleLocations);
        $this->assertEquals($propertyA->locations->first()->id, $visibleLocations[0]->id);
    }
}
