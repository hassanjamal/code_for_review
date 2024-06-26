<?php

namespace Tests\Feature\Webhooks\Mindbody;

use App\Client;
use App\Property;
use App\Staff;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientProfileMergerTest extends TestCase
{
    use RefreshDatabase;
    use WebhookHttpRequest;

    /**  @test */
    public function a_new_client_is_created_in_tenant_database()
    {
        $this->withoutExceptionHandling();
        $siteId = -99787;
        $tenant = $this->createTenantWithProperty([$siteId]);

        $propertyId = Property::findByApiIdentifier($siteId)->first()->id;
        $client1 = factory(Client::class)->create([
            'property_id' => $propertyId,
            'api_public_id' => "client-1-id",
            'api_id' => 1000,
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john.smith@example.com',
        ]);

        $client2 = factory(Client::class)->create([
            'property_id' => $propertyId,
            'api_public_id' => "client-2-id",
            'api_id' => 2000,
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'john.smith@example.com',
        ]);


        // receive the webhook for merger
        $staff = factory(Staff::class)->create([
            'property_id' => $propertyId,
            'api_id' => 9999,
        ]);

        $mergedAt = Carbon::now()->toDateTimeString();

        $this->receiveFakeWebhook($overrides = [
            "siteId" => $siteId,
            "mergeDateTime" => $mergedAt,
            "mergedByStaffId" => $staff->api_id,
            "keptClientId" => $client1->api_public_id,
            "keptClientUniqueId" => $client1->api_id,
            "removedClientUniqueId" => $client2->api_id,
        ])->assertOk();

        tenancy()->initialize($tenant);

        $mergedClient = Property::findByApiIdentifier($siteId)
                                ->first()
                                ->clients()
                                ->forApiId($client2->api_id)
                                ->first();

        $this->assertEquals($staff->id, $mergedClient->merged_by);
        $this->assertEquals($client1->id, $mergedClient->merged_to);
        $this->assertEquals($mergedAt, $mergedClient->merged_at);
    }

    private function getFakeData($overrides)
    {
        return [
            "messageId" => "ASwFMoA2Q5UKw69g3RDbvU",
            "eventId" => "clientProfileMerger.created",
            "eventSchemaVersion" => 1,
            "eventInstanceOriginationDateTime" => Carbon::now(),
            "eventData" => [
                "siteId" => data_get($overrides, 'siteId'),
                "mergeDateTime" => data_get($overrides, 'mergeDateTime'),
                "mergedByStaffId" => data_get($overrides, 'mergedByStaffId'),
                "keptClientId" => data_get($overrides, 'keptClientId'),
                "keptClientUniqueId" => data_get($overrides, 'keptClientUniqueId'),
                "removedClientUniqueId" => data_get($overrides, 'removedClientUniqueId'),
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
            $tenant->put('mb:' . $siteId, null);
        }

        return $tenant;
    }
}
