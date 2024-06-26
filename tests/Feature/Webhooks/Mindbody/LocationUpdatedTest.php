<?php

namespace Tests\Feature\Webhooks\Mindbody;

use App\Location;
use App\Property;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationUpdatedTest extends TestCase
{
    use RefreshDatabase;
    use WebhookHttpRequest;

    /**  @test */
    public function when_a_location_is_updated_at_mindbody_corresponding_location_is_updated_in_tenant_database()
    {
        $this->withoutExceptionHandling();
        $siteId = -99787;
        $this->createTenantWithProperty([$siteId]);
        $property = Property::findByApiIdentifier($siteId)->first(); // Properties are created with Locations that match the API.
        $locations = $property->locations;


        $this->receiveFakeWebhook($overrides = [
            "siteId" => $siteId,
            "locationId" => $locations[0]->api_id,
            "name" => "Changed Name For Location",
        ])->assertOk();

        $this->assertEquals("Changed Name For Location", $locations[0]->fresh()->name);
    }

    /**  @test */
    public function a_location_is_not_updated_in_tenant_database_if_property_is_not_set()
    {
        $this->createTenantWithProperty([-99787, 16134]);

        $property99787 = Property::findByApiIdentifier(-99787)->first();
        $locationToTestAgainst = $property99787->locations->first();

        // Location has same API Id as the one we're testing but the webhook is for a location from a different site.
        $this->receiveFakeWebhook($overrides = [
            "siteId" => 16134, // Notice different Site ID.
            "locationId" => $locationToTestAgainst->api_id, // Noteice same location api_id
            "name" => "Changed Name For Location",
        ])->assertOk();

        $updated = $locationToTestAgainst->fresh();

        // Ensure the -99787 location was not updated.
        $this->assertEquals($locationToTestAgainst->toArray(), $updated->toArray(), 'The location was updated and should not have been.');
    }


    private function getFakeData($overrides)
    {
        return [
            "messageId" => "ASwFMoA2Q5UKw69g3RDbvU",
            "eventId" => "location.updated",
            "eventSchemaVersion" => 1,
            "eventInstanceOriginationDateTime" => Carbon::now(),
            "eventData" => [
                "siteId" => data_get($overrides, 'siteId'),
                "locationId" => data_get($overrides, 'locationId'),
                "name" => data_get($overrides, 'name'),
                "description" => data_get($overrides, 'description'),
                "hasClasses" => data_get($overrides, 'hasClasses'),
                "phoneExtension" => data_get($overrides, 'phoneExtension'),
                "addressLine1" => data_get($overrides, 'addressLine1'),
                "addressLine2" => data_get($overrides, 'addressLine2'),
                "city" => data_get($overrides, 'city'),
                "state" => data_get($overrides, 'state'),
                "postalCode" => data_get($overrides, 'postalCode'),
                "phone" => data_get($overrides, 'phone'),
                "latitude" => data_get($overrides, 'latitude'),
                "longitude" => data_get($overrides, 'longitude'),
                "tax1" => data_get($overrides, 'tax1'),
                "tax2" => data_get($overrides, 'tax2'),
                "tax3" => data_get($overrides, 'tax3'),
                "tax4" => data_get($overrides, 'tax4'),
                "tax5" => data_get($overrides, 'tax5'),
                "webColor5" => data_get($overrides, 'webColor5'),
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
