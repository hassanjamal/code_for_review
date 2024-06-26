<?php

namespace Tests\Feature\Webhooks\Mindbody;

use App\Appointment;
use App\Client;
use App\Location;
use App\PlatformAPI\Mindbody\FakeMindbodyGateway;
use App\PlatformAPI\PlatformGateway;
use App\Property;
use App\Staff;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentBookingUpdatedTest extends TestCase
{
    use RefreshDatabase;
    use WebhookHttpRequest;

    public function setUp(): void
    {
        parent::setUp();

        app()->instance(PlatformGateway::class, new FakeMindbodyGateway());
    }

    /**  @test */
    public function when_appointment_webhook_is_received_our_appointment_is_updated()
    {
        $this->createTenantWithProperty([-99787]); // Will be created with 2 locations corresponding to API locations.

        $property = Property::findByApiIdentifier(-99787)->first();
        $staff = factory(Staff::class)->create(['property_id' => $property->id]);
        $client = factory(Client::class)->create(['property_id' => $property->id]);

        //  Get the first appointment from the Fake gateway to set the necessary data before the update.
        // The update webhook calls the API again to get the new details of an appointment.
        // Here we are going to spoof some data in the existing appointment, and after the update,
        // the new data should match the first fake gateway appointment.
        $firstFakeGateWayAppointment = (new FakeMindbodyGateway())->getAppointments($property->api_identifier, now()->subYears(100), now())->first();

        $existingAppointment = factory(Appointment::class)->create([
            'id' => $firstFakeGateWayAppointment->id,
            'api_id' => $firstFakeGateWayAppointment->api_id,
            'property_id' => $property->id,
            'location_id' => $property->locations->first()->id,
            'location_api_id' => $property->locations->first()->api_id,
            'staff_id' => 'original-staff-id',
            'staff_api_id' => 100,
            'client_api_public_id' => 'original-client-public-id',
            'start_date_time' => now()->parse("November 10th 2019 11:00 PM"),
            'end_date_time' => now()->parse("November 10th 2019 3:30 PM"),
            'duration' => 500,
            'status' => 'Not a status',
            'staff_requested' => false,
            'service_name' => 'service faked',
            'first_appointment' => true,
            'room_name' => 'Room faked',
        ]);

        // And I receive a webhook to update that appointment.
        $this->receiveFakeWebhook($overrides = [
            "siteId" => $property->api_identifier,
            "appointmentId" => $existingAppointment->api_id,
            "status" => "Scheduled",
            "isConfirmed" => true,
            "hasArrived" => false,
            "locationId" => $property->locations[1]->api_id,
            "clientId" => $newClient = (string) factory(Client::class)->create(['property_id' => $property->id])->api_id,
            "staffId" => $newStaff = factory(Staff::class)->create(['property_id' => $property->id])->api_id,
            "startDateTime" => $webhookStartTime = now()->parse("November 14th 2019 12:10 PM")->toIso8601String(),
            "endDateTime" => $webhookEndTime = now()->parse("November 14th 2019 12:45 PM")->toIso8601String(),
            "durationMinutes" => 120,
            "genderRequested" => 'male',
            "resources" => [['id' => 1, 'Name ' => 'foo']],
            "notes" => 'Some Notes',
            "formulaNotes" => 'Formula Notes',
            "providerId" => null,
        ])->assertOk();

        // The appointment should now contain the new data that matches the appointment coming from the fake API.
        $updated = $existingAppointment->fresh();

        $this->assertEquals($firstFakeGateWayAppointment->id, $updated->id);
        $this->assertEquals($firstFakeGateWayAppointment->api_id, $updated->api_id);
        $this->assertEquals($firstFakeGateWayAppointment->property_id, $updated->property_id);
        $this->assertEquals($firstFakeGateWayAppointment->location_id, $updated->location_id);
        $this->assertEquals($firstFakeGateWayAppointment->location_api_id, $updated->location_api_id);
        $this->assertEquals($firstFakeGateWayAppointment->staff_id, $updated->staff_id);
        $this->assertEquals($firstFakeGateWayAppointment->staff_api_id, $updated->staff_api_id);
        $this->assertEquals($firstFakeGateWayAppointment->client_api_public_id, $updated->client_api_public_id);
        $this->assertEquals($firstFakeGateWayAppointment->start_date_time, $updated->start_date_time);
        $this->assertEquals($firstFakeGateWayAppointment->end_date_time, $updated->end_date_time);
        $this->assertEquals($firstFakeGateWayAppointment->duration, $updated->duration);
        $this->assertEquals($firstFakeGateWayAppointment->status, $updated->status);
        $this->assertEquals($firstFakeGateWayAppointment->staff_requested, $updated->staff_requested);
        $this->assertEquals($firstFakeGateWayAppointment->service_name, $updated->service_name);
        $this->assertEquals($firstFakeGateWayAppointment->first_appointment, $updated->first_appointment);
        $this->assertEquals($firstFakeGateWayAppointment->room_name, $updated->room_name);
    }

    /**  @test */
    public function an_appointment_is_not_updated_in_tenant_database_if_property_is_not_set()
    {
        $this->withoutExceptionHandling();
        $this->createTenantWithProperty([-99787, 16134]);

        $property99787 = Property::findByApiIdentifier(-99787)->first();

        $location = factory(Location::class)->create(['property_id' => $property99787->id]);
        $staff = factory(Staff::class)->create(['property_id' => $property99787->id]);
        $client = factory(Client::class)->create(['property_id' => $property99787->id]);

        // Database has an existing appointment.
        $existingAppointment = factory(Appointment::class)->create([
            'api_id' => 123,
            'property_id' => $property99787->id,
            'status' => 'Booked',
            'start_date_time' => $startTime = now()->parse("November 11th 2019 12:00 PM"),
            'end_date_time' => $endTime = now()->parse("November 11th 2019 12:30 PM"),
            'staff_requested' => true,
            'location_api_id' => $location->api_id,
            'staff_api_id' => $staff->api_id,
            'client_api_public_id' => $client->api_public_id,
            'first_appointment' => true,
            'room_name' => 'Yoga Room',
        ]);

        // Webhook is received for an appointment with the asme appointment ID but for a different property
        $this->receiveFakeWebhook($overrides = [
            'siteId' => 16134,
            "appointmentId" => $existingAppointment->api_id,
            "status" => "Scheduled",
            "isConfirmed" => true,
            "hasArrived" => false,
            "locationId" => 1,
            "clientId" => 1,
            "staffId" => 1,
            "startDateTime" => $webhookStartTime = now()->parse("November 15th 2019 12:10 PM")->toIso8601String(),
            "endDateTime" => $webhookEndTime = now()->parse("November 15th 2019 12:45 PM")->toIso8601String(),
            "durationMinutes" => 120,
            "genderRequested" => 'male',
            "resources" => [],
            "notes" => 'Some Notes',
            "formulaNotes" => 'Formula Notes',
            "providerId" => null,
        ])->assertOk();

        $freshAppointment = Property::findByApiIdentifier(-99787)->first()->appointments()->forApiId($existingAppointment->api_id)->first();

        // Assert the existing appointment is unchanged.
        $this->assertEquals($existingAppointment->api_id, $freshAppointment->api_id);
        $this->assertEquals($existingAppointment->property_id, $freshAppointment->property_id);
        $this->assertEquals($existingAppointment->status, $freshAppointment->status);
        $this->assertEquals($existingAppointment->start_date_time, $freshAppointment->start_date_time);
        $this->assertEquals($existingAppointment->end_date_time, $freshAppointment->end_date_time);
        $this->assertEquals($existingAppointment->staff_requested, $freshAppointment->staff_requested);
        $this->assertEquals($existingAppointment->location_api_id, $freshAppointment->location_api_id);
        $this->assertEquals($existingAppointment->staff_api_id, $freshAppointment->staff_api_id);
        $this->assertEquals($existingAppointment->client_api_public_id, $freshAppointment->client_api_public_id);
        $this->assertEquals($existingAppointment->first_appointment, $freshAppointment->first_appointment);
        $this->assertEquals($existingAppointment->room_name, $freshAppointment->room_name);
    }

    private function getFakeData($overrides)
    {
        return [
            "messageId" => "ASwFMoA2Q5UKw69g3RDbvU",
            "eventId" => "appointmentBooking.updated",
            "eventSchemaVersion" => 1,
            "eventInstanceOriginationDateTime" => Carbon::now(),
            "eventData" => [
                "siteId" => data_get($overrides, "siteId"),
                "appointmentId" => data_get($overrides, "appointmentId"),
                "status" => data_get($overrides, "status"),
                "isConfirmed" => data_get($overrides, "isConfirmed"),
                "hasArrived" => data_get($overrides, "hasArrived"),
                "locationId" => data_get($overrides, "locationId"),
                "clientId" => data_get($overrides, "clientId"),
                "clientFirstName" => data_get($overrides, "clientFirstName"),
                "clientLastName" => data_get($overrides, "clientLastName"),
                "clientEmail" => data_get($overrides, "clientEmail"),
                "clientPhone" => data_get($overrides, "clientPhone"),
                "staffId" => data_get($overrides, "staffId"),
                "staffFirstName" => data_get($overrides, "staffFirstName"),
                "staffLastName" => data_get($overrides, "staffLastName"),
                "startDateTime" => data_get($overrides, "startDateTime"),
                "endDateTime" => data_get($overrides, "endDateTime"),
                "durationMinutes" => data_get($overrides, "durationMinutes"),
                "genderRequested" => data_get($overrides, "genderRequested"),
                "resources" => data_get($overrides, "resources"),
                "notes" => data_get($overrides, "notes"),
                "formulaNotes" => data_get($overrides, "formulaNotes"),
                "icdCodes" => data_get($overrides, "icdCodes"),
                "providerId" => data_get($overrides, "providerId"),
            ],
        ];
    }

    private function createTenantWithProperty(array $siteIds, array $tenantOverrides = [])
    {
        $tenant = $this->createTenant($tenantOverrides);
        foreach ($siteIds as $siteId) {
            factory(Property::class)->create([
                'api_identifier' => $siteId,
            ]);
        }

        return $tenant;
    }
}
