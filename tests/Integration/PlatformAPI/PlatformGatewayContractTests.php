<?php

namespace Tests\Integration\PlatformAPI;

use App\AccessToken;
use App\Exceptions\PlatformGatewayException;
use App\Property;
use App\Staff;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

trait PlatformGatewayContractTests
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_activation_code()
    {
        $activationCode = collect($this->platformGateway->getActivationCode(16134));

        $this->assertTrue($activationCode->keys()->contains('code'));
        $this->assertNotNull(data_get($activationCode, 'code'));

        $this->assertTrue($activationCode->keys()->contains('link'));
        $this->assertNotNull(data_get($activationCode, 'link'));
    }

    /** @test */
    public function it_throws_an_exception_if_user_tries_get_activation_code_for_a_invalid_site()
    {
        try {
            $this->platformGateway->getActivationCode('invalid-site');

            $this->fail('The proper exception was not thrown');
        } catch (\Exception $e) {
            $this->assertInstanceOf(PlatformGatewayException::class, $e);
            $this->assertEquals('Something went wrong while getting the activation code.', $e->getMessage());
            $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getCode());
        }
    }

    /** @test */
    public function it_can_authenticate_app_and_store_access_token_in_database()
    {
        $this->createTenant();

        $access = $this->platformGateway->authenticateApp(-99787);
        $actual = AccessToken::ForSite(-99787)->first();

        $this->assertEquals($access->token, $actual->token);
    }

    /** @test */
    public function it_can_authenticate_app_and_update_access_token_in_database()
    {
        $this->createTenant();

        $existingAccess = AccessToken::updateOrCreate(
            ['site_id' => -99787],
            ['token' => 'some-old-access-token']
        );
        $newAccess = $this->platformGateway->authenticateApp(-99787);
        $actualAccess = AccessToken::ForSite(-99787)->first();

        $this->assertNotEquals($existingAccess->token, $newAccess->token);
        $this->assertEquals($actualAccess->token, $newAccess->token);
    }

    /** @test */
    public function it_can_return_a_token_stored_in_database_if_token_is_valid()
    {
        $this->createTenant();

        $existingAccess = AccessToken::updateOrCreate(
            ['site_id' => -99787],
            ['token' => 'access-token']
        );
        $newAccess = $this->platformGateway->authenticateApp(-99787);

        $this->assertEquals($existingAccess->token, $newAccess->token);
    }

    /** @test */
    public function it_can_authenticate_a_staff_member_with_valid_credentials()
    {
        $this->createTenant();

        $staff = $this->platformGateway->authenticateStaff(-99787, 'valid-test-staff', '@tempPW1234');

        $this->assertInstanceOf(Staff::class, $staff);

        $this->assertEquals(100000005, $staff->api_id);
        $this->assertEquals('valid', $staff->first_name);
        $this->assertEquals('test-staff', $staff->last_name);
        $this->assertEquals('staff', $staff->api_role);
        $this->assertNotNull($staff->api_access_token);
    }

    /** @test */
    public function invalid_login_attempt_returns_an_exception()
    {
        $this->withoutExceptionHandling();
        $this->createTenant();

        try {
            $this->platformGateway->authenticateStaff(-99787, 'invalid-test-staff', '@tempPW1234');
            $this->fail('Authorization passed with invalid credentials');
        } catch (\Exception $e) {
            $this->assertInstanceOf(PlatformGatewayException::class, $e);
            $this->assertEquals('Authentication failed.', $e->getMessage());
            $this->assertEquals(Response::HTTP_UNAUTHORIZED, $e->getCode());
        }
    }

    /** @test */
    public function it_can_get_a_single_staff_member_from_a_site()
    {
        $this->createTenant();

        $staff = $this->platformGateway->getStaffMember(-99787, 2);

        $this->assertInstanceOf(Staff::class, $staff);
        $this->assertEquals('2', $staff->api_id);
        $this->assertEquals('Dr. Quinn', $staff->first_name);
        $this->assertEquals('Medicine Woman', $staff->last_name);
        $this->assertNull($staff->api_access_token);
    }

    /** @test */
    public function it_can_get_multiple_staff_members_by_id()
    {
        $this->createTenant();

        $staff = $this->platformGateway->getStaffMembers(-99787, [2, 100000002]);

        $this->assertInstanceOf(Collection::class, $staff);

        $this->assertCount(2, $staff);

        $this->assertSame(
            [
                'api_id' => 2,
                'first_name' => 'Dr. Quinn',
                'last_name' => 'Medicine Woman',
                'api_access_token' => null,
            ],
            $staff->where('api_id', 2)->first()->setAppends([])->toArray()
        );

        $this->assertSame(
            [
                'api_id' => 100000002,
                'first_name' => 'Dr. Foo',
                'last_name' => 'Bar',
                'api_access_token' => null,
            ],
            $staff->where('api_id', 100000002)->first()->setAppends([])->toArray()
        );
    }

    /** @test */
    public function it_can_get_all_staff_members_from_a_site()
    {
        $this->createTenant();

        $staff = $this->platformGateway->getStaffMembers(-99787); // If no ids are passed to the method, all staff are returned.

        $this->assertInstanceOf(Collection::class, $staff);

        $this->assertTrue($staff->count() > 1);

        $this->assertSame(
            [
                'api_id' => 2,
                'first_name' => 'Dr. Quinn',
                'last_name' => 'Medicine Woman',
                'api_access_token' => null,
            ],
            $staff->where('api_id', 2)->first()->setAppends([])->toArray()
        );

        $this->assertSame(
            [
                'api_id' => 100000002,
                'first_name' => 'Dr. Foo',
                'last_name' => 'Bar',
                'api_access_token' => null,
            ],
            $staff->where('api_id', 100000002)->first()->setAppends([])->toArray()
        );
    }

    /** @test */
    public function it_throws_an_exception_when_a_staffMember_of_invalid_site_is_being_fetched()
    {
        $this->createTenant();

        try {
            $this->platformGateway->getStaffMember('in-valid-site-id', 2);

            $this->fail('The proper exception was not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(PlatformGatewayException::class, $e);
            $this->assertEquals('The site id is not valid.', $e->getMessage());
            $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getCode());
        }
    }

    /** @test */
    public function it_throws_an_exception_when_staffMembers_of_invalid_site_are_being_fetched()
    {
        $this->createTenant();

        try {
            $this->platformGateway->getStaffMembers('invalid-site-id');

            $this->fail('The proper exception was not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(PlatformGatewayException::class, $e);
            $this->assertEquals('The site id is not valid.', $e->getMessage());
            $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getCode());
        }
    }

    /** @test */
    public function it_can_get_locations()
    {
        $this->createTenant();

        $locations = $this->platformGateway->getLocations(16134);

        $this->assertInstanceOf(Collection::class, $locations);

        $this->assertCount(2, $locations);

        $this->assertSame(
            [
                'api_id' => 2,
                'name' => 'QuickerNotes Sandbox Site 2',
                'address' => '234 East Main Street',
                'address_2' => 'Bozeman MT 59715',
                'phone' => '4444444444',
                'city' => 'Bozeman',
                'state_province' => 'MT',
                'postal_code' => '59715',
                'latitude' => 45.6791405,
                'longitude' => -111.0331703,
                'is_subscribed' => false,
                'subscription' => null,
            ],
            $locations->where('api_id', 2)->first()->toArray()
        );

        $this->assertSame(
            [
                'api_id' => 1,
                'name' => 'QuickerNotes Sandbox Site 3',
                'address' => '123 East Main Street',
                'address_2' => 'Bozeman MT 59715',
                'phone' => '4444444444',
                'city' => 'Bozeman',
                'state_province' => 'MT',
                'postal_code' => '59715',
                'latitude' => 45.6793256,
                'longitude' => -111.0348995,
                'is_subscribed' => false,
                'subscription' => null,
            ],
            $locations->where('api_id', 1)->first()->toArray()
        );
    }

    /** @test */
    public function it_throws_an_exception_when_locations_of_invalid_site_is_being_fetched()
    {
        $this->createTenant();

        try {
            $this->platformGateway->getStaffMembers('in-valid-site-id');

            $this->fail('The proper exception was not thrown');
        } catch (Exception $e) {
            $this->assertInstanceOf(PlatformGatewayException::class, $e);
            $this->assertEquals('The site id is not valid.', $e->getMessage());
            $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getCode());
        }
    }

    /** @test */
    public function it_can_get_clients()
    {
        $this->createTenant();
        $property = factory(Property::class)->create(['api_identifier' => -99787]);
        $clients = $this->platformGateway->getClients($property->api_identifier);

        $this->assertInstanceOf(Collection::class, $clients);

        $angelina = $clients->where('api_id', 100000000)->first();

        $this->assertEquals(1, $angelina->property_id);
        $this->assertEquals(100000000, $angelina->api_id);
        $this->assertEquals('new', $angelina->api_public_id);
        $this->assertEquals('Angelina', $angelina->first_name);
        $this->assertEquals('Jolie', $angelina->last_name);
        $this->assertEquals('Female', $angelina->gender);
        $this->assertEquals(0, $angelina->membership_icon);
        $this->assertEquals('drfraker@gmail.com', $angelina->email);
        $this->assertEquals(Carbon::parse('1969-02-02'), $angelina->birth_date);
        $this->assertEquals(null, $angelina->referred_by);
        $this->assertEquals(Carbon::parse('2015-05-12T00:00:00'), $angelina->first_appointment_date);
        $this->assertTrue(Str::contains($angelina->photo_url, 'https://clients.mindbodyonline.com/studios/DEMOQUICKERNOTESDUSTINF/clients/100000000_large.jpg'));
        $this->assertEquals('Active', $angelina->status);
        $this->assertEquals('1:100000000', $angelina->id);
    }

    /** @test */
    public function it_can_get_paginated_data_for_get_clients()
    {
        $this->createTenant();
        $property = factory(Property::class)->create();

        // Get the first result.
        $firstOnly = $this->platformGateway->withPagination(1, 0)->getClients(-99787);
        $this->assertCount(1, $firstOnly);
        $this->assertEquals('Acme Inc', $firstOnly->first()->first_name);
        $this->assertResponseContainsPagination($firstOnly);

        // Get the second result.
        $secondOnly = $this->platformGateway->withPagination(1, 1)->getClients(-99787);

        $this->assertCount(1, $secondOnly);
        $this->assertEquals('Jessica', $secondOnly->first()->first_name);

        // Get first and second.
        $both = $this->platformGateway->withPagination(2, 0)->getClients(-99787);

        $this->assertCount(2, $both);

        $this->assertEquals('Acme Inc', $both[0]->first_name);
        $this->assertEquals('Jessica', $both[1]->first_name);
    }

    /** @test */
    public function it_can_get_session_types()
    {
        $this->createTenant();

        factory(Property::class)->create([
            'api_identifier' => -99787,
        ]);

        $sessionTypes = $this->platformGateway->getSessionTypes(-99787);

        $this->assertCount(8, $sessionTypes);

        $this->assertSame('Advanced Yoga', Arr::get($sessionTypes, 9));
        $this->assertSame('Initial Consultation (60mins)', Arr::get($sessionTypes, 10));
    }

    /** @test */
    public function can_get_and_cache_membership_info()
    {
        $this->createTenant();

        $memberships = $this->platformGateway->getMemberships(-99787);

        $this->assertEquals($memberships[0]['id'], 3);
        $this->assertEquals($memberships[0]['name'], 'Red Membership');

        $this->assertEquals($memberships[1]['id'], 202);
        $this->assertEquals($memberships[1]['name'], 'blue membership');


        $this->assertTrue(cache()->has('-99787:memberships'));
    }

    /** @test */
    public function it_can_get_appointments()
    {
        $this->createTenant();
        $property = factory(Property::class)->create(['api_identifier' => -99787]);

        $appointments = $this->platformGateway->getAppointments($property->api_identifier, Carbon::parse('November 14, 2019'), Carbon::parse('November 22, 2019'));

        $this->assertInstanceOf(Collection::class, $appointments);

        $this->assertCount(5, $appointments);

        $appointment = $appointments->where('api_id', 70507)->first();

        $this->assertSame([
            'id' => makeTripleCompositeKey($property->id, 2, 70507),
            'api_id' => '70507',
            'property_id' => $property->id,
            'location_api_id' => 2,
            'location_id' => 2,
            'client_api_public_id' => 'new',
            'staff_api_id' => 2,
            'staff_id' => '1:2',
            'duration' => 15,
            'status' => 'Arrived',
            'start_date_time' => '2019-11-14 15:30:00',
            'end_date_time' => '2019-11-14 15:45:00',
            'notes' => 'running late',
            'staff_requested' => false,
            "service_id" => 6,
            'service_name' => 'Outpatient Visit New',
            'room_name' => null,
            'first_appointment' => false,
        ], $appointment->setAppends([])->toArray());

        // Assert that the room name is populated.
        $this->assertEquals('Yoga Room', $appointments->firstWhere('api_id', 70510)->room_name);
    }

    /** @test */
    public function it_can_get_appointments_for_a_specific_staff_member()
    {
        $this->createTenant();
        $property = factory(Property::class)->create(['api_identifier' => -99787]);

        $appointments = $this->platformGateway->getAppointments($property->api_identifier, Carbon::parse('November 14, 2019'), Carbon::parse('November 22, 2019'), [2]);

        $appointments->each(function ($appointment) {
            $this->assertEquals(2, $appointment->staff_api_id);
        });
    }

    /** @test */
    public function it_can_get_appointments_for_a_specific_client()
    {
        $this->createTenant();
        $property = factory(Property::class)->create(['api_identifier' => -99787]);

        $appointments = $this->platformGateway->getAppointments($property->api_identifier, Carbon::parse('November 14, 2019'), Carbon::parse('November 22, 2019'), [], 'new');

        $appointments->each(function ($appointment) {
            $this->assertEquals('new', $appointment->client_api_public_id);
        });
    }

    /** @test */
    public function it_can_get_appointments_for_a_specific_location()
    {
        $this->createTenant();
        $property = factory(Property::class)->create(['api_identifier' => -99787]);

        $appointments = $this->platformGateway->getAppointments($property->api_identifier, Carbon::parse('November 14, 2019'), Carbon::parse('November 22, 2019'), [], null, [1]);

        $appointments->each(function ($appointment) {
            $this->assertEquals(1, $appointment->location_api_id);
        });
    }

    /** @test */
    public function it_can_get_appointments_for_specific_appointment_ids()
    {
        $this->createTenant();
        $property = factory(Property::class)->create(['api_identifier' => -99787]);

        $appointments = $this->platformGateway->getAppointments($property->api_identifier, now()->parse('november 14, 2019'), now()->parse('november 20, 2019'), [], null, [], [70507, 70508]);

        $appointments->each(function ($appointment) {
            $this->assertTrue(in_array($appointment->api_id, [70507, 70508]));
        });
    }

    /** @test */
    public function it_can_get_paginated_data_for_get_appointments()
    {
        $this->createTenant();
        $property = factory(Property::class)->create(['api_identifier' => -99787]);

        // Get the first result.
        $firstOnly = $this->platformGateway->withPagination(1, 0)->getAppointments($property->api_identifier, now()->subYears(100), now());
        $this->assertCount(1, $firstOnly);
        $this->assertResponseContainsPagination($firstOnly);

        // Get the second result.
        $secondOnly = $this->platformGateway->withPagination(1, 1)->getAppointments($property->api_identifier, now()->subYears(100), now());
        $this->assertCount(1, $secondOnly);

        // Get first and second.
        $both = $this->platformGateway->withPagination(2, 0)->getAppointments($property->api_identifier, now()->subYears(100), now());

        $this->assertCount(2, $both);

        $this->assertEquals($firstOnly->first()->api_id, $both[0]->api_id);
        $this->assertEquals($secondOnly->first()->api_id, $both[1]->api_id);
    }

    /** @test */
    public function can_validate_site_id_based_on_tenant()
    {
        // given I have a tenant with active property.
        $tenant = $this->createTenant(['site_ids' => [-987, 123]]);

        // It can tell if the call is for a valid site ID.
        $this->assertTrue($this->platformGateway->validateSiteId(-987));
        $this->assertTrue($this->platformGateway->validateSiteId(123));

        $this->expectException(PlatformGatewayException::class);
        $this->platformGateway->validateSiteId(1234);
    }

    /** @test */
    public function can_create_or_update_appointment_notes()
    {
        $this->withoutExceptionHandling();
        $this->createTenant();

        $property = factory(Property::class)->create(['api_identifier' => -99787]);

        $appointment = $this->platformGateway->getAppointments($property->api_identifier, now()->parse('January 1st, 2000'), now(), [], null, [], [70508])->first();

        $newNote = Str::random(10);
        $appointment->notes = $newNote;

        $refreshedAppointment = $this->platformGateway->updateAppointment($property->api_identifier, $appointment);

        $this->assertEquals($newNote, $refreshedAppointment->notes);
    }

    protected function assertResponseContainsPagination($response)
    {
        $this->assertArrayHasKey('RequestedLimit', $response->pagination);
        $this->assertArrayHasKey('RequestedOffset', $response->pagination);
        $this->assertArrayHasKey('PageSize', $response->pagination);
        $this->assertArrayHasKey('TotalResults', $response->pagination);
    }
}
