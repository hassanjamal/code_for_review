<?php

namespace Tests\Integration\Billing\Traits;

use App\Exceptions\BillingException;

trait TestsCreditCards
{

    /** @test */
    public function it_can_create_a_card()
    {
        /*
         * When you create a new credit card, you must specify a customer or recipient on which to create it.
         * If the card’s owner has no default card, then the new card will become the default.
         * However, if the owner already has a default, then it will not change.
         */

        $customer = $this->paymentGateway->createCustomer('tok_visa', ['email' => 'hs.jamal@gmail.com']);
        $card = $this->paymentGateway->createCard($customer->id, 'tok_mastercard');

        $this->assertEquals('card', $card->object);
        $this->assertEquals('4444', $card->last4);
        $this->assertEquals('MasterCard', $card->brand);
        $this->assertEquals($customer->id, $card->customer);
    }

    /** @test */
    public function it_can_retrieve_the_card()
    {
        $customer = $this->paymentGateway->createCustomer('tok_visa', ['email' => 'hs.jamal@gmail.com']);
        $card = $this->paymentGateway->createCard($customer->id, 'tok_mastercard');
        $actual = $this->paymentGateway->getCard($customer->id, $card->id);

        $this->assertEquals($card->id, $actual->id);
    }

    /** @test */
    public function it_throws_an_error_when_card_is_not_found()
    {
        try {
            $customer = $this->paymentGateway->createCustomer('tok_visa', ['email' => 'hs.jamal@gmail.com']);
            $card = $this->paymentGateway->getCard($customer->id, 'invalid-card');

            $this->fail('The proper exception was not thrown.');
        } catch (BillingException $e) {
            $this->assertEquals('No such source: invalid-card', $e->getMessage());
        }
    }

    /** @test */
    function it_creates_a_card_when_a_customer_is_added()
    {
        $customer = $this->paymentGateway->createCustomer('tok_visa', ['email' => 'hs.jamal@gmail.com']);
        $allCards = $this->paymentGateway->getCards($customer->id);

        $this->assertEquals($allCards->count(), 1);
        $this->assertEquals($allCards->where('brand', 'Visa')->first()['last4'], '4242');
    }

    /** @test */
    function it_can_get_all_cards_for_a_customer()
    {
        $customer = $this->paymentGateway->createCustomer('tok_visa', ['email' => 'hs.jamal@gmail.com']);
        $card = $this->paymentGateway->createCard($customer->id, 'tok_mastercard');

        $allCards = $this->paymentGateway->getCards($customer->id);

        $this->assertEquals($allCards->count(), 2);
        $this->assertEquals($allCards->where('brand', 'MasterCard')->first()['last4'], '4444');
        $this->assertEquals($allCards->where('brand', 'Visa')->first()['last4'], '4242');
    }

    /** @test */
    function it_creates_a_card_when_a_customer_is_added_and_created_card_is_default_card()
    {
        $customer = $this->paymentGateway->createCustomer('tok_visa', ['email' => 'hs.jamal@gmail.com']);
        $card = $this->paymentGateway->createCard($customer->id, 'tok_mastercard');

        $allCards = $this->paymentGateway->getCards($customer->id);

        $this->assertEquals($allCards->count(), 2);
        $this->assertEquals($allCards->where('brand', 'Visa')->first()['id'], $customer->default_source);
    }
}
