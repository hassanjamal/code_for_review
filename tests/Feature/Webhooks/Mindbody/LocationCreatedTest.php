<?php

namespace Tests\Feature\Webhooks\Mindbody;

use App\Location;
use App\Property;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationCreatedTest extends TestCase
{
    use RefreshDatabase;
    use WebhookHttpRequest;

    /**  @test */
    public function a_new_location_is_created_in_tenant_database()
    {
        $siteId = -99787;
        $tenant = $this->createTenantWithProperty([$siteId]);

        $this->receiveFakeWebhook($overrides = [
            "siteId" => $siteId,
            "locationId" => 44,
            "name" => "ACME Yoga (Downtown)",
            "addressLine1" => "123 ABC Ct",
            "addressLine2" => null,
            "city" => "San Luis Obispo",
            "state" => "CA",
            "postalCode" => "93401",
            "phone" => "8055551234",
            "latitude" => 150.123,
            "longitude" => 120.1211,
        ])->assertOk();

        tenancy()->initialize($tenant);

        $location = Location::forApiId(44)->first();

        $this->assertEquals(Property::findByApiIdentifier($siteId)->first()->id, $location->property_id);
        $this->assertEquals(44, $location->api_id);
        $this->assertEquals("ACME Yoga (Downtown)", $location->name);
        $this->assertEquals("123 ABC Ct", $location->address);
        $this->assertNull($location->address_2);
        $this->assertEquals("San Luis Obispo", $location->city);
        $this->assertEquals("CA", $location->state_province);
        $this->assertEquals("93401", $location->postal_code);
        $this->assertEquals("8055551234", $location->phone);
        $this->assertEquals(150.123, $location->latitude);
        $this->assertEquals(120.1211, $location->longitude);
    }

    /**  @test */
    public function a_new_location_is_not_created_in_tenant_database_if_tenant_is_not_set()
    {
        $tenant = $this->createTenantWithProperty([16134]);

        $this->assertCount(2, Location::all()); // Properties are automatically created with the 2 API locations.

        $this->receiveFakeWebhook($overrides = [
            "siteId" => "-99787",
            "locationId" => 44,
            "name" => "ACME Yoga (Downtown)",
            "addressLine1" => "123 ABC Ct",
            "addressLine2" => null,
            "city" => "San Luis Obispo",
            "state" => "CA",
            "postalCode" => "93401",
            "phone" => "8055551234",
            "latitude" => 150.0,
            "longitude" => 120.0,
        ])->assertOk();

        tenancy()->initialize($tenant);

        $this->assertCount(2, Location::all()); // Properties are automatically created with the 2 API locations.
    }

    /**  @test */
    public function a_new_location_is_not_created_in_tenant_database_if_tenant_is_set_but_property_is_not_set()
    {
        $tenant = $this->createTenant();

        $this->receiveFakeWebhook($overrides = [
            "siteId" => "-99787",
            "locationId" => 2,
            "name" => "ACME Yoga (Downtown)",
            "addressLine1" => "123 ABC Ct",
            "addressLine2" => null,
            "city" => "San Luis Obispo",
            "state" => "CA",
            "postalCode" => "93401",
            "phone" => "8055551234",
            "latitude" => 150.0,
            "longitude" => 120.0,
        ])->assertOk();

        tenancy()->initialize($tenant);

        $this->assertCount(0, Location::all());
    }


    private function getFakeData($overrides)
    {
        return [
            "messageId" => "ASwFMoA2Q5UKw69g3RDbvU",
            "eventId" => "location.created",
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
        }

        return $tenant;
    }
}
