<?php

namespace Tests\Unit;

use App\Appointment;
use App\Client;
use App\Location;
use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_location()
    {
        $this->createTenant();
        $location = factory(Location::class)->create();

        $appointment = factory(Appointment::class)->create([
            'property_id' => $location->property_id,
            'location_id' => $location->id,
        ]);

        $this->assertTrue($location->is($appointment->location));
    }

    /** @test */
    public function it_belongs_to_a_staff()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->create();

        $appointment = factory(Appointment::class)->create([
            'property_id' => $staff->property_id,
            'staff_id' => $staff->id,
        ]);

        $this->assertTrue($staff->is($appointment->staff));
    }

    /** @test */
    public function it_belongs_to_a_client()
    {
        $this->createTenant();
        $client = factory(Client::class)->create();

        $appointment = factory(Appointment::class)->create([
            'property_id' => $client->property_id,
            'client_api_public_id' => $client->api_public_id,
        ]);

        $this->assertTrue($client->is($appointment->client));
    }

    /** @test */
    public function can_make_a_composite_id_for_itself()
    {
        $appointment = new Appointment(['property_id' => 10, 'location_api_id' => 20, 'api_id' => 12345]);

        $actual = $appointment->makeCompositeKey();

        $this->assertEquals('10:20:12345', $actual);
    }

    /** @test */
    public function an_id_is_generated_automatically_upon_creation_if_one_does_not_yet_exist()
    {
        $this->createTenant();

        $appointment = factory(Appointment::class)->create();

        $this->assertEquals($appointment->makeCompositeKey(), $appointment->id);
    }
}
