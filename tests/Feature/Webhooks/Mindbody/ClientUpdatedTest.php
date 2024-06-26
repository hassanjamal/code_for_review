<?php

namespace Tests\Feature\Webhooks\Mindbody;

use App\Client;
use App\Property;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientUpdatedTest extends TestCase
{
    use RefreshDatabase;
    use WebhookHttpRequest;

    /**  @test */
    public function when_a_client_is_updated_at_mindbody_corresponding_client_is_updated_in_tenant_database()
    {
        $siteId = -99787;
        $this->createTenantWithProperty([$siteId]);
        $propertyId = Property::findByApiIdentifier($siteId)->first()->id;
        $existingClient = factory(Client::class)->create([
            'property_id' => $propertyId,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        $this->receiveFakeWebhook($overrides = [
            'siteId' => $siteId,
            'clientId' => "new-id",
            'clientUniqueId' => $existingClient->api_id,
            'firstName' => 'John',
            'lastName' => 'Smith',
        ])->assertOk();


        $client = Property::findByApiIdentifier($siteId)
                          ->first()
                          ->clients()
                          ->forApiId($existingClient->api_id)
                          ->first();
        $this->assertEquals($propertyId, $client->property_id);
        $this->assertEquals("new-id", $client->api_public_id);
        $this->assertEquals('John', $client->first_name);
        $this->assertEquals('Smith', $client->last_name);
    }

    /**  @test */
    public function it_throws_200_and_does_not_update_db_for_client_update_webhook_have_same_client_unique_id_but_for_different_property_id()
    {
        $this->createTenantWithProperty([-99787, 16134]);

        $property99787 = Property::findByApiIdentifier(-99787)->first();

        $existingClient = factory(Client::class)->create([
            'property_id' => $property99787->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        $this->receiveFakeWebhook($overrides = [
            'siteId' => 16134,
            'clientId' => "new-id",
            'clientUniqueId' => $existingClient->api_id,
            'firstName' => 'John',
            'lastName' => 'Smith',
        ])->assertStatus(200);

        $client = Property::findByApiIdentifier(-99787)
                          ->first()
                          ->clients()
                          ->forApiId($existingClient->api_id)
                          ->first();

        $this->assertEquals('Jane', $client->first_name);
        $this->assertEquals('Doe', $client->last_name);
    }


    private function getFakeData($overrides)
    {
        return [
            "messageId" => "ASwFMoA2Q5UKw69g3RDbvU",
            "eventId" => "client.updated",
            "eventSchemaVersion" => 1,
            "eventInstanceOriginationDateTime" => Carbon::now(),
            "eventData" => [
                "siteId" => data_get($overrides, 'siteId'),
                "clientId" => data_get($overrides, 'clientId'),
                "clientUniqueId" => data_get($overrides, 'clientUniqueId'),
                "creationDateTime" => data_get($overrides, 'creationDateTime'),
                "status" => data_get($overrides, 'status'),
                "firstName" => data_get($overrides, 'firstName'),
                "lastName" => data_get($overrides, 'lastName'),
                "email" => data_get($overrides, 'email'),
                "mobilePhone" => data_get($overrides, 'mobilePhone'),
                "homePhone" => data_get($overrides, 'homePhone'),
                "workPhone" => data_get($overrides, 'workPhone'),
                "addressLine1" => data_get($overrides, 'addressLine1'),
                "addressLine2" => data_get($overrides, 'addressLine2'),
                "city" => data_get($overrides, 'city'),
                "state" => data_get($overrides, 'state'),
                "postalCode" => data_get($overrides, 'postalCode'),
                "country" => data_get($overrides, 'country'),
                "birthDateTime" => data_get($overrides, 'birthDateTime'),
                "gender" => data_get($overrides, 'gender'),
                "appointmentGenderPreference" => data_get($overrides, 'appointmentGenderPreference'),
                "firstAppointmentDateTime" => data_get($overrides, 'firstAppointmentDateTime'),
                "referredBy" => data_get($overrides, 'referredBy'),
                "isProspect" => data_get($overrides, 'isProspect'),
                "isCompany" => data_get($overrides, 'isCompany'),
                "isLiabilityReleased" => data_get($overrides, 'isLiabilityReleased'),
                "liabilityAgreementDateTime" => data_get($overrides, 'liabilityAgreementDateTime'),
                "homeLocation" => data_get($overrides, 'homeLocation'),
                "clientNumberOfVisitsAtSite" => data_get($overrides, 'clientNumberOfVisitsAtSite'),
                "indexes" => data_get($overrides, 'indexes'),
                "sendPromotionalEmails" => data_get($overrides, 'sendPromotionalEmails'),
                "sendScheduleEmails" => data_get($overrides, 'sendScheduleEmails'),
                "sendAccountEmails" => data_get($overrides, 'sendAccountEmails'),
                "sendPromotionalTexts" => data_get($overrides, 'sendPromotionalTexts'),
                "sendScheduleTexts" => data_get($overrides, 'sendScheduleTexts'),
                "sendAccountTexts" => data_get($overrides, 'sendAccountTexts'),
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
