<?php

namespace Tests\Feature;

use App\Appointment;
use App\Client;
use App\Exceptions\PropertyNotVerifiedException;
use App\Location;
use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrivacyConcernsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp():void
    {
        parent::setUp();

        $this->createTenant();
    }

    /** @test */
    public function cannot_save_anything_to_staff_if_property_is_not_verified()
    {
        $staff = factory(Staff::class)->create();

        $staff->property()->update(['verified_at' => null]);

        try {
            $staff->update(['first_name' => 'foo']);

            $this->fail('The exception was not thrown.');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof PropertyNotVerifiedException);
            $this->assertEquals('Cannot save staff data when property is not verified.', $e->getMessage());
        }

        try {
            factory(Staff::class)->create();

            $this->fail('The exception was not thrown.');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof PropertyNotVerifiedException);
            $this->assertEquals('Cannot save staff data when property is not verified.', $e->getMessage());
        }
    }

    /** @test */
    public function cannot_save_anything_to_clients_if_property_is_not_verified()
    {
        $client = factory(Client::class)->create();

        $client->property()->update(['verified_at' => null]);

        try {
            $client->update(['first_name' => 'foo']);

            $this->fail('The exception was not thrown.');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof PropertyNotVerifiedException);
            $this->assertEquals('Cannot save client data when property is not verified.', $e->getMessage());
        }

        try {
            factory(Client::class)->create();

            $this->fail('The exception was not thrown.');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof PropertyNotVerifiedException);
            $this->assertEquals('Cannot save client data when property is not verified.', $e->getMessage());
        }
    }

    /** @test */
    public function cannot_save_anything_to_locations_if_property_is_not_verified()
    {
        $location = factory(Location::class)->create();

        $location->property()->update(['verified_at' => null]);

        try {
            $location->update(['name' => 'foo']);

            $this->fail('The exception was not thrown.');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof PropertyNotVerifiedException);
            $this->assertEquals('Cannot save location data when property is not verified.', $e->getMessage());
        }

        try {
            factory(Location::class)->create();

            $this->fail('The exception was not thrown.');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof PropertyNotVerifiedException);
            $this->assertEquals('Cannot save location data when property is not verified.', $e->getMessage());
        }
    }

    /** @test */
    public function cannot_save_anything_to_appointments_if_property_is_not_verified()
    {
        $appointment = factory(Appointment::class)->create();

        $appointment->property()->update(['verified_at' => null]);

        try {
            $appointment->update(['service_name' => 'foo']);

            $this->fail('The exception was not thrown.');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof PropertyNotVerifiedException);
            $this->assertEquals('Cannot save appointment data when property is not verified.', $e->getMessage());
        }

        try {
            // I need to use the values from the old appointment here so that the other models created in the factory
            // don't trigger the exception before the appointment is saving.
            factory(Appointment::class)->create([
                'property_id' => $appointment->property_id,
                'location_id' => $appointment->location_id,
                'client_api_public_id' => $appointment->client_api_public_id,
                'staff_id' => $appointment->staff_id,
            ]);

            $this->fail('The exception was not thrown.');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof PropertyNotVerifiedException);
            $this->assertEquals('Cannot save appointment data when property is not verified.', $e->getMessage());
        }
    }
}
