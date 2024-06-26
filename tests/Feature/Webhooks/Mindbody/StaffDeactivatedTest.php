<?php

namespace Tests\Feature\Webhooks\Mindbody;

use App\Property;
use App\Staff;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffDeactivatedTest extends TestCase
{
    use RefreshDatabase;
    use WebhookHttpRequest;

    /**  @test */
    public function when_a_staff_is_deactivated_at_mindbody_staff_is_deactivate_in_tenant_database()
    {
        $siteId = -99787;
        $this->createTenantWithProperty([$siteId]);

        $property = Property::findByApiIdentifier($siteId)->first();

        $property->staff()->create([
            'id' => makeDoubleCompositeKey($property->id, 100),
            'api_id' => 101,
            'first_name' => 'John',
            'last_name' => 'Smith',
            'is_active' => true,
        ]);

        $this->receiveFakeWebhook($overrides = [
            "staffId" => 101,
            "siteId" => -99787,
            "staffFirstName" => "Jane",
            "staffLastName" => "Doe",
        ])->assertOk();

        $staff = Staff::forApiId(101)->first();
        $this->assertEquals('101', $staff->api_id);
        $this->assertFalse($staff->is_active);
    }

    /**  @test */
    public function if_staff_does_not_exist_in_our_database_nothing_happens_when_the_webhook_is_received()
    {
        $this->withoutExceptionHandling();
        $this->createTenantWithProperty([-99787]);

        $this->assertCount(0, Staff::all());

        $this->receiveFakeWebhook($overrides = [
            "staffId" => 101,
            "siteId" => -99787,
            "staffFirstName" => "Jane",
            "staffLastName" => "Doe",
        ])->assertOk();

        $this->assertCount(0, Staff::all());
    }

    private function getFakeData($overrides)
    {
        return [
            "messageId" => "ASwFMoA2Q5UKw69g3RDbvU",
            "eventId" => "staff.deactivated",
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
            $tenant->put('mb:'.$siteId, null);
        }

        return $tenant;
    }
}
