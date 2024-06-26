<?php

namespace App\Jobs;

use App\Actions\SyncAppointmentsToPropertyAction;
use App\Actions\SyncClassesToPropertyAction;
use App\Actions\SyncClientsToPropertyAction;
use App\Actions\SyncLocationsToPropertyAction;
use App\Actions\SyncStaffToPropertyAction;
use App\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncApiData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var \App\Property */
    protected $property;

    public function __construct(Property $property)
    {
        $this->property = $property;
    }

    public function handle()
    {
        $propertyId = $this->property->id;

        app(SyncLocationsToPropertyAction::class)->execute($propertyId);

        // Syncs all active staff.
        app(SyncStaffToPropertyAction::class)->execute($propertyId);

        // Syncs all appointments that belong to active staff.
        app(SyncAppointmentsToPropertyAction::class)->onQueue('long-running')->execute($propertyId);

        // Syncs all clients that have appointments in the db.
        app(SyncClientsToPropertyAction::class)->onQueue('long-running')->execute($propertyId);


        // app(SyncClassesToPropertyAction::class)->execute($propertyId)->onQueue('tenants');
    }
}
