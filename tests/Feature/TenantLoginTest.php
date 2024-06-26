<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class TenantLoginTest extends TestCase
{
    use RefreshDatabase;

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
    public function a_tenant_user_can_login()
    {
        $this->createTenant();

        $user = factory(User::class)->create();

        $this->post(routeForTenant('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(routeForTenant('dashboard'));
    }

    /** @test */
    public function an_authenticated_tenant_user_cannot_see_login_page()
    {
        $this->createTenant();

        $user = factory(User::class)->create();
        $this->actingAs($user)->get(routeForTenant('login'))->assertRedirect(routeForTenant('dashboard'));
    }

    /** @test */
    public function it_blocks_a_user_after_five_unsuccessful_login_attemps()
    {
        $this->createTenant();

        $user = factory(User::class)->create();

        for ($i = 0; $i < 5; ++$i) {
            $response = $this->post(routeForTenant('login'), [
                'email' => $user->email,
                'password' => 'wrong',
            ]);

            $response->assertRedirect('/');
        }

        $this->post(routeForTenant('login'), [
            'email' => $user->email,
            'password' => 'wrong',
        ]);

        $this->assertTrue(Str::contains(session('errors')->get('email')[0], 'Too many login attempts. Please try again in'));
    }

    /** @test */
    public function a_user_with_same_email_can_belong_to_two_different_org_but_can_login_to_org_they_belong()
    {
        $fooTenant = $this->createTenant([
            'domains' => 'foo.qn2020.test',
            'name' => 'foo',
            'email' => 'john@foo.com',
        ]);
        $userOrgFoo = factory(User::class)->create([
            'password' => bcrypt('fooPass'),
        ]);

        $barTenant = $this->createTenant([
            'domains' => 'bar.qn2020.test',
            'name' => 'bar',
            'email' => 'john@bar.com',
        ]);
        $userOrgBar = factory(User::class)->create([
            'password' => bcrypt('barPass'),
        ]);

        // Current connection is with bar tenant.
        // Log in bar user and ensure they go to their dashboard.
        $this->from('http://bar.qn2020.test/login')->post(routeForTenant('login'), [
            'email' => $userOrgBar->email,
            'password' => 'barPass',
        ])->assertRedirect(routeForTenant('dashboard'));

        Auth::logout();

        // Now no tenant is logged in.
        // Attempt to log into bar domain with foo user's credentials.
        $this->from('http://bar.qn2020.test/login')->post(routeForTenant('login'), [
            'email' => $userOrgFoo->email,
            'password' => 'fooPass',
        ])->assertRedirect('http://bar.qn2020.test/login');

        tenancy()->initialize($fooTenant);

        $this->post(routeForTenant('login'), [
            'email' => $userOrgFoo->email,
            'password' => 'fooPass',
        ])->assertRedirect(routeForTenant('dashboard'));

        Auth::logout();

        $this->from('http://foo.qn2020.test/login')->post(routeForTenant('login'), [
            'email' => $userOrgBar->email,
            'password' => 'barPass',
        ])->assertRedirect('http://foo.qn2020.test/login');
    }

    /** @test */
    public function once_authenticated_tenant_user_goto_their_dashboard_and_see_site_related_info()
    {
        $this->createTenant();

        $user = factory(User::class)->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@acme.com',
        ]);

        $this->actingAs($user)->get(routeForTenant('login'))->assertRedirect(routeForTenant('dashboard'));

        $admin = User::where('email', 'john.doe@acme.com')->first();

        $this->assertEquals('John', $admin->first_name);
        $this->assertEquals('Doe', $admin->last_name);
    }

    /**
     * @test
     * @dataProvider EmailInputValidation
     * @dataProvider PasswordInputValidation
     * @param $formInput
     * @param $formInputValue
     */
    public function test_form_validation($formInput, $formInputValue)
    {
        $this->createTenant();

        $user = factory(User::class)->create();

        $response = $this->json('POST', routeForTenant('login'), [
            $formInput => $formInputValue,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($formInput);
    }

    public function EmailInputValidation()
    {
        return [
            'Eail is required' => ['email', ''],
            'Email is string' => ['email', ['some', 'any']],
        ];
    }

    public function PasswordInputValidation()
    {
        return [
            'Password is required' => ['password', ''],
            'Password is string' => ['password', ['any','some']],
        ];
    }
}
