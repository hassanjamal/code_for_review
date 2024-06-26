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

/** @see \App\Actions\CreateAppointmentFromMindbodyWebhookAction */
class AppointmentBookingCreatedTest extends TestCase
{
    use RefreshDatabase;
    use WebhookHttpRequest;

    protected function setUp(): void
    {
        parent::setUp();

        app()->instance(PlatformGateway::class, new FakeMindbodyGateway());
    }

    /**  @test */
    public function a_new_appointment_is_created_in_tenant_database()
    {
        $this->withoutExceptionHandling();
        $siteId = -99787;
        $tenant = $this->createTenantWithProperty([$siteId]);

        $property = Property::findByApiIdentifier($siteId)->first();
        $location = factory(Location::class)->create(['property_id' => $property->id, 'api_id' => 2]);
        $staff = factory(Staff::class)->create(['property_id' => $property->id]);
        $client = factory(Client::class)->create(['property_id' => $property->id]);

        $this->assertCount(0, Appointment::all());

        tenancy()->end();

        $this->receiveFakeWebhook($overrides = [
            "siteId" => $siteId,
            "appointmentId" => 70507,
            "status" => "Scheduled",
            "isConfirmed" => true,
            "hasArrived" => false,
            "locationId" => $location->api_id,
            "clientId" => $client->api_public_id,
            "staffId" => $staff->api_id,
            "startDateTime" => $webhookStartTime = now()->parse("November 14th 2019 3:30 PM")->toIso8601String(),
            "endDateTime" => $webhookEndTime = now()->parse("November 14th 2019 3:45 PM")->toIso8601String(),
            "durationMinutes" => 60,
            "resources" => [],
            "notes" => null,
            "formulaNotes" => null,
            "providerId" => null,
        ])->assertOk();

        tenancy()->initialize($tenant);

        $appointments = Appointment::all();
        $this->assertCount(1, $appointments);
        // Since the action to create the appointment calls the API for the appointment data,
        // I'm just testing that the appointment Id is correct.
        $this->assertEquals(70507, $appointments->first()->api_id);
    }

    /** @test */
    public function nothing_is_created_if_the_appointment_does_not_exist_on_the_api()
    {
        $siteId = -99787;
        $tenant = $this->createTenantWithProperty([$siteId]);

        $property = Property::findByApiIdentifier($siteId)->first();
        $location = factory(Location::class)->create(['property_id' => $property->id]);
        $staff = factory(Staff::class)->create(['property_id' => $property->id]);
        $client = factory(Client::class)->create(['property_id' => $property->id]);

        $this->assertCount(0, Appointment::all());

        tenancy()->end();

        $this->receiveFakeWebhook($overrides = [
            "siteId" => $siteId,
            "appointmentId" => -987654321, // Id we know does not exist
            "status" => "Scheduled",
            "isConfirmed" => true,
            "hasArrived" => false,
            "locationId" => $location->api_id,
            "clientId" => $client->api_public_id,
            "staffId" => $staff->api_id,
            "startDateTime" => $webhookStartTime = now()->parse("November 14th 2019 3:30 PM")->toIso8601String(),
            "endDateTime" => $webhookEndTime = now()->parse("November 14th 2019 3:45 PM")->toIso8601String(),
            "durationMinutes" => 60,
            "room_name" => [],
            "notes" => null,
            "formulaNotes" => null,
            "providerId" => null,
        ])->assertOk();

        tenancy()->initialize($tenant);

        $this->assertCount(0, Appointment::all());
    }

    /**  @test */
    public function a_new_appointment_is_not_created_in_tenant_database_if_tenant_is_set_but_property_does_not_exist()
    {
        $tenant = $this->createTenant();

        $knownDate = Carbon::create(2001, 5, 21, 12);
        Carbon::setTestNow($knownDate);

        $this->receiveFakeWebhook($overrides = [
            "siteId" => -99787,
            "appointmentId" => 70507,
            "status" => "Scheduled",
            "isConfirmed" => true,
            "hasArrived" => false,
            "locationId" => 1,
            "clientId" => 1,
            "staffId" => 1,
            "startDateTime" => $webhookStartTime = now()->parse("November 14th 2019 3:30 PM")->toIso8601String(),
            "endDateTime" => $webhookEndTime = now()->parse("November 14th 2019 3:45 PM")->toIso8601String(),
            "durationMinutes" => 60,
            "room_name" => [],
            "notes" => null,
            "formulaNotes" => null,
            "providerId" => null,
        ])->assertOk();

        tenancy()->initialize($tenant);

        $this->assertCount(0, Appointment::all());
    }

    private function getFakeData($overrides)
    {
        return [
            "messageId" => "ASwFMoA2Q5UKw69g3RDbvU",
            "eventId" => "appointmentBooking.created",
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
