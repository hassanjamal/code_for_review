<?php

namespace Tests\Feature\Webhooks\Mindbody;

use App\Property;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationDeactivatedTest extends TestCase
{
    use RefreshDatabase;
    use WebhookHttpRequest;

    /**  @test */
    public function when_a_location_is_deactivated_at_mindbody_corresponding_location_is_deactivated_in_tenant_database()
    {
        $this->withoutExceptionHandling();

        $siteId = -99787;

        $this->createTenantWithProperty([$siteId]);

        $property = Property::findByApiIdentifier($siteId)->first();

        $locations = $property->locations;

        $locations->each(function ($l) {
            $this->assertTrue($l->active);
        });

        $this->receiveFakeWebhook($overrides = [
            "siteId" => $siteId,
            "locationId" => $locations[0]->api_id,
        ])->assertOk();

        $locations = $locations->fresh();
        $this->assertFalse($locations[0]->active);
        $this->assertTrue($locations[1]->active);
    }

    private function getFakeData($overrides)
    {
        return [
            "messageId" => "ASwFMoA2Q5UKw69g3RDbvU",
            "eventId" => "location.deactivated",
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
