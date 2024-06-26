<?php

namespace Tests\Unit\Jobs;

use App\Actions\SyncAppointmentsToPropertyAction;
use App\Actions\SyncClassesToPropertyAction;
use App\Actions\SyncClientsToPropertyAction;
use App\Actions\SyncLocationsToPropertyAction;
use App\Actions\SyncStaffToPropertyAction;
use App\Jobs\SyncApiData;
use App\Property;
use Tests\TestCase;

/** @see SyncApiData */
class SyncApiDataTest extends TestCase
{
    /** @test */
    public function it_calls_actions_to_sync_all_data()
    {
        $syncLocations = $this->spy(SyncLocationsToPropertyAction::class);
        $syncStaffMembers = $this->spy(SyncStaffToPropertyAction::class);
        $syncAppointments = $this->spy(SyncAppointmentsToPropertyAction::class, function ($spy) {
            return $spy->shouldReceive('onQueue')->with('long-running')->andReturnSelf();
        });
        $syncClients = $this->spy(SyncClientsToPropertyAction::class, function ($spy) {
            return $spy->shouldReceive('onQueue')->with('long-running')->andReturnSelf();
        });
        //$syncClasses = $this->spy(SyncClassesToPropertyAction::class);

        SyncApiData::dispatchNow(new Property(['id' => 1]));

        $syncLocations->shouldHaveReceived('execute')->once()->with(1);
        $syncStaffMembers->shouldHaveReceived('execute')->once()->with(1);
        $syncAppointments->shouldHaveReceived('execute')->once()->with(1);
        $syncClients->shouldHaveReceived('execute')->once()->with(1);
        // $syncClasses->shouldHaveReceived('execute')->once()->with(1);
    }
}
