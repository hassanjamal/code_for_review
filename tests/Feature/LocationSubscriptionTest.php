<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Jobs\SyncApiData;
use App\Location;
use App\Staff;
use App\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/** @see \App\Http\Controllers\LocationSubscriptionsController */
class LocationSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        app()->instance(PaymentGateway::class, new FakePaymentGateway());
    }

    /** @test */
    public function the_endpoint_is_not_accessible_to_guests()
    {
        $this->createTenant();

        $this->post(routeForTenant('location-subscriptions.store'))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function creates_a_new_subscription_for_a_location()
    {
        Bus::fake();

        $this->createTenant();
        $location = factory(Location::class)->create();
        $staff = factory(Staff::class)->create(['api_id' => 1, 'property_id' => $location->property->id]);

        $this->assertNull($location->subscription);

        $this->actingAs($staff)->from(routeForTenant('properties.edit', $location->property))->post(routeForTenant('location-subscriptions.store'), [
            'location_id' => $location->id,
        ])->assertRedirect(routeForTenant('properties.edit', $location->property));

        $this->assertNotNull($location->fresh()->subscription);

        Bus::assertDispatched(SyncApiData::class);
    }

    /** @test */
    public function validation_fails_if_the_location_already_has_an_active_subscription()
    {
        $this->createTenant();
        $location = factory(Location::class)->create();
        factory(Subscription::class)->create(['location_id' => $location->id]);
        $staff = factory(Staff::class)->create(['api_id' => 1, 'property_id' => $location->property->id]);

        $this->actingAs($staff)->from(routeForTenant('properties.edit', $location->property))->post(routeForTenant('location-subscriptions.store'), [
            'location_id' => $location->id,
        ])->assertRedirect(routeForTenant('properties.edit', $location->property))
            ->assertSessionHas('error', 'This location already has a subscription.');
    }

    /** @test */
    public function validation_fails_if_the_location_already_has_a_cancelled_but_still_active_subscription()
    {
        $this->createTenant();
        $location = factory(Location::class)->create();
        factory(Subscription::class)->create(['location_id' => $location->id, 'ends_at' => now()->addMonth()]);
        $staff = factory(Staff::class)->create(['api_id' => 1, 'property_id' => $location->property->id]);

        $this->actingAs($staff)->from(routeForTenant('properties.edit', $location->property))->post(routeForTenant('location-subscriptions.store'), [
            'location_id' => $location->id,
        ])->assertRedirect(routeForTenant('properties.edit', $location->property))
            ->assertSessionHas('error', 'This location already has a subscription.');
    }

    /** @test */
    public function cancels_a_subscription_at_period_end_when_customer_cancels()
    {
        Carbon::setTestNow(now());

        list($location, $staff, $subscription) = $this->createLocationWithSubscription();

        $this->assertNull($subscription->ends_at);

        $this->actingAs($staff)->from(routeForTenant('properties.edit', $location->property))->delete(routeForTenant('location-subscriptions.cancel', $location), [
            'location_id' => $location->id,
        ])->assertRedirect(routeForTenant('properties.edit', $location->property));

        $this->assertNotNull($subscription->fresh()->ends_at);
    }

    /** @test */
    public function cannot_cancel_a_subscription_that_is_already_cancelled()
    {
        list($location, $staff, $subscription) = $this->createLocationWithSubscription();

        $subscription->update(['ends_at' => now()]);

        $this->assertNotNull($subscription->ends_at);

        $this->actingAs($staff)->from(routeForTenant('properties.edit', $location->property))->delete(routeForTenant('location-subscriptions.cancel', $location), [
            'location_id' => $location->id,
        ])->assertRedirect(routeForTenant('properties.edit', $location->property))
            ->assertSessionHas('error', 'The subscription is already cancelled.');
    }

    /** @test */
    public function can_renew_a_subscription_that_is_pending_cancellation()
    {
        list($location, $staff, $subscription) = $this->createLocationWithSubscription();

        // Subscription is cancelled, but won't expire until tomorrow.
        $subscription->update(['ends_at' => now()->addDay()]);

        $this->actingAs($staff)->from(routeForTenant('properties.edit', $location->property))
            ->post(routeForTenant('location-subscriptions.reactivate', $location))
            ->assertRedirect(routeForTenant('properties.edit', $location->property))
            ->assertSessionHas('success', 'The subscription was renewed.');

        $this->assertNull($location->subscription->fresh()->ends_at);
    }

    private function createLocationWithSubscription()
    {
        $this->createTenant();

        app(PaymentGateway::class)->createSubscription('cus_id', config('services.stripe.subscription_plan_id'));

        $subscription = factory(Subscription::class)->create();
        $location = $subscription->location;
        $staff = factory(Staff::class)->create(['property_id' => $location->property_id]);

        return [$location, $staff, $subscription];
    }
}
