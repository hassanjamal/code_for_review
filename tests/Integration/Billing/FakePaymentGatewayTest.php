<?php

namespace Tests\Integration;

use App\Billing\FakePaymentGateway;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    use ContractTests;

    /**
     * @var \App\Billing\PaymentGateway
     */
    private $paymentGateway;

    private $planId;

    public function setUp(): void
    {
        parent::setUp();
        $this->paymentGateway = new FakePaymentGateway();
        $this->planId = config('services.stripe.subscription_plan_id');
    }
}
