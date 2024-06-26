<?php

namespace Tests\Integration\Billing\Traits;

use App\Exceptions\BillingException;
use Exception;

trait TestsPaymentMethods
{
    /** @test */
    public function it_can_create_a_payment_method()
    {
        $paymentMethod = $this->paymentGateway->createPaymentMethod('card', 'tok_visa');

        $this->assertEquals('payment_method', $paymentMethod->object);
        $this->assertEquals('4242', $paymentMethod->card->last4);
        $this->assertEquals('visa', $paymentMethod->card->brand);
    }

    /** @test */
    public function it_throws_an_error_when_create_payment_method_fails()
    {
        try {
            $this->paymentGateway->createPaymentMethod('card', 'tok_invalid');

            $this->fail('The billing exception was not thrown.');
        } catch (Exception $e) {
            if (! $e instanceof BillingException) {
                throw $e;
            }

            $this->assertEquals('Invalid token id: tok_invalid', $e->getMessage());
        }
    }

    /** @test */
    public function it_can_retrieve_the_payment_method()
    {
        $paymentMethod = $this->paymentGateway->createPaymentMethod('card', 'tok_visa');
        $actual = $this->paymentGateway->getPaymentMethod($paymentMethod->id);

        $this->assertEquals($paymentMethod->id, $actual->id);
    }

    /** @test */
    public function it_throws_an_error_when_payment_method_is_not_found()
    {
        try {
            $this->paymentGateway->getPaymentMethod('invalid-payment-method-id');

            $this->fail('The proper exception was not thrown.');
        } catch (Exception $e) {
            if (! $e instanceof BillingException) {
                throw $e;
            }

            $this->assertEquals('No such PaymentMethod: invalid-payment-method-id', $e->getMessage());
        }
    }

    /** @test */
    public function it_can_attach_a_payment_method_to_a_customer()
    {
        $customer = $this->paymentGateway->createCustomer('tok_visa', ['email' => 'hs.jamal@gmail.com']);

        $cardToAdd = $this->paymentGateway->createPaymentMethod('card', 'tok_mastercard');

        $this->paymentGateway->attachPaymentMethodToCustomer($cardToAdd->id, $customer->id);

        $customerCards = $this->paymentGateway->getCustomerPaymentMethods($customer->id, 'card');

        $this->assertCount(2, $customerCards);
        $this->assertTrue($customerCards->pluck('id')->contains($cardToAdd->id));
    }

    /** @test */
    public function it_creates_a_payment_method_and_attaches_it_to_a_customer_whenever_a_new_customer_is_created()
    {
        $customer = $this->paymentGateway->createCustomer('tok_visa', ['email' => 'hs.jamal@gmail.com']);
        $customerPaymentMethods = $this->paymentGateway->getCustomerPaymentMethods($customer->id, 'card');

        $this->assertEquals($customerPaymentMethods->first()['customer'], $customer->id);
    }

    /** @test */
    public function it_removes_a_payment_method_from_a_customer()
    {
        $customer = $this->paymentGateway->createCustomer('tok_visa', ['email' => 'hs.jamal@gmail.com']);

        $cards = $this->paymentGateway->getCustomerPaymentMethods($customer->id, 'card');

        $this->assertCount(1, $cards);

        $this->paymentGateway->detachPaymentMethod($cards->first()['id']);

        $cards = $this->paymentGateway->getCustomerPaymentMethods($customer->id, 'card');

        $this->assertEmpty($cards);
    }
}
