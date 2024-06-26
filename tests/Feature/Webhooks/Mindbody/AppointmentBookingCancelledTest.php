<?php

namespace Tests\Feature\Webhooks\Mindbody;

use App\Appointment;
use App\Client;
use App\Location;
use App\Property;
use App\Staff;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentBookingCancelledTest extends TestCase
{
    use RefreshDatabase;
    use WebhookHttpRequest;

    /**  @test */
    public function when_a_appointment_is_cancelled_at_mindbody_corresponding_appointment_is_cancelled_in_tenant_database()
    {
        $this->withoutExceptionHandling();
        $siteId = -99787;
        $this->createTenantWithProperty([$siteId]);
        $property = Property::findByApiIdentifier($siteId)->first();
        $location = factory(Location::class)->create(['property_id' => $property->id]);
        $staff = factory(Staff::class)->create(['property_id' => $property->id]);
        $client = factory(Client::class)->create(['property_id' => $property->id]);

        $existingAppointment = factory(Appointment::class)->create([
            'api_id' => 123,
            'property_id' => $property->id,
            'status' => 'Booked',
            'start_date_time' => now()->parse("November 11th 2019 12:00 PM"),
            'end_date_time' => now()->parse("November 11th 2019 12:30 PM"),
            'staff_requested' => true,
            'location_api_id' => $location->api_id,
            'staff_api_id' => $staff->api_id,
            'client_api_public_id' => $client->api_public_id,
            'first_appointment' => true,
            'room_name' => 'Yoga Room',
        ]);

        $this->receiveFakeWebhook($overrides = [
            "siteId" => $siteId,
            "appointmentId" => $existingAppointment->api_id,
        ])->assertOk();


        $cancelledAppointment = Property::findByApiIdentifier($siteId)
                          ->first()
                          ->appointments()
                          ->forApiId($existingAppointment->api_id)
                          ->first();

        $this->assertEquals('Cancelled', $cancelledAppointment->status);
    }

    private function getFakeData($overrides)
    {
        return [
            "messageId" => "ASwFMoA2Q5UKw69g3RDbvU",
            "eventId" => "appointmentBooking.cancelled",
            "eventSchemaVersion" => 1,
            "eventInstanceOriginationDateTime" => Carbon::now(),
            "eventData" => [
                "siteId" => data_get($overrides, "siteId"),
                "appointmentId" => data_get($overrides, "appointmentId"),
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
            $tenant->put('mb:'.$siteId, null);
        }

        return $tenant;
    }
}
