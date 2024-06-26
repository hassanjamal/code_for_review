<?php

namespace Tests\Feature;

use App\PlatformAPI\Mindbody\FakeMindbodyGateway;
use App\PlatformAPI\PlatformGateway;
use App\Property;
use App\Staff;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantMindbodyLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()->instance(PlatformGateway::class, new FakeMindbodyGateway);
    }

    /** @test */
    public function it_redirects_to_home_if_subdomain_does_not_exists()
    {
        $this->get('http://acme.qn2020.test/login')->assertRedirect(config('app.url'));
    }

    /** @test */
    public function a_tenant_can_view_login_page()
    {
        $this->createTenant();

        $this->get(routeForTenant('login'))
            ->assertOk()
            ->assertComponentIs('Auth/Login');
    }

    /** @test */
    public function a_tenant_user_with_valid_mindbody_credentials_can_login()
    {
        $this->withoutExceptionHandling();

        $this->createTenant();

        factory(Property::class)->create();

        $response = $this->post(routeForTenant('login.mindbody'), [
            'username' => 'valid-test-staff',
            'password' => '@tempPW1234',
            'apiIdentifier' => '-99787',
        ])->assertRedirect(routeForTenant('dashboard'));

        $allStaff = Staff::all();
        $authStaff = Staff::first();

        $response->assertSessionHas('api_access_token', $authStaff->api_access_token);

        $this->assertCount(1, $allStaff);
    }

    /** @test */
    public function when_a_staff_logs_in_and_details_on_their_platform_have_changed_they_are_updated_in_the_database()
    {
        $this->withoutExceptionHandling();
        $this->createTenant();
        $property = factory(Property::class)->create(['api_identifier' => -99787]);

        $originalStaff = factory(Staff::class)->create([
            'api_id' => 100000005, // Same ID as user 'valid-test-staff'.
            'property_id' => $property->id,
            'first_name' => 'john',
            'last_name' => 'doe',
            'api_role' => 'staff',
            'api_access_token' => 'original-token',
        ]);


        $this->post(routeForTenant('login.mindbody'), [
            'username' => 'valid-test-staff', // Log in using the credentials of 'valid-test-staff' user'.
            'password' => '@tempPW1234',
            'apiIdentifier' => '-99787',
        ]);

        // Assert the staff member was updated.
        $this->assertDatabaseHas('staff', [
            'id' => $originalStaff->id,
            "api_id" => '100000005',
            "first_name" => "valid",
            "last_name" => "test-staff",
            "api_role" => "staff",
            "api_access_token" => "access-token",
        ]);
    }

    /** @test */
    public function an_authenticated_tenant_user_cannot_see_login_page()
    {
        tenancy()->create('acme.qn2020.test', ['name' => 'foo', 'phone' => '444', 'email' => 'email']);
        tenancy()->init('acme.qn2020.test');

        $user = factory(User::class)->create();
        $this->actingAs($user)->get(routeForTenant('login'))->assertRedirect(routeForTenant('dashboard'));
    }

    /** @test */
    public function the_username_is_required()
    {
        $this->createTenant();

        $this->post(routeForTenant('login.mindbody'), [
            'username' => null,
            'password' => 'secret',
            'apiIdentifier' => 'foo',
        ])->assertSessionHasErrors('username');
    }

    /** @test */
    public function the_password_is_required()
    {
        $this->createTenant();

        $this->post(routeForTenant('login.mindbody'), [
            'username' => 'foo',
            'password' => null,
            'apiIdentifier' => 'foo',
        ])->assertSessionHasErrors('password');
    }

    /** @test */
    public function the_site_id_is_required()
    {
        $this->createTenant();

        $this->post(routeForTenant('login.mindbody'), [
            'username' => 'foo',
            'password' => 'secret',
            'apiIdentifier' => null,
        ])->assertSessionHasErrors('apiIdentifier');
    }

    /** @test */
    public function it_redirects_back_to_login_page_if_wrong_site_id_is_provided_with_error_message()
    {
        $this->createTenant();

        $response = $this->post(routeForTenant('login.mindbody'), [
            'username' => 'valid-test-staff',
            'password' => '@tempPW1234',
            'apiIdentifier' => 'invalid-site_id',
        ])->assertRedirect(routeForTenant('login'));

        $response->assertSessionHasErrors('message', 'foo');
        $this->assertEquals('The site id is not valid.', session('errors')->first('message'));
    }

    /** @test */
    public function it_redirects_back_to_login_page_if_wrong_password_is_provided_with_error_message()
    {
        $this->createTenant();

        $response = $this->post(routeForTenant('login.mindbody'), [
            'username' => 'valid-test-staff',
            'password' => 'wrong-password',
            'apiIdentifier' => '-99787',
        ])->assertRedirect(routeForTenant('login'));

        $response->assertSessionHasErrors('message', 'foo');
        $this->assertEquals('Authentication failed.', session('errors')->first('message'));
    }

    /** @test */
    public function it_redirects_back_to_login_page_if_wrong_username_is_provided_with_error_message()
    {
        $this->withoutExceptionHandling();
        $this->createTenant();

        $response = $this->post(routeForTenant('login.mindbody'), [
            'username' => 'in-valid-test-staff',
            'password' => '@tempPW1234',
            'apiIdentifier' => '-99787',
        ])->assertRedirect(routeForTenant('login'));

        $response->assertSessionHasErrors('message', 'foo');
        $this->assertEquals('Authentication failed.', session('errors')->first('message'));
    }
}
