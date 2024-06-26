<?php

namespace App\Observers;

use App\Exceptions\PropertyNotVerifiedException;
use App\Location;

class LocationObserver
{
    public function saving(Location $location)
    {
        $property = $location->property;

        if (! $property || ! $property->fresh()->verified) {
            throw new PropertyNotVerifiedException('Cannot save location data when property is not verified.');
        }
    }

    /**
     * Handle the location "created" event.
     *
     * @param  \App\Location  $location
     * @return void
     */
    public function created(Location $location)
    {
        //
    }

    /**
     * Handle the location "updated" event.
     *
     * @param  \App\Location  $location
     * @return void
     */
    public function updated(Location $location)
    {
        //
    }

    /**
     * Handle the location "deleted" event.
     *
     * @param  \App\Location  $location
     * @return void
     */
    public function deleted(Location $location)
    {
        //
    }

    /**
     * Handle the location "restored" event.
     *
     * @param  \App\Location  $location
     * @return void
     */
    public function restored(Location $location)
    {
        //
    }

    /**
     * Handle the location "force deleted" event.
     *
     * @param  \App\Location  $location
     * @return void
     */
    public function forceDeleted(Location $location)
    {
        //
    }
}
