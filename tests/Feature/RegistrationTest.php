<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Stancl\Tenancy\Tenant;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var FakePaymentGateway
     */
    protected $paymentGateway;

    public function setUp(): void
    {
        parent::setUp();

        Bus::fake();
        $this->paymentGateway = new FakePaymentGateway();
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    /** @test */
    public function register_page_is_viewable_by_guest()
    {
        $this->assertTrue(auth()->guest());

        $this->get(route('register'))->assertOk()->assertViewIs('auth.register');
    }

    /** @test */
    public function it_creates_a_tenant_and_an_admin_user_when_registration_is_successful()
    {
        $formData = $this->getValidRegisterFormData();

        $response = $this->post(route('register'), $formData);

        $response->assertRedirect(routeForTenant('login'));

        $this->assertSame($formData['business_name'], tenant()->get('name'));
        $this->assertSame($formData['business_email'], tenant()->get('email'));
        $this->assertSame($formData['business_phone'], tenant()->get('phone'));

        $this->assertDatabaseHas('users', [
            'first_name' => 'john',
            'last_name' => 'doe',
            'email' => 'john@example.com',
        ]);

        $this->assertTrue(Hash::check('secret', User::first()->password));
    }

    /** @test */
    public function upon_successful_registration_a_new_stripe_customer_is_created()
    {
        $this->post(route('register'), $this->getValidRegisterFormData());

        $this->assertEquals('cus_FvdWzNL9DjX3fM', tenant()->stripe_id);
    }

    /** @test */
    public function when_an_tenant_is_registered_a_tenant_database_is_created()
    {
        $this->post(route('register'), $this->getValidRegisterFormData());

        $this->assertEquals(tenant()->getDatabaseName(), DB::getDatabaseName());
    }

    /**
     * @test
     * @dataProvider businessNameInputValidation
     * @dataProvider businessEmailInputValidation
     * @dataProvider businessPhoneInputValidation
     * @dataProvider firstNameInputValidation
     * @dataProvider lastNameInputValidation
     * @dataProvider adminEmailInputValidation
     * @dataProvider paymentTokenInputValidation
     * @dataProvider subdomainInputValidation
     * @param $formInput
     * @param $formInputValue
     */
    public function test_form_validation($formInput, $formInputValue)
    {
        if ($formInputValue === 'existing-domain') {
            // Create another tenant with the same domain to ensure duplicates are not allowed
            Tenant::create([$formInputValue], ['name' => 'foo', 'phone' => 'foo', 'email' => 'foo']);
        }

        $response = $this->json('POST', route('register', [
            $formInput => $formInputValue,
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($formInput);
    }

    public function businessNameInputValidation()
    {
        return [
            'Business Name is required' => ['business_name', ''],
            'Business Name is string' => ['business_name', ['some', 'any']],
            'Business Name is less than 256 characters' => ['business_name', Str::random(256)],
        ];
    }

    public function subdomainInputValidation()
    {
        return [
            'Subdomain is required' => ['subdomain', ''],
            'Subdomain must be unique' => ['subdomain', 'existing-domain'],
        ];
    }

    public function businessEmailInputValidation()
    {
        return [
            'Business Email is required' => ['business_email', ''],
            'Business Email is string' => ['business_email', ['some'.'any']],
            'Business Email is valid' => ['business_email', 'not-a-valid-business-email'],
            'Business Email is less than 256 characters' => ['business_email', Str::random(256).'@example.com'],
        ];
    }

    public function businessPhoneInputValidation()
    {
        return [
            'Business Phone is required' => ['business_phone', ''],
        ];
    }

    public function firstNameInputValidation()
    {
        return [
            'First Name is required' => ['first_name', ''],
            'First Name is string' => ['first_name', ['some'.'any']],
            'First Name is less than 256 characters' => ['first_name', Str::random(256)],
        ];
    }

    public function lastNameInputValidation()
    {
        return [
            'Last Name is required' => ['last_name', ''],
            'Last Name is string' => ['last_name', ['some'.'any']],
            'Last Name is less than 256 characters' => ['last_name', Str::random(256)],
        ];
    }

    public function adminEmailInputValidation()
    {
        return [
            'Admin Email is required' => ['admin_email', ''],
            'Admin Email is string' => ['admin_email', ['some'.'any']],
            'Admin Email is valid' => ['admin_email', 'not-a-valid-email'],
            'Admin Email is less than 256 characters' => ['admin_email', Str::random(256).'@example.com'],
        ];
    }

    public function paymentTokenInputValidation()
    {
        return [
            'Payment token is required' => ['payment_token', ''],
        ];
    }

    private function getValidRegisterFormData($overrides = [])
    {
        return array_merge([
            'business_name' => 'biz name',
            'business_email' => 'biz@example.com',
            'business_phone' => '444-444-4444',
            'subdomain' => 'acme',
            'first_name' => 'john',
            'last_name' => 'doe',
            'admin_email' => 'john@example.com',
            'password' => 'secret',
            'payment_token' => 'tok_visa',
        ], $overrides);
    }
}
