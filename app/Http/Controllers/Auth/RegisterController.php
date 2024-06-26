<?php

namespace App\Http\Controllers\Auth;

use App\Actions\SyncRolesAfterRegistrationAction;
use App\Billing\PaymentGateway;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\User;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Tenant;

class RegisterController extends Controller
{
    public function index()
    {
        return view('auth.register');
    }

    public function store(RegistrationRequest $request)
    {
        $subdomain = sprintf('%s.%s', $request->subdomain, $request->server->get('HTTP_HOST'));

        // Creating a new tenant automatically creates a new database
        // for the tenant named as: "tenant_tenant-id-goes-here".
        // We are removing the option to automatically migrate and
        // see the database so we have control over the timing.
        config()->set('tenancy.seed_after_migration', false);

        $tenant = Tenant::create([$subdomain], [
            'name' => $request->business_name,
            'phone' => $request->business_phone,
            'email' => $request->business_email,
        ]);

        tenancy()->initialize($tenant);

        // Create a customer for this organization on Stripe.com
        $stripeCustomer = $this->createNewAccount($request);

        // Add the stripe customer Id to the tenant table.
        $tenant->put('stripe_id', $stripeCustomer->id);

        // Create a new user based on the user that signed up.
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->admin_email,
            'password' => Hash::make($request->password),
        ]);

        // Seed the roles and permissions.
        app(SyncRolesAfterRegistrationAction::class)->onQueue('long-running')->execute($tenant->id, $user->id);

        // Send them to the link to login.
        return redirect(route('login'))->tenant($subdomain);
    }

    protected function createNewAccount($request)
    {
        return $this->createCustomerOnStripe($request);
    }

    protected function createCustomerOnStripe($request)
    {
        $paymentGateway = app(PaymentGateway::class);

        return $paymentGateway->createCustomer($request->payment_token, ['email' => $request->business_email]);
    }
}
