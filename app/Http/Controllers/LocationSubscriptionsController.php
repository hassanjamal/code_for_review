<?php

namespace App\Http\Controllers;

use App\Actions\SubscribeALocationAction;
use App\Billing\PaymentGateway;
use App\Location;
use Illuminate\Http\Request;

class LocationSubscriptionsController extends Controller
{
    public function store(Request $request, SubscribeALocationAction $subscribeALocationAction)
    {
        $location = Location::find($request->location_id);

        if ($location->subscription) {
            return back()->with('error', 'This location already has a subscription.');
        }

        $subscribeALocationAction->execute($location);

        return back()->with('success', 'Subscription added.');
    }

    public function reactivate(Location $location, PaymentGateway $paymentGateway)
    {
        $subscription = $location->subscription;

        $paymentGateway->renewSubscription($subscription->stripe_id);

        $subscription->update(['ends_at' => null]);

        return back()->with('success', 'The subscription was renewed.');
    }

    public function destroy(Location $location, PaymentGateway $paymentGateway)
    {
        $subscription = $location->subscription;

        if (! is_null($subscription->ends_at)) {
            return back()->with('error', 'The subscription is already cancelled.');
        }

        $cancelledSubscription = $paymentGateway->cancelSubscriptionAtPeriodEnd($subscription->stripe_id);

        $subscription->update([
            'ends_at' => $cancelledSubscription->current_period_end,
        ]);

        return back()->with('success', 'Subscription cancelled.');
    }
}
