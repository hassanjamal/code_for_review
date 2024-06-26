<?php

namespace Tests\Integration\Billing\Traits;

use App\Exceptions\BillingException;
use Exception;

trait TestsCustomers
{
    /** @test */
    public function it_create_a_customer_on_stripe()
    {
        $customer = $this->paymentGateway->createCustomer('tok_visa', ['email' => 'hs.jamal@gmail.com']);

        $this->assertEquals('hs.jamal@gmail.com', $customer->email);
    }

    /** @test */
    public function to_create_a_customer_a_valid_token_is_required()
    {
        try {
            $this->paymentGateway->createCustomer('in-valid-token', ['email' => 'hs.jamal@gmail.com']);

            $this->fail('The proper exception was not thrown.');
        } catch (Exception $e) {
            if (! $e instanceof BillingException) {
                throw $e;
            }

            $this->assertEquals('No such token: in-valid-token', $e->getMessage());
        }
    }

    /** @test */
    public function it_can_retrieve_a_customer()
    {
        $customer = $this->paymentGateway->createCustomer('tok_visa', ['email' => 'hs.jamal@gmail.com']);

        $actual = $this->paymentGateway->getCustomer($customer->id);

        $this->assertEquals($customer->id, $actual->id);
        $this->assertEquals($customer->email, $actual->email);
    }

    /** @test */
    public function it_throws_an_error_when_customer_is_not_found()
    {
        try {
            $this->paymentGateway->getCustomer('invalid-customer-id');

            $this->fail('The proper exception was not thrown.');
        } catch (Exception $e) {
            if (! $e instanceof BillingException) {
                throw $e;
            }

            $this->assertEquals('No such customer: invalid-customer-id', $e->getMessage());
        }
    }

    /** @test */
    public function it_can_delete_a_customer()
    {
        $customer = $this->paymentGateway->createCustomer('tok_visa', ['email' => 'hs.jamal@gmail.com']);
        $deletedCustomer = $this->paymentGateway->deleteCustomer($customer->id);

        $this->assertTrue($deletedCustomer->deleted);
    }

    /** @test */
    public function it_can_update_a_customer()
    {
        $customer = $this->paymentGateway->createCustomer('tok_visa', ['email' => 'hs.jamal@gmail.com']);
        $this->paymentGateway->updateCustomer($customer->id, ['email' => 'john@example.com', 'description' => 'A supercharged customer']);

        $updatedCustomer = $this->paymentGateway->getCustomer($customer->id);

        $this->assertEquals('john@example.com', $updatedCustomer->email);
        $this->assertEquals('A supercharged customer', $updatedCustomer->description);
    }
}
