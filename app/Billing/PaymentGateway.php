<?php

namespace App\Billing;

interface PaymentGateway
{
    public function createCustomer($token, $data);

    public function getCustomer($id);

    public function deleteCustomer($customerId);

    public function updateCustomer($customerId, $data = []);

    public function createPaymentMethod($type, $token);

    public function getPaymentMethod($id);

    public function getCustomerPaymentMethods($customerId, $type);

    public function attachPaymentMethodToCustomer($paymentMethodId, $customerId);

    public function detachPaymentMethod($paymentMethodId);

    public function createCard($customerId, $token);

    public function getCard($customerId, $cardId);

    public function getCards($customerId);

    public function getPlan($planId);

    public function retrieveSubscription($id);

    public function createSubscription($customerId, $planId);

    public function updateSubscription($subscriptionId, array $params);

    public function cancelSubscriptionAtPeriodEnd($subscriptionId);

    public function cancelSubscriptionNow($subscriptionId);
}
