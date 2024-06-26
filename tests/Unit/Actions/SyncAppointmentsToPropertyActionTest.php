<?php

namespace Tests\Unit\Actions;

use App\Actions\SyncAppointmentsToPropertyAction;
use App\Appointment;
use App\Exceptions\PropertyNotVerifiedException;
use App\PlatformAPI\Mindbody\FakeMindbodyGateway;
use App\PlatformAPI\PlatformGateway;
use App\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncAppointmentsToPropertyActionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        app()->instance(PlatformGateway::class, new FakeMindbodyGateway);
    }

    /** @test */
    public function if_property_is_not_verified_an_error_is_thrown()
    {
        $this->createTenant();

        $property = factory(Property::class)->state('not-verified')->create();

        $this->assertFalse($property->verified);

        $this->expectException(PropertyNotVerifiedException::class);

        app(SyncAppointmentsToPropertyAction::class)
            ->execute($property->id, now()->subYears(100), now());
    }

    /** @test */
    public function new_appointments_are_created_for_the_property()
    {
        $this->createTenant();

        $property = factory(Property::class)->create();

        $this->assertCount(0, Appointment::all());

        app(SyncAppointmentsToPropertyAction::class)
            ->execute($property->id, now()->subYears(100), now());

        $appointments = Appointment::all();

        $this->assertGreaterThan(0, $appointments->count());

        $ids = $appointments->pluck('api_id');

        $this->assertContains('70507', $ids);
        $this->assertContains('70511', $ids);
        $this->assertContains('70510', $ids);
        $this->assertContains('70509', $ids);
    }

    /** @test */
    public function if_an_appointment_already_exists_it_will_be_updated()
    {
        $this->createTenant();

        // Create a property
        $property = factory(Property::class)->create(['api_identifier' => -99787]);

        // Sync the staff members.
        app(SyncAppointmentsToPropertyAction::class)
            ->execute($property->id, now()->subYears(100), now());

        // Fake a change to a staff member to determine if it is updated later.
        $appointments = Appointment::get();
        $originalCount = $appointments->count();
        $originalStatus = $appointments->first()->status;
        $appointments->first()->update(['status' => 'Updated Status']);

        $this->assertEquals('Updated Status', $appointments->first()->status);

        // Sync again to test the system does not create new staff members.
        app(SyncAppointmentsToPropertyAction::class)->execute($property->id, now()->subYears(100), now());

        $appointments = Appointment::get();

        // This asserts that the staff member was updated.
        $this->assertEquals($originalStatus, $appointments->first()->status);
        $this->assertEquals($originalCount, $appointments->count());
    }

    protected function getExpectedStaffValuesForProperty($property)
    {
        $gatewayStaff = (new FakeMindbodyGateway())->getStaffMembers('-99787');

        return $gatewayStaff->each(function ($staff) use ($property) {
            $staff->property_id = $property->id;
        });
    }
}
