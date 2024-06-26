<?php

namespace Tests\Unit;

use App\Client;
use App\Document;
use App\ProgressNote;
use App\Property;
use App\Staff;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_client_belongs_to_a_property()
    {
        $this->createTenant();

        $property = factory(Property::class)->create();
        $client = factory(Client::class)->create(['property_id' => $property->id]);

        $this->assertTrue($property->is($client->property));
    }

    /** @test */
    public function a_client_can_have_null_birthdate()
    {
        $this->createTenant();

        $client = factory(Client::class)->create([
            'birth_date' => null,
        ]);
        $this->assertNull($client->birth_date);
    }

    /** @test */
    public function a_client_can_have_many_progress_notes()
    {
        $this->createTenant();

        $client = factory(Client::class)->create();

        $noteA = factory(ProgressNote::class)->create([
            'client_id' => $client->id,
        ]);

        $noteB = factory(ProgressNote::class)->create([
            'client_id' => $client->id,
        ]);

        $this->assertTrue($noteA->is($client->progressNotes[0]));
        $this->assertTrue($noteB->is($client->progressNotes[1]));
    }

    /** @test */
    public function a_client_can_have_many_documents()
    {
        $this->createTenant();

        $client = factory(Client::class)->create();

        $documentA = factory(Document::class)->create([
            'client_id' => $client->id,
        ]);

        $documentB = factory(Document::class)->create([
            'client_id' => $client->id,
        ]);

        $this->assertTrue($documentA->is($client->documents[0]));
        $this->assertTrue($documentB->is($client->documents[1]));
    }

    /** @test */
    public function a_client_can_have_a_carbon_date_for_birthday()
    {
        $this->createTenant();

        $client = factory(Client::class)->create([
            'birth_date' => Carbon::now()->subYears(35)->toDateTimeString(),
        ]);
        $this->assertNotNull($client->birth_date);
        $this->assertTrue($client->birth_date instanceof \Illuminate\Support\Carbon);
    }

    /** @test */
    public function a_client_can_have_null_first_appointment_date()
    {
        $this->createTenant();

        $client = factory(Client::class)->create([
            'first_appointment_date' => null,
        ]);
        $this->assertNull($client->first_appointment_date);
    }

    /** @test */
    public function a_client_can_have_a_carbon_date_for_first_appointment_date()
    {
        $this->createTenant();

        $client = factory(Client::class)->create([
            'first_appointment_date' => Carbon::now()->subYears(35)->toDateTimeString(),
        ]);
        $this->assertNotNull($client->first_appointment_date);
        $this->assertTrue($client->first_appointment_date instanceof \Illuminate\Support\Carbon);
    }

    /** @test */
    public function when_a_client_is_created_it_active_by_default_unless_specified()
    {
        $this->createTenant();

        $client = factory(Client::class)->create();
        $client->refresh();

        $this->assertTrue($client->isActive);
    }
    /** @test */
    public function it_encrypts_the_email_before_strong_in_db()
    {
        $this->createTenant();
        $client = factory(Client::class)->create([
            'email' => 'john@doe.com',
        ]);
        $this->assertEquals('john@doe.com', decrypt($client->getRawOriginal('email')));
    }

    /** @test */
    public function global_scope_is_applied_based_on_user_privileges()
    {
        $this->createTenant();

        $propertyA = factory(Property::class)->create(['api_identifier' => '1234']);
        $clientFromPropertyA = factory(Client::class)->create(['property_id' => $propertyA->id]);

        $propertyB = factory(Property::class)->create(['api_identifier' => '5678']);
        $clientFromPropertyB = factory(Client::class)->create(['property_id' => $propertyB->id]);

        // When app is getting clients without auth user, return all clients.
        $noAuth = Client::all();
        $this->assertCount(2, $noAuth);

        $staff = factory(Staff::class)->create(['property_id' => $propertyA->id]);
        $this->be($staff);

        $staffCanViewNoClientsQuery = Client::all();
        $this->assertCount(0, $staffCanViewNoClientsQuery);

        $staff->givePermissionTo(['clients:view-from-own-property']);
        $staffCanViewOwnClientsQuery = Client::all();
        $this->assertCount(1, $staffCanViewOwnClientsQuery);
        $this->assertTrue($clientFromPropertyA->is($staffCanViewOwnClientsQuery[0]));

        $staff->givePermissionTo(['clients:view-from-all-properties']);
        $staffCanViewAllClientsQuery = Client::all();
        $this->assertCount(2, $staffCanViewAllClientsQuery);
        $this->assertTrue($clientFromPropertyA->is($staffCanViewAllClientsQuery[0]));
        $this->assertTrue($clientFromPropertyB->is($staffCanViewAllClientsQuery[1]));
    }
}
