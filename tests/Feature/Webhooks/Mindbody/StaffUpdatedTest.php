<?php

namespace Tests\Feature\Webhooks\Mindbody;

use App\Property;
use App\Staff;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffUpdatedTest extends TestCase
{
    use RefreshDatabase;
    use WebhookHttpRequest;

    /**  @test */
    public function when_a_staff_is_updated_at_mindbody_staff_is_updated_in_tenant_database()
    {
        $siteId = -99787;
        $this->createTenantWithProperty([$siteId], ['site_ids' => [-99787]]);

        $property = Property::findByApiIdentifier($siteId)->first();

        $property->staff()->create([
            'id' => makeDoubleCompositeKey($property->id, 100),
            'api_id' => 101,
            'first_name' => 'John',
            'last_name' => 'Smith',
        ]);

        $this->receiveFakeWebhook($overrides = [
            "staffId" => 101,
            "siteId" => $siteId,
            "staffFirstName" => "Jane",
            "staffLastName" => "Doe",
        ])->assertOk();

        $staff = Staff::forApiId(101)->first();
        $this->assertTrue($staff->property->is($property));
        $this->assertEquals('Jane', $staff->first_name);
        $this->assertEquals('Doe', $staff->last_name);
        $this->assertEquals(101, $staff->api_id);
    }

    /**  @test */
    public function a_staff_is_not_updated_in_tenant_database_if_property_is_not_set()
    {
        $this->createTenantWithProperty([-99787, 16134], ['site_ids' => [-99787]]);

        $property99787 = Property::findByApiIdentifier(-99787)->first();

        $property99787->staff()->create([
            'id' => makeDoubleCompositeKey($property99787->id, 100),
            'api_id' => 100,
            'first_name' => 'John',
            'last_name' => 'Smith',
        ]);

        $this->receiveFakeWebhook($overrides = [
            "staffId" => 100,
            "siteId" => 16134, // This tenant does not have a property 16134.
            "staffFirstName" => "Jane",
            "staffLastName" => "Doe",
        ])->assertOk();

        $staff = Staff::forApiId(100)->first();

        $this->assertEquals('John', $staff->first_name);
        $this->assertEquals('Smith', $staff->last_name);
        $this->assertTrue($property99787->is($staff->property));
    }

    /**  @test */
    public function staff_is_updated_in_valid_tenant_database()
    {
        // first tenant with multiple sites ( properties )
        $tenantFirst = $this->createTenantWithProperty([-99787, 16134], ['site_ids' => [-99787, 16134]]);

        $property99787 = Property::findByApiIdentifier(-99787)->first();

        $property99787->staff()->create([
            'id' => makeDoubleCompositeKey($property99787->id, 100),
            'api_id' => 100,
            'first_name' => 'John',
            'last_name' => 'Smith',
        ]);

        tenancy()->end();

        // another tenant with multiple sites
        $tenantSecond = $this->createTenantWithProperty([12345, 67890], [
            'domains' => 'foo.qn2020.test',
            'email' => 'foo@bar.com',
            'site_ids' => [12345, 67890],
        ]);

        $property12345 = Property::findByApiIdentifier(12345)->first();

        $property12345->staff()->create([
            'id' => makeDoubleCompositeKey($property12345->id, 100),
            'api_id' => 100,
            'first_name' => 'John',
            'last_name' => 'Smith',
        ]);

        tenancy()->end();

        $this->receiveFakeWebhook($overrides = [
            "staffId" => 100,
            "siteId" => -99787,
            "staffFirstName" => "Jane",
            "staffLastName" => "Doe",
        ])->assertOk();

        // testing the first tenant
        tenancy()->initialize($tenantFirst);
        $staff = Staff::forApiId(100)->first();

        $this->assertEquals('Jane', $staff->first_name); // should be updated
        $this->assertEquals('Doe', $staff->last_name); // should be updated
        $this->assertTrue($property99787->is($staff->property));
        tenancy()->end();

        // testing the second tenant
        tenancy()->initialize($tenantSecond);
        $staff = Staff::where('api_id', 100)->first();

        $this->assertEquals('John', $staff->first_name); // should remain intact
        $this->assertEquals('Smith', $staff->last_name); // should remain intact
        $this->assertTrue($property12345->is($staff->property));
        tenancy()->end();
    }

    /** @test */
    public function does_nothing_if_updated_is_received_and_webhook_staff_does_not_exisit()
    {
        $siteId = -99787;
        $this->createTenantWithProperty([$siteId], ['site_ids' => [$siteId]]);

        Property::findByApiIdentifier($siteId)->first();

        $this->assertCount(0, Staff::all());

        $this->receiveFakeWebhook($overrides = [
            "staffId" => 101,
            "siteId" => $siteId,
            "staffFirstName" => "Jane",
            "staffLastName" => "Doe",
        ])->assertOk();

        $this->assertCount(0, Staff::all());
    }

    private function getFakeData($overrides)
    {
        return [
            "messageId" => "ASwFMoA2Q5UKw69g3RDbvU",
            "eventId" => "staff.updated",
            "eventSchemaVersion" => 1,
            "eventInstanceOriginationDateTime" => Carbon::now(),
            "eventData" => [
                "staffId" => data_get($overrides, 'staffId'),
                "siteId" => data_get($overrides, 'siteId'),
                "addressLine1" => data_get($overrides, 'addressLine1'),
                "addressLine2" => data_get($overrides, 'addressLine2'),
                "staffFirstName" => data_get($overrides, 'staffFirstName'),
                "staffLastName" => data_get($overrides, 'staffLastName'),
                "city" => data_get($overrides, 'city'),
                "state" => data_get($overrides, 'state'),
                "country" => data_get($overrides, 'country'),
                "postalCode" => data_get($overrides, 'postalCode'),
                "sortOrder" => data_get($overrides, 'sortOrder'),
                "isIndependentContractor" => data_get($overrides, 'isIndependentContractor'),
                "alwaysAllowDoubleBooking" => data_get($overrides, 'alwaysAllowDoubleBooking'),
                "providerIds" => [
                    "688135485",
                ],
                "imageUrl" => data_get($overrides, 'imageUrl'),
                "biography" => data_get($overrides, 'biography'),
                "gender" => data_get($overrides, 'gender'),
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
