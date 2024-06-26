<?php

namespace Tests\Feature\Webhooks\Mindbody;

use App\Client;
use App\Property;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientCreatedTest extends TestCase
{
    use RefreshDatabase;
    use WebhookHttpRequest;

    /**  @test */
    public function a_new_client_is_created_in_tenant_database()
    {
        $this->withoutExceptionHandling();
        $siteId = -99787;
        $tenant = $this->createTenantWithProperty([$siteId]);

        $this->receiveFakeWebhook($overrides = [
            "siteId" => $siteId,
            "clientId" => "100000009",
            "clientUniqueId" => 100000009,
            "creationDateTime" => "2018-08-28T06:45:58Z",
            "status" => "Non-Member",
            "firstName" => "John",
            "lastName" => "Smith",
            "email" => "john.smith@gmail.com",
            "mobilePhone" => "8055551234",
            "homePhone" => null,
            "workPhone" => null,
            "addressLine1" => "123 ABC Ct",
            "addressLine2" => null,
            "city" => "San Luis Obispo",
            "state" => "CA",
            "postalCode" => "93401",
            "country" => "US",
            "birthDateTime" => "1989-07-02T00:00:00Z",
            "gender" => "Male",
            "appointmentGenderPreference" => null,
            "firstAppointmentDateTime" => "2018-08-29T06:45:58Z",
            "referredBy" => null,
            "isProspect" => false,
            "isCompany" => false,
            "isLiabilityReleased" => true,
            "liabilityAgreementDateTime" => "2018-08-29T06:45:58Z",
            "homeLocation" => 1,
            "clientNumberOfVisitsAtSite" => 2,
            "indexes" => [
                [
                    "indexName" => "LongtermGoal",
                    "indexValue" => "IncreasedFlexibility",
                ],
            ],
            "sendPromotionalEmails" => true,
            "sendScheduleEmails" => true,
            "sendAccountEmails" => true,
            "sendPromotionalTexts" => false,
            "sendScheduleTexts" => false,
            "sendAccountTexts" => false,
        ])->assertOk();

        tenancy()->initialize($tenant);

        $client = Property::findByApiIdentifier($siteId)
                          ->first()
                          ->clients()
                          ->forApiId(100000009)
                          ->first();

        $this->assertEquals(Property::findByApiIdentifier($siteId)->first()->id, $client->property_id);
        $this->assertEquals(100000009, $client->api_id);
        $this->assertEquals("100000009", $client->api_public_id);
        $this->assertEquals("John", $client->first_name);
        $this->assertEquals("Smith", $client->last_name);
        $this->assertEquals("john.smith@gmail.com", $client->email);
    }

    /**  @test */
    public function a_new_client_is_not_created_in_tenant_database_if_tenant_is_not_set()
    {
        $tenant = $this->createTenantWithProperty([16134]);

        $this->receiveFakeWebhook($overrides = [
            "siteId" => 123,
            "clientId" => "100000009",
            "clientUniqueId" => 100000009,
            "creationDateTime" => "2018-08-28T06:45:58Z",
            "status" => "Non-Member",
            "firstName" => "John",
            "lastName" => "Smith",
            "email" => "john.smith@gmail.com",
            "mobilePhone" => "8055551234",
            "homePhone" => null,
            "workPhone" => null,
            "addressLine1" => "123 ABC Ct",
            "addressLine2" => null,
            "city" => "San Luis Obispo",
            "state" => "CA",
            "postalCode" => "93401",
            "country" => "US",
            "birthDateTime" => "1989-07-02T00:00:00Z",
            "gender" => "Male",
            "appointmentGenderPreference" => null,
            "firstAppointmentDateTime" => "2018-08-29T06:45:58Z",
            "referredBy" => null,
            "isProspect" => false,
            "isCompany" => false,
            "isLiabilityReleased" => true,
            "liabilityAgreementDateTime" => "2018-08-29T06:45:58Z",
            "homeLocation" => 1,
            "clientNumberOfVisitsAtSite" => 2,
            "indexes" => [
                [
                    "indexName" => "LongtermGoal",
                    "indexValue" => "IncreasedFlexibility",
                ],
            ],
            "sendPromotionalEmails" => true,
            "sendScheduleEmails" => true,
            "sendAccountEmails" => true,
            "sendPromotionalTexts" => false,
            "sendScheduleTexts" => false,
            "sendAccountTexts" => false,
        ])->assertOk();

        tenancy()->initialize($tenant);

        $this->assertCount(0, Client::all());
    }

    /**  @test */
    public function a_new_client_is_not_created_in_tenant_database_if_tenant_is_set_but_property_is_not_set()
    {
        $tenant = $this->createTenant();

        $this->receiveFakeWebhook($overrides = [
            "siteId" => 123,
            "clientId" => "100000009",
            "clientUniqueId" => 100000009,
            "creationDateTime" => "2018-08-28T06:45:58Z",
            "status" => "Non-Member",
            "firstName" => "John",
            "lastName" => "Smith",
            "email" => "john.smith@gmail.com",
            "mobilePhone" => "8055551234",
            "homePhone" => null,
            "workPhone" => null,
            "addressLine1" => "123 ABC Ct",
            "addressLine2" => null,
            "city" => "San Luis Obispo",
            "state" => "CA",
            "postalCode" => "93401",
            "country" => "US",
            "birthDateTime" => "1989-07-02T00:00:00Z",
            "gender" => "Male",
            "appointmentGenderPreference" => null,
            "firstAppointmentDateTime" => "2018-08-29T06:45:58Z",
            "referredBy" => null,
            "isProspect" => false,
            "isCompany" => false,
            "isLiabilityReleased" => true,
            "liabilityAgreementDateTime" => "2018-08-29T06:45:58Z",
            "homeLocation" => 1,
            "clientNumberOfVisitsAtSite" => 2,
            "indexes" => [
                [
                    "indexName" => "LongtermGoal",
                    "indexValue" => "IncreasedFlexibility",
                ],
            ],
            "sendPromotionalEmails" => true,
            "sendScheduleEmails" => true,
            "sendAccountEmails" => true,
            "sendPromotionalTexts" => false,
            "sendScheduleTexts" => false,
            "sendAccountTexts" => false,
        ])->assertOk();

        tenancy()->initialize($tenant);

        $this->assertCount(0, Client::all());
    }


    private function getFakeData($overrides)
    {
        return [
            "messageId" => "ASwFMoA2Q5UKw69g3RDbvU",
            "eventId" => "client.created",
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
