<?php

namespace App\Billing;

use App\Exceptions\BillingException;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentMethod;
use Stripe\Stripe;
use Stripe\Subscription;

class StripePaymentGateway implements PaymentGateway
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret_key'));
    }

    public function createCustomer($token, $data)
    {
        try {
            return Customer::create([
                "email" => $data["email"],
                "source" => $token,
            ]);
        } catch (ApiErrorException $e) {
            throw new BillingException($e->getMessage());
        }
    }

    public function getCustomer($id)
    {
        try {
            return Customer::retrieve($id);
        } catch (ApiErrorException $e) {
            throw new BillingException($e->getMessage());
        }
    }

    public function deleteCustomer($customerId)
    {
        try {
            $customer = Customer::retrieve($customerId);
            return $customer->delete();
        } catch (ApiErrorException $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function updateCustomer($customerId, $data = [])
    {
        try {
            Customer::update(
                $customerId,
                [
                    'email' => $data['email'],
                    'description' => $data['description'],
                ]
            );
        } catch (ApiErrorException $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function createPaymentMethod($type, $token)
    {
        try {
            $paymentMethod = PaymentMethod::create([
                'type' => 'card',
                'card' => [
                    'token' => $token,
                ],
            ]);
            return $paymentMethod;
        } catch (ApiErrorException $e) {
            throw new BillingException($e->getMessage());
        }
    }

    public function getPaymentMethod($id)
    {
        try {
            return PaymentMethod::retrieve($id);
        } catch (ApiErrorException $e) {
            throw new BillingException($e->getMessage());
        }
    }

    public function getCustomerPaymentMethods($customerId, $type)
    {
        // If no more PaymentMethods are available, the resulting array will be empty.
        // This request should never throw an error.
        $allPaymentMethods = PaymentMethod::all([
            'customer' => $customerId,
            'type' => $type,
        ]);

        return collect($allPaymentMethods->data);
    }

    public function attachPaymentMethodToCustomer($paymentMethodId, $customerId)
    {
        try {
            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
            $allPaymentMethods = $paymentMethod->attach(['customer' => $customerId]);
            return $allPaymentMethods;
        } catch (ApiErrorException $e) {
            throw new BillingException($e->getMessage());
        }
    }

    public function detachPaymentMethod($paymentMethodId)
    {
        try {
            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
            $detachedPaymentMethod = $paymentMethod->detach();
            return $detachedPaymentMethod;
        } catch (ApiErrorException $e) {
            throw new BillingException($e->getMessage());
        }
    }

    public function createCard($customerId, $token)
    {
        try {
            $card = Customer::createSource(
                $customerId,
                [
                    'source' => $token,
                ]
            );
            return $card;
        } catch (ApiErrorException $e) {
            throw new BillingException($e->getMessage());
        }
    }

    public function getCard($customerId, $cardId)
    {
        try {
            return Customer::retrieveSource(
                $customerId,
                $cardId
            );
        } catch (ApiErrorException $e) {
            throw new BillingException($e->getMessage());
        }
    }

    public function getCards($customerId)
    {
        try {
            $cards = Customer::allSources(
              $customerId,
              [
                  'limit' => 3,
                  'object' => 'card',
              ]);
            return collect($cards->data);
        } catch (ApiErrorException $e) {
            throw new BillingException($e->getMessage());
        }
    }

    public function getPlan($planId)
    {
        try {
            return \Stripe\Plan::retrieve($planId);
        } catch (ApiErrorException $e) {
            throw new BillingException($e->getMessage());
        }
    }

    public function retrieveSubscription($id)
    {
        try {
            return Subscription::retrieve($id);
        } catch (ApiErrorException $e) {
            throw new BillingException($e->getMessage());
        }
    }

    public function createSubscription($customerId, $planId)
    {
        try {
            $subscription = Subscription::create([
                "customer" => $customerId,
                "items" => [
                    [
                        "plan" => $planId,
                    ],
                ],
            ]);

            return $subscription;
        } catch (ApiErrorException $e) {
            throw new BillingException($e->getMessage());
        }
    }

    public function updateSubscription($subscriptionId, array $params)
    {
        try {
            return Subscription::update($subscriptionId, $params);
        } catch (ApiErrorException $e) {
            throw new BillingException($e->getMessage());
        }
    }

    public function cancelSubscriptionAtPeriodEnd($subscriptionId)
    {
        return $this->updateSubscription($subscriptionId, ['cancel_at_period_end' => true]);
    }

    public function cancelSubscriptionNow($subscriptionId)
    {
        try {
            return $this->retrieveSubscription($subscriptionId)->cancel();
        } catch (ApiErrorException $e) {
            throw new BillingException($e->getMessage());
        }
    }

    public function renewSubscription($subscriptionId)
    {
        return $this->updateSubscription($subscriptionId, [
            'cancel_at_period_end' => false,
        ]);
    }
}
