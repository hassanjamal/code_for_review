<?php

namespace Tests\Unit\Actions;

use App\Actions\SyncStaffToPropertyAction;
use App\Exceptions\PropertyNotVerifiedException;
use App\PlatformAPI\Mindbody\FakeMindbodyGateway;
use App\PlatformAPI\PlatformGateway;
use App\Property;
use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncStaffToPropertyActionTest extends TestCase
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

        app(SyncStaffToPropertyAction::class)
            ->execute($property->id);
    }

    /** @test */
    public function new_staff_members_are_created_for_the_property()
    {
        $this->createTenant();

        $property = factory(Property::class)->create();

        $this->assertCount(0, Staff::all());

        app(SyncStaffToPropertyAction::class)->execute($property->id);

        $staff = Staff::all();

        $this->assertGreaterThan(0, $staff->count());

        $this->assertEquals(2, $staff[1]->api_id);
        $this->assertEquals('Dr. Quinn', $staff[1]->first_name);
        $this->assertEquals('Medicine Woman', $staff[1]->last_name);
        $this->assertNull($staff[1]->api_access_token);
    }

    /** @test */
    public function if_a_staff_member_already_exists_it_will_be_updated()
    {
        $this->createTenant();

        // Create a property
        $property = factory(Property::class)->create(['api_identifier' => -99787]);

        // Sync the staff members.
        app(SyncStaffToPropertyAction::class)->execute($property->id);

        // Fake a change to a staff member to determine if it is updated later.
        $staff = Staff::get();
        $originalCount = $staff->count();
        $originalName = $staff->first()->first_name;
        $staff->first()->update(['first_name' => 'New Name']);

        $this->assertEquals('New Name', $staff->first()->first_name);

        // Sync again to test the system does not create new staff members.
        app(SyncStaffToPropertyAction::class)->execute($property->id);

        $staff = Staff::get();

        // This asserts that the staff member was updated.
        $this->assertEquals($originalName, $staff->first()->first_name);
        $this->assertEquals($originalCount, $staff->count());
    }

    protected function getExpectedStaffValuesForProperty($property)
    {
        $gatewayStaff = (new FakeMindbodyGateway())->getStaffMembers('-99787');

        return $gatewayStaff->each(function ($staff) use ($property) {
            $staff->property_id = $property->id;
        });
    }
}
