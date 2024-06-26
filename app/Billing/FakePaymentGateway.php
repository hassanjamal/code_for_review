<?php

namespace App\Billing;

use App\Exceptions\BillingException;
use Illuminate\Support\Str;

class FakePaymentGateway implements PaymentGateway
{
    public $customers;

    public $paymentMethods;

    public $cards;

    public $plans;

    public $subscriptions;

    public function __construct()
    {
        $this->customers = collect();
        $this->paymentMethods = collect();
        $this->cards = collect();
        $this->plans = collect();
        $this->subscriptions = collect();

        $this->initializeBasePlan();
    }

    public function validateToken($token)
    {
        return collect(['tok_visa', 'tok_mastercard'])->contains($token);
    }

    public function createCustomer($token, $data)
    {
        if (! $this->validateToken($token)) {
            throw new BillingException('No such token: in-valid-token');
        }

        $customer = [
            "id" => "cus_FvdWzNL9DjX3fM",
            "object" => "customer",
            "account_balance" => 0,
            "address" => $data['address'] ?? null,
            "balance" => 0,
            "created" => 1570178104,
            "currency" => "usd",
            "default_source" => null,
            "delinquent" => false,
            "description" => $data['description'] ?? null,
            "discount" => null,
            "email" => $data['email'] ?? null,
            "invoice_prefix" => "4D7BFA1",
            "invoice_settings" => [
                "custom_fields" => null,
                "default_payment_method" => null,
                "footer" => null,
            ],
            "livemode" => false,
            "metadata" => [],
            "name" => $data['name'] ?? null,
            "phone" => $data['phone'] ?? null,
            "preferred_locales" => [],
            "shipping" => null,
            "sources" => [
                "object" => "list",
                "data" => [],
                "has_more" => false,
                "total_count" => 0,
                "url" => "/v1/customers/cus_FvdWzNL9DjX3fM/sources",
            ],
            "subscriptions" => [
                "object" => "list",
                "data" => [],
                "has_more" => false,
                "total_count" => 0,
                "url" => "/v1/customers/cus_FvdWzNL9DjX3fM/subscriptions",
            ],
            "tax_exempt" => "none",
            "tax_ids" => [
                "object" => "list",
                "data" => [],
                "has_more" => false,
                "total_count" => 0,
                "url" => "/v1/customers/cus_FvdWzNL9DjX3fM/tax_ids",
            ],
            "tax_info" => null,
            "tax_info_verification" => null,
        ];

        $paymentMethod = $this->createPaymentMethod('card', $token);
        $this->attachPaymentMethodToCustomer($paymentMethod->id, $customer['id']);

        $card = $this->createCard($customer['id'], $token);
        $customer['default_source'] = $card->id;
        $customer['sources']['data'] = $card;

        $this->customers->push($customer);

        return json_decode(json_encode($customer));
    }

    public function getCustomer($id)
    {
        $customer = $this->customers->where('id', $id)->first();

        if (! $customer) {
            throw new BillingException('No such customer: '.$id);
        }

        return json_decode(json_encode($customer));
    }

    public function deleteCustomer($customerId)
    {
        $customer = $this->getCustomer($customerId);
        if ($customer) {
            $this->customers = $this->customers->reject(function ($customer) use ($customerId) {
                return $customer['id'] == $customerId;
            });
            $deletedCustomer = [
                'id' => $customerId,
                "object" => "customer",
                "deleted" => true,
            ];

            return json_decode(json_encode($deletedCustomer));
        }
        throw new Exception("Customer Not Found", 1);
    }

    public function updateCustomer($customerId, $data = [])
    {
        $customer = $this->getCustomer($customerId);
        if ($customer) {
            $this->customers = $this->customers->map(function ($customer) use ($customerId, $data) {
                if ($customer['id'] == $customerId) {
                    foreach ($data as $key => $value) {
                        $customer[$key] = $value;
                    }
                }

                return $customer;
            });

            return json_decode(json_encode($this->customers->where('id', $customerId)->first()));
        }
        throw new Exception("Customer Not Found", 1);
    }

    public function createPaymentMethod($type, $token)
    {
        if (! $this->validateToken($token)) {
            throw new BillingException('Invalid token id: '.$token);
        }

        $cardInfoFromToken = $this->getCardInfoFromToken($token);
        $paymentMethod = [
            "id" => "pm_".Str::random(24),
            "object" => "payment_method",
            "billing_details" => [
                "address" => [
                    "city" => null,
                    "country" => null,
                    "line1" => null,
                    "line2" => null,
                    "postal_code" => null,
                    "state" => null,
                ],
                "email" => null,
                "name" => null,
                "phone" => null,
            ],
            "card" => [
                "brand" => strtolower($cardInfoFromToken['brand']),
                "checks" => [
                    "address_line1_check" => null,
                    "address_postal_code_check" => null,
                    "cvc_check" => null,
                ],
                "country" => "US",
                "exp_month" => 8,
                "exp_year" => 2020,
                "fingerprint" => "Xt5EWLLDS7FJjR1c",
                "funding" => "credit",
                "generated_from" => null,
                "last4" => $cardInfoFromToken['last4'],
                "three_d_secure_usage" => [
                    "supported" => true,
                ],
                "wallet" => null,
            ],
            "created" => 1570199039,
            "customer" => null,
            "livemode" => false,
            "metadata" => [],
            "type" => $type,
        ];

        $this->paymentMethods->push($paymentMethod);

        return json_decode(json_encode($paymentMethod));
    }

    public function getPaymentMethod($id)
    {
        $paymentMethod = $this->paymentMethods->where('id', $id)->first();

        if (! $paymentMethod) {
            throw new BillingException('No such PaymentMethod: '.$id);
        }

        return json_decode(json_encode($paymentMethod));
    }

    public function getCustomerPaymentMethods($customerId, $type)
    {
        return $this->paymentMethods->filter(function ($card) use ($customerId) {
            if (is_null($card)) {
                return false;
            }
            return $card['customer'] === $customerId;
        });
    }

    public function attachPaymentMethodToCustomer($paymentMethodId, $customerId)
    {
        $this->paymentMethods->transform(function ($card) use ($paymentMethodId, $customerId) {
            if ($card['id'] === $paymentMethodId) {
                $card['customer'] = $customerId;
            }

            return $card;
        });

        $addedCard = $this->paymentMethods->filter(function ($card) use ($paymentMethodId) {
            return $card['id'] === $paymentMethodId;
        })->toJson();

        return json_decode($addedCard);
    }

    public function detachPaymentMethod($paymentMethodId)
    {
        $this->paymentMethods = $this->paymentMethods->map(function ($paymentMethod) use ($paymentMethodId) {
            if ($paymentMethod['id'] == $paymentMethodId) {
                $paymentMethod['customer'] = null;
            }
        });

        return $this->paymentMethods->where('id', $paymentMethodId)->first();
    }

    public function createCard($customerId, $token)
    {
        $cardInfoFromToken = $this->getCardInfoFromToken($token);
        $card = [
            "id" => "card_1FPwsSKUHMNrOayIoZa6OtzL",
            "object" => "card",
            "address_city" => null,
            "address_country" => null,
            "address_line1" => null,
            "address_line1_check" => null,
            "address_line2" => null,
            "address_state" => null,
            "address_zip" => null,
            "address_zip_check" => null,
            "brand" => $cardInfoFromToken['brand'],
            "country" => "US",
            "customer" => $customerId,
            "cvc_check" => null,
            "dynamic_last4" => null,
            "exp_month" => 8,
            "exp_year" => 2020,
            "fingerprint" => "rayrBLir8TocTs9a",
            "funding" => "credit",
            "last4" => $cardInfoFromToken['last4'],
            "metadata" => [],
            "name" => null,
            "tokenization_method" => null,
        ];

        $this->cards->push($card);

        return json_decode(json_encode($card));
    }

    public function getCard($customerId, $cardId)
    {
        $card = $this->cards->where('id', $cardId)->where('customer', $customerId)->first();

        if (! $card) {
            throw new BillingException('No such source: '.$cardId);
        }

        return json_decode(json_encode($card));
    }

    public function getCards($customerId)
    {
        $allCards = $this->cards->where('customer', $customerId)->all();

        if (! $allCards) {
            throw new BillingException('No such customer: '.$customerId);
        }

        return collect($allCards);
    }

    public function getPlan($planId)
    {
        $plan = $this->plans->where('id', $planId)->first();

        if (! $plan) {
            throw new BillingException('No such plan: '.$planId);
        }

        return json_decode(json_encode($plan));
    }

    public function retrieveSubscription($id)
    {
        $subscription = $this->subscriptions->where('id', $id)->first();

        if (! $subscription) {
            throw new BillingException('No Such Subscription: '.$id);
        }

        return json_decode(json_encode($subscription));
    }

    public function createSubscription($customerId, $planId)
    {
        $plan = $this->plans->where('id', $planId)->first();

        $subscription = $this->fakeSubscription($customerId, [
            'plan' => $plan,
        ]);

        $this->subscriptions->push($subscription);

        return json_decode(json_encode($subscription));
    }

    public function updateSubscription($subscriptionId, array $params)
    {
        // TODO: Implement updateSubscription() method.
    }

    public function cancelSubscriptionAtPeriodEnd($subscriptionId)
    {
        $subscription = $this->retrieveSubscription($subscriptionId);

        $subscription->cancel_at_period_end = true;
        $subscription->canceled_at = now()->timestamp;

        return $subscription;
    }

    public function cancelSubscriptionNow($subscriptionId)
    {
        $subscription = $this->retrieveSubscription($subscriptionId);

        $subscription->cancel_at_period_end = false;
        $subscription->status = 'canceled';
        $subscription->canceled_at = now()->timestamp;

        return $subscription;
    }

    public function renewSubscription($subscriptionId)
    {
        $subscription = $this->retrieveSubscription($subscriptionId);

        $subscription->cancel_at_period_end = false;
        $subscription->status = 'active';
        $subscription->canceled_at = null;

        return $subscription;
    }

    private function getCardInfoFromToken($token)
    {
        switch ($token) {
            case "tok_visa":
                return ['brand' => 'Visa', 'last4' => '4242'];
            case "tok_mastercard":
                return ['brand' => 'MasterCard', 'last4' => '4444'];
        }
    }

    private function initializeBasePlan()
    {
        $plan = [
            "id" => config('services.stripe.subscription_plan_id'),
            "object" => "plan",
            "active" => true,
            "aggregate_usage" => null,
            "amount" => 9900,
            "amount_decimal" => "9900",
            "billing_scheme" => "per_unit",
            "created" => 1570453612,
            "currency" => "usd",
            "interval" => "month",
            "interval_count" => 1,
            "livemode" => false,
            "metadata" => [],
            "nickname" => "Monthly Subscription",
            "product" => "prod_FwwS1WkG1k86PE",
            "tiers" => null,
            "tiers_mode" => null,
            "transform_usage" => null,
            "trial_period_days" => null,
            "usage_type" => "licensed",
        ];
        $this->plans->push($plan);
    }

    private function fakeSubscription($customerId, $overrides = [])
    {
        return [
            "id" => "sub_FvoVJ5TkR7Z5mn",
            "object" => "subscription",
            "application_fee_percent" => null,
            "billing" => "charge_automatically",
            "billing_cycle_anchor" => 1570218948,
            "billing_thresholds" => null,
            "cancel_at" => null,
            "cancel_at_period_end" => false,
            "canceled_at" => null,
            "collection_method" => "charge_automatically",
            "created" => 1570218948,
            "current_period_end" => 1572897348,
            "current_period_start" => 1570218948,
            "customer" => $customerId,
            "days_until_due" => null,
            "default_payment_method" => null,
            "default_source" => null,
            "default_tax_rates" => [],
            "discount" => null,
            "ended_at" => null,
            "items" => [
                "object" => "list",
                "data" => [
                    [
                        "id" => "si_FvoVVopdnnAJ8T",
                        "object" => "subscription_item",
                        "billing_thresholds" => null,
                        "created" => 1570218948,
                        "metadata" => [],
                        "plan" => data_get($overrides, 'plan'),
                        "quantity" => 1,
                        "subscription" => "sub_FvoVJ5TkR7Z5mn",
                        "tax_rates" => [],
                    ],
                ],
                "has_more" => false,
                "total_count" => 1,
                "url" => "/v1/subscription_items?subscription=sub_FvoVJ5TkR7Z5mn",
            ],
            "latest_invoice" => null,
            "livemode" => false,
            "metadata" => [],
            "pending_setup_intent" => null,
            "plan" => data_get($overrides, 'plan'),
            "quantity" => 1,
            "schedule" => null,
            "start" => 1570218948,
            "start_date" => 1570218948,
            "status" => "active",
            "tax_percent" => null,
            "trial_end" => null,
            "trial_start" => null,
        ];
    }
}
