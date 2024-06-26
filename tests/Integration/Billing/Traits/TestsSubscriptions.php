<?php

namespace Tests\Integration\Billing\Traits;

use App\Exceptions\BillingException;

trait TestsSubscriptions
{
    /** @test */
    function it_can_create_a_subscription()
    {
        $customer = $this->paymentGateway->createCustomer('tok_visa', ['email' => 'hs.jamal@gmail.com']);
        $subscription = $this->paymentGateway->createSubscription($customer->id, $this->planId);

        $this->assertEquals($subscription->customer, $customer->id);
        $this->assertEquals(collect($subscription->items->data)->first()->plan->id, $this->planId);
    }

    /** @test */
    function it_can_retrieve_the_plan()
    {
        $plan = $this->paymentGateway->getPlan($this->planId);

        $this->assertEquals($plan->id, $this->planId);
    }

    /** @test */
    public function it_can_retrieve_a_subscription()
    {
        $customer = $this->paymentGateway->createCustomer('tok_visa', ['email' => 'drfraker@gmail.com']);
        $subscription = $this->paymentGateway->createSubscription($customer, $this->planId);

        $actual = $this->paymentGateway->retrieveSubscription($subscription->id);

        $this->assertEquals($subscription->id, $actual->id);
    }

    /** @test */
    public function it_can_cancel_a_subscription_immediately()
    {
        $customer = $this->paymentGateway->createCustomer('tok_visa', ['email' => 'drfraker@gmail.com']);
        $subscription = $this->paymentGateway->createSubscription($customer, $this->planId);

        $this->assertNull($subscription->canceled_at);
        $this->assertEquals('active', $subscription->status);

        $cancelledSubscription = $this->paymentGateway->cancelSubscriptionNow($subscription->id);

        $this->assertNotNull($cancelledSubscription->canceled_at);
        $this->assertEquals('canceled', $cancelledSubscription->status);
        $this->assertFalse($subscription->cancel_at_period_end);
    }

    /** @test */
    public function it_can_cancel_a_subscription_at_period_end()
    {
        $customer = $this->paymentGateway->createCustomer('tok_visa', ['email' => 'drfraker@gmail.com']);
        $subscription = $this->paymentGateway->createSubscription($customer, $this->planId);

        $this->assertNull($subscription->canceled_at);
        $this->assertEquals('active', $subscription->status);

        $cancelledSubscription = $this->paymentGateway->cancelSubscriptionAtPeriodEnd($subscription->id);

        $this->assertNotNull($cancelledSubscription->canceled_at);
        $this->assertEquals('active', $cancelledSubscription->status);
        $this->assertTrue($cancelledSubscription->cancel_at_period_end);
    }

    /** @test */
    public function it_can_renew_a_cancelled_subscription()
    {
        $customer = $this->paymentGateway->createCustomer('tok_visa', ['email' => 'drfraker@gmail.com']);
        $subscription = $this->paymentGateway->createSubscription($customer, $this->planId);

        $cancelledSubscription = $this->paymentGateway->cancelSubscriptionAtPeriodEnd($subscription->id);

        $this->assertNotNull($cancelledSubscription->canceled_at);
        $this->assertEquals('active', $cancelledSubscription->status);
        $this->assertTrue($cancelledSubscription->cancel_at_period_end);

        $renewed = $this->paymentGateway->renewSubscription($subscription->id);

        $this->assertNull($renewed->canceled_at);
        $this->assertEquals('active', $renewed->status);
        $this->assertFalse($renewed->cancel_at_period_end);
    }

    /** @test */
    public function it_throws_an_error_if_the_subscription_does_not_exist_when_retrieving()
    {
        $this->expectException(BillingException::class);

        $this->paymentGateway->retrieveSubscription('invalid-id');
    }
}
