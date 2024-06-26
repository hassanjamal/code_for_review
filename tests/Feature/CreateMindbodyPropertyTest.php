<?php

namespace Tests\Feature;

use App\Actions\CreateMindbodyPropertyAction;
use App\PlatformAPI\Mindbody\FakeMindbodyGateway;
use App\PlatformAPI\PlatformGateway;
use App\Property;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @see \App\Http\Requests\CreateMindbodyPropertyRequest */
/** @see \App\Http\Controllers\CreateMindbodyPropertiesController */
class CreateMindbodyPropertyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()->instance(PlatformGateway::class, new FakeMindbodyGateway);
    }

    /** @test */
    public function the_endpoint_to_store_a_new_property_is_not_accessible_by_guests()
    {
        $this->createTenant();
        $this->post(routeForTenant('mindbody.properties.store'))
            ->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function when_the_form_is_submitted_the_action_runs_and_we_are_redirected_to_tenant_dashboard()
    {
        $this->createTenant();
        $user = factory(User::class)->create();

        $spy = $this->spy(CreateMindbodyPropertyAction::class);

        $this->actingas($user)->post(routeForTenant('mindbody.properties.store'), ['api_identifier' => -99787, 'name' => 'Test Name'])
            ->assertRedirect(routeForTenant('properties.index'))
            ->assertSessionHas('success', 'Property created.');

        $spy->shouldHaveReceived('execute')->with(-99787, 'Test Name');
    }

    /** @test */
    public function when_the_site_id_is_invalid_and_the_action_throws_an_exception_no_property_should_be_created_and_there_should_be_errors_in_the_session()
    {
        $this->createTenant();
        $user = factory(User::class)->create();

        $this->actingas($user)->from(routeForTenant('properties.create'))->post(routeForTenant('mindbody.properties.store'), ['api_identifier' => 'invalid-site', 'name' => 'Test Name'])
            ->assertRedirect(routeForTenant('properties.create'))
            ->assertSessionHasErrors(['api_identifier' => 'The property could not be added. Please check your Site ID.']);

        $this->assertCount(0, Property::all());
    }

    /** @test */
    public function validates_required_fields()
    {
        $this->createTenant();
        $user = factory(User::class)->create();

        // Site Id is required.
        $this->actingas($user)
            ->post(routeForTenant('mindbody.properties.store'), ['name' => 'Test Name'])
            ->assertSessionHasErrors('api_identifier');

        // Name for property is required.
        $this->actingas($user)
            ->post(routeForTenant('mindbody.properties.store'), ['api_identifier' => -9787])
            ->assertSessionHasErrors('name');
    }
}
