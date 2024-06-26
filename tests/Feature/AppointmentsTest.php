<?php

namespace Tests\Feature;

use App\Appointment;
use App\Client;
use App\Location;
use App\PlatformAPI\PlatformGateway;
use App\Property;
use App\Staff;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AppointmentsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function the_index_page_is_not_viewable_by_guests()
    {
        $this->createTenant();

        $this->assertTrue(auth()->guest());

        $this->get(routeForTenant('appointments.index'))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function the_index_page_is_not_viewable_by_authenticated_users_without_proper_permissions()
    {
        $this->createTenant();
        $user = factory(User::class)->create();

        $this->actingAs($user)->get(routeForTenant('appointments.index'))->assertStatus(403);
    }

    /** @test */
    public function the_index_page_is_visible_to_authenticated_staff_member_with_appointments_permissions()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->state('staff')->create();

        $this->actingAs($staff)->get(routeForTenant('appointments.index'))->assertOk();
    }

    /** @test */
    public function the_index_page_returns_a_list_of_appointments_belonging_to_the_property()
    {
        Carbon::setTestNow('December 12, 2019 3:00 PM');
        $this->createTenant();
        $staff = factory(Staff::class)->state('staff')->create();
        $property = $staff->property;

        $appointmentA = factory(Appointment::class)->create([
            'property_id' => $property->id,
            'location_id' => $property->locations[0]->id,
            'start_date_time' => now(),
        ]);

        $appointmentB = factory(Appointment::class)->create([
            'property_id' => $property->id,
            'location_id' => $property->locations[1]->id,
            'start_date_time' => now()->addHour(),
        ]);

        $appointments = Appointment::orderBy('start_date_time')->get();

        $this->actingAs($staff)->get(routeForTenant('appointments.index'))->assertOk()
             ->assertPropValue('appointments', function ($prop) use ($appointments) {
                 $this->assertEquals($appointments[0]->id, $prop['data'][0]['id']);
                 $this->assertEquals($appointments[1]->id, $prop['data'][1]['id']);
             })->assertComponentIs('Appointments/Index');
    }

    /** @test */
    public function it_can_set_the_date_and_remember_the_value()
    {
        $this->createTenant();
        Carbon::setTestNow('March 24, 2020 12:00 pm');
        $staff = factory(Staff::class)->state('staff')->create();

        // When the date is not set on the request it gets set to today automatically.
        $this->actingAs($staff)->get(routeForTenant('appointments.index'));
        $this->assertEquals(session()->get('appointment-index-date'), now()->toDateString());

        //When the date is set on the request it is added as the session date.
        $this->actingAs($staff)->get(routeForTenant('appointments.index', ['date' => '2020-03-08']));
        $this->assertEquals(session()->get('appointment-index-date'), '2020-03-08');
    }

    /** @test */
    public function it_can_filter_appointments_by_date()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->state('staff')->create();
        $location = factory(Location::class)->state('subscribed')->create();

        $onFilterDate = factory(Appointment::class)->create([
            'property_id' => $location->property->id,
            'location_id' => $location->id,
            'start_date_time' => $filterDate = now()->parse('December 23, 2019')->toDateString(),
        ]);

        $notOnFilterDate = factory(Appointment::class)->create([
            'property_id' => $location->property->id,
            'location_id' => $location->id,
            'start_date_time' => now()->parse('December 24, 2019')->toDateTimeString(),
        ]);

        $this->actingAs($staff)->get(routeForTenant('appointments.index', ['date' => $filterDate]))->assertOk()
             ->assertPropValue('appointments', function ($prop) use ($onFilterDate) {
                 $this->assertCount(1, $prop['data']);
                 $this->assertEquals($onFilterDate->id, $prop['data'][0]['id']);
             })->assertComponentIs('Appointments/Index');
    }

    /** @test */
    public function it_can_filter_appointments_by_status()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->state('staff')->create();
        $location = factory(Location::class)->state('subscribed')->create();

        $statusBooked = factory(Appointment::class)->create([
            'property_id' => $location->property->id,
            'location_id' => $location->id,
            'status' => 'Booked',
            'start_date_time' => now(),
        ]);

        $statusComplete = factory(Appointment::class)->create([
            'property_id' => $location->property->id,
            'location_id' => $location->id,
            'status' => 'Completed',
            'start_date_time' => now(),
        ]);

        $this->actingAs($staff)->get(routeForTenant('appointments.index', ['status' => 'Booked']))->assertOk()
             ->assertPropValue('appointments', function ($prop) use ($statusBooked) {
                 $this->assertCount(1, $prop['data']);
                 $this->assertEquals($statusBooked->id, $prop['data'][0]['id']);
             })->assertComponentIs('Appointments/Index');
    }

    /** @test */
    public function it_can_filter_appointments_by_staff()
    {
        $this->createTenant();
        $staffWithApiId100 = factory(Staff::class)->state('staff')->create(['api_id' => '100']);
        $location = factory(Location::class)->state('subscribed')->create();

        $staffAppointment = factory(Appointment::class)->create([
            'property_id' => $location->property->id,
            'location_id' => $location->id,
            'staff_api_id' => $staffWithApiId100->api_id,
        ]);

        $notStaffAppointment = factory(Appointment::class)->create([
            'property_id' => $location->property->id,
            'location_id' => $location->id,
            'staff_api_id' => '123',
        ]);

        // With staff filter
        $this->actingAs($staffWithApiId100)
             ->get(routeForTenant('appointments.index', ['staff' => $staffWithApiId100->api_id]))->assertOk()
             ->assertPropValue('appointments', function ($prop) {
                 $this->assertCount(1, $prop['data']);
                 $this->assertEquals('100', $prop['data'][0]['staff_api_id']);
             })->assertComponentIs('Appointments/Index');
    }

    /** @test */
    public function it_shows_all_appointments_when_no_staff_is_passed()
    {
        Carbon::setTestNow('December 12, 2019 3:00 PM');
        $this->createTenant();
        $location = factory(Location::class)->state('subscribed')->create();
        $staffWithApiId100 = factory(Staff::class)->state('staff')->create([
            'api_id' => '100',
            'property_id' => $location->property_id,
        ]);

        $staffAppointment = factory(Appointment::class)->create([
            'property_id' => $location->property->id,
            'location_id' => $location->id,
            'staff_api_id' => $staffWithApiId100->api_id,
            'start_date_time' => now(),
            'end_date_time' => now()->addMinutes(15),
        ]);

        $notStaffAppointment = factory(Appointment::class)->create([
            'property_id' => $location->property->id,
            'location_id' => $location->id,
            'staff_api_id' => '123',
            'start_date_time' => now()->addMinutes(20),
            'end_date_time' => now()->addMinutes(35),
        ]);

        // Without staff filter
        $this->actingAs($staffWithApiId100)->get(routeForTenant('appointments.index', ['staff' => null]))->assertOk()
             ->assertPropValue('appointments', function ($prop) {
                 $this->assertCount(2, $prop['data']);
                 $this->assertEquals('100', $prop['data'][0]['staff_api_id']);
                 $this->assertEquals('123', $prop['data'][1]['staff_api_id']);
             })->assertComponentIs('Appointments/Index');
    }

    /** @test */
    public function it_can_search_by_client_name()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->state('staff')->create();
        $clientA = factory(Client::class)->create(['first_name' => 'foo', 'last_name' => 'bar']);
        $clientB = factory(Client::class)->create(['first_name' => 'fizz', 'last_name' => 'buzz']);

        $location = factory(Location::class)->state('subscribed')->create();

        $appointmentA = factory(Appointment::class)->create([
            'property_id' => $location->property->id,
            'location_id' => $location->id,
            'staff_api_id' => $staff->api_id,
            'client_api_public_id' => $clientB->api_public_id,
        ]);

        $appointmentB = factory(Appointment::class)->create([
            'property_id' => $location->property->id,
            'location_id' => $location->id,
            'staff_api_id' => $staff->api_id,
            'client_api_public_id' => $clientA->api_public_id,
        ]);

        $this->actingAs($staff)->get(routeForTenant('appointments.index', ['search' => 'foo bar']))->assertOk()
             ->assertPropValue('appointments', function ($prop) use ($clientA) {
                 $this->assertCount(1, $prop['data']);

                 // Assert the client in the response is clientA
                 $this->assertEquals($clientA->api_public_id, $prop['data'][0]['client_api_public_id']);
             })->assertComponentIs('Appointments/Index');

        $this->actingAs($staff)->get(routeForTenant('appointments.index', ['search' => 'fizz buzz']))->assertOk()
             ->assertPropValue('appointments', function ($prop) use ($clientB) {
                 $this->assertCount(1, $prop['data']);

                 // Assert the client in the response is clientB
                 $this->assertEquals($clientB->api_public_id, $prop['data'][0]['client_api_public_id']);
             })->assertComponentIs('Appointments/Index');
    }

    /** @test */
    public function staff_can_see_active_locations_from_their_home_property()
    {
        // Given I have two properties with 2 locations each, 1 active with a subscription.
        $this->createTenant();
        $propertyA = factory(Property::class)->state('no-locations')->create();
        $propertyA->locations()->save(factory(Location::class)->state('subscribed')->create());
        $this->assertCount(1, $propertyA->locations);
        $propertyB = factory(Property::class)->create(['api_identifier' => '1234']);
        $this->assertCount(2, $propertyB->locations);

        $staff = factory(Staff::class)->state('staff')->create(['property_id' => $propertyA->id]);

        // A staff member should only be able to see locations that are active from their home property.
        $this->actingAs($staff)->get(routeForTenant('appointments.index', ['staff' => null]))->assertOk()
             ->assertPropValue('visibleLocations', function ($prop) use ($propertyA) {
                 $this->assertCount(1, $prop);
                 $this->assertEquals($propertyA->locations->first()->id, $prop[0]['id']);
             })->assertComponentIs('Appointments/Index');
    }

    /** @test */
    public function staff_with_proper_permissions_can_see_active_locations_all_properties()
    {
        Carbon::setTestNow('December 12, 2019 3:00 PM');
        $this->createTenant();
        // Property A has 1 subscribed location.
        $propertyA = factory(Property::class)->state('no-locations')->create();
        $propertyA->locations()->save(factory(Location::class)->state('subscribed')->create());
        $this->assertCount(1, $propertyA->locations);

        // PropertyB has 2 locations one is subscribed.
        $propertyB = factory(Property::class)->create(['api_identifier' => '1234']);
        $propertyB->locations[1]->subscription()->update(['ends_at' => now()->subMonth()]);

        $this->assertCount(2, $propertyB->locations);
        $this->assertCount(1, $propertyB->locations()->active()->get());

        $staff = factory(Staff::class)->create();
        $staff->givePermissionTo(['locations:view-all', 'properties:view-all', 'appointments:view-all']);

        // A Super Admin user should only be able to see locations that are active from all Properties.
        $this->actingAs($staff)->get(routeForTenant('appointments.index', ['staff' => null]))->assertOk()
             ->assertPropValue('visibleLocations', function ($prop) use ($propertyA, $propertyB) {
                 $this->assertCount(2, $prop);
                 // Location from PropertyA
                 $this->assertEquals($propertyA->locations[0]->id, $prop[0]['id']);
                 // Only the subscribed Location from PropertyB
                 $this->assertEquals($propertyB->locations[0]->id, $prop[1]['id']);
             })->assertComponentIs('Appointments/Index');
    }

    /** @test */
    public function it_only_shows_appointments_from_locations_with_active_subscriptions()
    {
        Carbon::setTestNow('December 23, 2019');
        $this->createTenant();

        // Two locations from the same property. One is subscribed.
        $subscribedLocation = factory(Location::class)->state('subscribed')->create();
        $unSubscribedLocation = factory(Location::class)->create(['property_id' => $subscribedLocation->property_id]);
        $staff = factory(Staff::class)->state('staff')->create(['property_id' => $subscribedLocation->property_id]);

        $fromSubscribedLocation = factory(Appointment::class)->create([
            'property_id' => $subscribedLocation->property->id,
            'location_id' => $subscribedLocation->id,
            'start_date_time' => $filterDate = now()->parse('December 23, 2019')->toDateString(),
        ]);

        $fromUnSubscribedLocation = factory(Appointment::class)->create([
            'property_id' => $unSubscribedLocation->property->id,
            'location_id' => $unSubscribedLocation->id,
            'start_date_time' => now()->parse('December 23, 2019')->toDateTimeString(),
        ]);

        $this->actingAs($staff)->get(routeForTenant('appointments.index'))->assertOk()
             ->assertPropValue('appointments', function ($prop) use ($fromSubscribedLocation) {
                 $this->assertCount(1, $prop['data']);
                 $this->assertEquals($fromSubscribedLocation->id, $prop['data'][0]['id']);
             })->assertComponentIs('Appointments/Index');
    }

    /** @test */
    public function staff_can_update_appointment_notes()
    {
        $this->withoutExceptionHandling();

        $this->createTenant();
        $staff = factory(Staff::class)->state('staff')->create();

        // Save it to our database
        $platformGateway = app(PlatformGateway::class);
        $apiAppointment = $platformGateway->getAppointments(-99787, now()->parse('January 1st, 2000'), now())->first();
        $apiAppointment->save();

        $dbAppointment = Appointment::forApiId($apiAppointment->api_id)->first();

        // Update the appointment.
        $this->actingAs($staff)->from(routeForTenant('appointments.index'))->post(routeForTenant('appointments.update', [$dbAppointment]), ['note' => 'Updated note.'])
             ->assertRedirect(routeForTenant('appointments.index'));

        // Check that the API version was updated.
        $this->assertEquals('Updated note.', $platformGateway->getAppointments(-99787, now()->parse('January 1st, 2000'), now(), [], null, [], [$apiAppointment->api_id])->first()->notes);

        // Check that the DB version was updated.
        $this->assertEquals('Updated note.', $dbAppointment->fresh()->notes);
    }
}
