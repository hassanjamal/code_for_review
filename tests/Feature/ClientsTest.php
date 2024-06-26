<?php

namespace Tests\Feature;

use App\Client;
use App\FormTemplate;
use App\Location;
use App\PlatformAPI\Mindbody\FakeMindbodyGateway;
use App\PlatformAPI\PlatformGateway;
use App\Property;
use App\Staff;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_client_index_route_is_not_accessible_to_guests()
    {
        $this->createTenant();

        $this->get(routeForTenant('clients.index'))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function the_index_route_route_is_not_accessible_to_authenticated_users_without_proper_permissions()
    {
        $this->createTenant();
        $user = factory(User::class)->create();

        $this->actingAs($user)->get(routeForTenant('clients.index'))
            ->assertStatus(403);
    }

    /** @test */
    public function the_index_route_route_is_not_accessible_to_authenticated_staff_without_proper_permissions()
    {
        $this->createTenant();
        $user = factory(Staff::class)->create();

        $this->actingAs($user)->get(routeForTenant('clients.index'))
            ->assertStatus(403);
    }

    /** @test */
    public function the_index_route_route_is_accessible_to_authenticated_users_with_proper_permissions()
    {
        $this->createTenant();
        $user = factory(User::class)->create();
        $user->givePermissionTo('clients:view-from-all-properties');

        $this->actingAs($user)->get(routeForTenant('clients.index'))
            ->assertOk()
            ->assertComponentIs('Clients/Index');
    }

    /** @test */
    public function the_index_route_route_is_accessible_to_authenticated_staff_with_proper_permissions()
    {
        $this->createTenant();
        $user = factory(Staff::class)->state('staff')->create();

        $this->actingAs($user)->get(routeForTenant('clients.index'))
            ->assertOk()
            ->assertComponentIs('Clients/Index');
    }

    /** @test */
    public function clients_are_passed_to_the_view_as_props()
    {
        $this->createTenant();
        $user = factory(Staff::class)->state('staff')->create();

        $clientA = factory(Client::class)->create(['property_id' => $user->property_id]);
        $clientB = factory(Client::class)->create(['property_id' => $user->property_id]);

        $this->actingAs($user)->get(routeForTenant('clients.index'))
            ->assertOk()
            ->assertComponentIs('Clients/Index')
            ->assertHasProp('clients')
            ->assertPropCount('clients', 2);
    }

    /** @test */
    public function visible_properties_for_the_staff_member_are_passed_in_as_props()
    {
        $this->withoutExceptionHandling();
        $this->createTenant();
        $staff = factory(Staff::class)->create();
        $staff->givePermissionTo(['properties:view-own', 'locations:view-own', 'clients:view-from-all-properties']);

        $propertyNotBelongingToStaff = factory(Property::class)->create(['api_identifier' => 16134]);

        $this->assertFalse($propertyNotBelongingToStaff->is($staff->property));

        $this->actingAs($staff)->get(routeForTenant('clients.index'))
            ->assertOk()
            ->assertComponentIs('Clients/Index')
            ->assertHasProp('visibleProperties')
            ->assertPropCount('visibleProperties', 1);
    }

    /** @test */
    public function it_can_search_by_client_name()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->state('staff')->create();
        $clientA = factory(Client::class)->create(['first_name' => 'foo', 'last_name' => 'bar']);
        $clientB = factory(Client::class)->create(['first_name' => 'fizz', 'last_name' => 'buzz']);

        $location = factory(Location::class)->state('subscribed')->create();

        $this
            ->actingAs($staff)
            ->get(routeForTenant('clients.index', ['search' => 'foo bar']))
            ->assertOk()
            ->assertPropValue('clients', function ($prop) use ($clientA) {
                $this->assertCount(1, $prop['data']);

                // Assert the client in the response is clientA
                $this->assertEquals($clientA->api_public_id, $prop['data'][0]['api_public_id']);
            })->assertComponentIs('Clients/Index');

        $this
            ->actingAs($staff)
            ->get(routeForTenant('clients.index', ['search' => 'fizz buzz']))
            ->assertOk()
            ->assertPropValue('clients', function ($prop) use ($clientB) {
                $this->assertCount(1, $prop['data']);

                // Assert the client in the response is clientB
                $this->assertEquals($clientB->api_public_id, $prop['data'][0]['api_public_id']);
            })->assertComponentIs('Clients/Index');
    }

    /** @test */
    public function it_can_filter_by_property()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->state('staff')->create();
        $staff->givePermissionTo('clients:view-from-all-properties');

        $clientA = factory(Client::class)->create(['property_id' => $staff->property_id]);
        $clientB = factory(Client::class)->create([
            'property_id' => factory(Property::class)->create(['api_identifier' => '1'])->id,
        ]);

        $this
            ->actingAs($staff)
            ->get(routeForTenant('clients.index', ['property' => $clientA->property_id]))
            ->assertOk()
            ->assertPropValue('clients', function ($prop) use ($clientA) {
                $this->assertCount(1, $prop['data']);

                // Assert the client in the response is clientA
                $this->assertEquals($clientA->api_public_id, $prop['data'][0]['api_public_id']);
            })->assertComponentIs('Clients/Index');

        $this
            ->actingAs($staff)
            ->get(routeForTenant('clients.index', ['property' => $clientB->property_id]))
            ->assertOk()
            ->assertPropValue('clients', function ($prop) use ($clientB) {
                $this->assertCount(1, $prop['data']);

                // Assert the client in the response is clientB
                $this->assertEquals($clientB->api_public_id, $prop['data'][0]['api_public_id']);
            })->assertComponentIs('Clients/Index');
    }

    /** @test */
    public function it_passes_all_intake_forms_as_prop_to_client_detail_page()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->state('staff')->create();

        $client = factory(Client::class)->create(['property_id' => $staff->property_id]);
        $form = factory(FormTemplate::class)->create();

        $this
            ->actingAs($staff)
            ->get(routeForTenant('clients.show', ['client' => $client->id]))
            ->assertOk()
            ->assertPropValue('formTemplates', function ($prop) use ($client, $form) {
                $this->assertCount(1, $prop);
                $this->assertEquals(FormTemplate::select(['id','name'])->get()->toArray(), $prop);
            })
        ->assertComponentIs('Clients/Show');
    }

    /** @test */
    public function can_sync_clients_from_the_api()
    {
        $this->createTenant();

        app()->instance(PlatformGateway::class, new FakeMindbodyGateway());

        $staff = factory(Staff::class)->state('staff')->create();

        $location = factory(Location::class)->state('subscribed')->create(['property_id' => Property::first()->id]);

        $this->assertCount(0, Client::all());

        $this
            ->actingAs($staff)
            ->from(routeForTenant('clients.index'))
            ->post(routeForTenant('clients.sync'))
            ->assertRedirect(routeForTenant('clients.index'));

        $this->assertTrue(count(Client::all()) > 0);
    }
    /** @test */
    public function will_not_sync_clients_for_properties_with_no_active_subscriptions()
    {
        $this->createTenant();

        app()->instance(PlatformGateway::class, new FakeMindbodyGateway());

        $staff = factory(Staff::class)->state('staff')->create();

        $propertyA = $staff->property; //-99787
        $this->assertTrue($propertyA->locations()->active()->count() > 0);

        $propertyB = factory(Property::class)->state('no-locations')->create(['api_identifier' => 16134]);
        $locationB = factory(Location::class)->create(['property_id' => $propertyB->id]);
        $this->assertCount(0, $propertyB->locations()->active()->get());

        $this->assertCount(0, Client::all());

        $this
            ->actingAs($staff)
            ->from(routeForTenant('clients.index'))
            ->post(routeForTenant('clients.sync'))
            ->assertRedirect(routeForTenant('clients.index'));

        $this->assertTrue(count(Client::wherePropertyId($propertyA->id)->get()) > 0);
        $this->assertCount(0, Client::wherePropertyId($propertyB->id)->get());
    }
}
