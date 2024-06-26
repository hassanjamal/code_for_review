<?php

namespace Tests\Unit\Actions;

use App\Actions\SubscribeALocationAction;
use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Location;
use App\PlatformAPI\Mindbody\FakeMindbodyGateway;
use App\PlatformAPI\PlatformGateway;
use App\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @see \App\Actions\SubscribeALocationAction */
class SubscribeALocationActionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        app()->instance(PlatformGateway::class, new FakeMindbodyGateway);
        app()->instance(PaymentGateway::class, new FakePaymentGateway);
    }

    /** @test */
    public function it_adds_a_subscription_to_a_location()
    {
        $this->createTenant();

        $location = factory(Location::class)->create();

        $this->assertNull($location->subscription);

        app(SubscribeALocationAction::class)->execute($location);

        $this->assertNotNull($location->fresh()->subscription);
    }

    /** @test */
    public function it_does_not_add_a_subscription_to_a_location_that_has_a_subscription()
    {
        $this->createTenant();

        $subscription = factory(Subscription::class)->create();
        $location = $subscription->location;

        $this->assertTrue($location->subscription->is($subscription));

        $count = Subscription::current()->count();

        app(SubscribeALocationAction::class)->execute($location);

        $this->assertEquals($count, Subscription::current()->count());
        $this->assertTrue($location->subscription->is($subscription));
    }
}
