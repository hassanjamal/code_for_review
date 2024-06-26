<?php

namespace Tests\Unit\Actions;

use App\Actions\SyncClientsToPropertyAction;
use App\Client;
use App\Exceptions\PropertyNotVerifiedException;
use App\PlatformAPI\Mindbody\FakeMindbodyGateway;
use App\PlatformAPI\PlatformGateway;
use App\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncClientsToPropertyActionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        app()->instance(PlatformGateway::class, new FakeMindbodyGateway);
    }

    /** @test */
    public function if_property_is_not_verified_an_exception_is_thrown()
    {
        $this->createTenant();

        $property = factory(Property::class)->state('not-verified')->create();

        $this->assertFalse($property->verified);

        $this->expectException(PropertyNotVerifiedException::class);

        app(SyncClientsToPropertyAction::class)
            ->execute($property->id);
    }

    /** @test */
    public function new_clients_are_created_for_the_property()
    {
        $this->createTenant();

        $property = factory(Property::class)->create();

        $this->assertCount(0, Client::all());

        app(SyncClientsToPropertyAction::class)->execute($property->id);

        $clients = Client::all();

        $this->assertGreaterThan(0, $clients->count());
    }

    /** @test */
    public function if_a_client_exists_it_will_be_updated()
    {
        $this->createTenant();

        // Create a property
        $property = factory(Property::class)->create(['api_identifier' => -99787]);

        // Sync the clients.
        app(SyncClientsToPropertyAction::class)->execute($property->id);

        // Fake a change to a client to determine if it is updated later.
        $clients = Client::get();
        $originalCount = $clients->count();
        $originalName = $clients->first()->first_name;
        $clients->first()->update(['first_name' => 'New Name']);

        $this->assertEquals('New Name', $clients->first()->first_name);

        // Sync again to test the system does not create new staff members.
        app(SyncClientsToPropertyAction::class)->execute($property->id);

        $clients = Client::get();

        // This asserts that the location was updated.
        $this->assertEquals($originalName, $clients->first()->first_name);
        $this->assertEquals($originalCount, $clients->count());
    }

    protected function getExpectedClientValuesForProperty($property)
    {
        $gatewayClients = (new FakeMindbodyGateway())->getclients('-99787');

        return $gatewayClients->each(function ($client) use ($property) {
            $client->property_id = $property->id;
        });
    }
}
