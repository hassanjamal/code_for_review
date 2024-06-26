<?php

namespace Tests\Feature\Webhooks\Mindbody;

use App\Property;
use App\Staff;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffCreatedTest extends TestCase
{
    use RefreshDatabase;
    use WebhookHttpRequest;

    /**  @test */
    public function a_new_staff_is_created_in_tenant_database()
    {
        $this->withoutExceptionHandling();
        $tenant = $this->createTenantWithProperty([-99787]);

        $this->receiveFakeWebhook($overrides = [
            "staffId" => 100,
            "siteId" => -99787,
            "staffFirstName" => "Jane",
            "staffLastName" => "Doe",
        ])->assertOk();

        tenancy()->initialize($tenant);

        $staff = Staff::forApiId(100)->first();
        $this->assertTrue($staff->property->is(Property::findByApiIdentifier(-99787)->first()));
        $this->assertEquals('Jane', $staff->first_name);
        $this->assertEquals('Doe', $staff->last_name);
    }

    /** @test */
    public function nothing_happens_if_the_staff_member_already_exists_in_the_database()
    {
        // We have a tenant with a property and a staff member.
        $siteId = -99787;
        $tenant = $this->createTenantWithProperty([$siteId]);
        tenancy()->initialize($tenant);

        $property = Property::findByApiIdentifier($siteId)->first();

        $property->staff()->create([
            'id' => makeDoubleCompositeKey($property->id, 100),
            'api_id' => 100,
            'first_name' => 'John',
            'last_name' => 'Smith',
        ]);

        $this->assertCount(1, Staff::all());

        tenancy()->end();

        // We receive a webhook to create the staff member that already exists.
        $this->receiveFakeWebhook($overrides = [
            "staffId" => 100,
            "siteId" => $property->api_identifier,
            "staffFirstName" => "Jane",
            "staffLastName" => "Doe",
        ])->assertOk();

        tenancy()->initialize($tenant);

        // Assert nothing changes.
        $this->assertCount(1, Staff::all());

        $staff = Staff::forApiId(100)->first();
        $this->assertTrue($staff->property->is(Property::findByApiIdentifier(-99787)->first()));
        $this->assertEquals('John', $staff->first_name);
        $this->assertEquals('Smith', $staff->last_name);
    }

    /**  @test */
    public function a_new_staff_is_not_created_in_tenant_database_if_property_is_not_set()
    {
        $tenant = $this->createTenantWithProperty([16134]);

        $this->receiveFakeWebhook($overrides = [
            "staffId" => 100,
            "siteId" => -99787,
            "staffFirstName" => "Jane",
            "staffLastName" => "Doe",
        ])->assertOk();

        tenancy()->initialize($tenant);

        $this->assertCount(0, Staff::all());
    }


    private function getFakeData($overrides)
    {
        return [
            "messageId" => "ASwFMoA2Q5UKw69g3RDbvU",
            "eventId" => "staff.created",
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
            $tenant->put('mb:'.$siteId, $siteId);
        }

        return $tenant;
    }
}
