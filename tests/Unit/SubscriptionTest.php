<?php

namespace Tests\Unit;

use App\Location;
use App\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_location()
    {
        $this->createTenant();

        $location = factory(Location::class)->create();

        $subscription = factory(Subscription::class)->create([
            'location_id' => $location->id,
        ]);

        $this->assertTrue($location->is($subscription->location));
    }

    /** @test */
    public function it_can_tell_if_it_is_current_when_ends_at_is_set_and_null()
    {
        $this->createTenant();

        // Test that null ends at is current.
        $noEndsAt = factory(Subscription::class)->create(['ends_at' => null]);

        // Test that future ends_at is current.
        $futureEndsAt = factory(Subscription::class)->create(['ends_at' => now()->addDay()]);

        // Test that past ends_at is not current.
        $pastEndsAt = factory(Subscription::class)->create(['ends_at' => now()->subDay()]);

        $currentSubscriptions = Subscription::current()->get();

        $this->assertTrue($currentSubscriptions->contains($noEndsAt->id));
        $this->assertTrue($currentSubscriptions->contains($futureEndsAt->id));
        $this->assertFalse($currentSubscriptions->contains($pastEndsAt->id));
    }
}
