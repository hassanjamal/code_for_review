<?php

namespace Tests\Integration;

use App\Billing\PaymentGateway;
use Tests\TestCase;

/** @group wifi */
class StripePaymentGatewayTest extends TestCase
{
    use ContractTests;

    /**
     * @var PaymentGateway
     * */
    private $paymentGateway;

    private $planId;

    public function setUp(): void
    {
        parent::setUp();
        $this->paymentGateway = app(PaymentGateway::class);
        $this->planId = config('services.stripe.subscription_plan_id');
    }
}
