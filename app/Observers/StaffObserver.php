<?php

namespace App\Observers;

use App\Exceptions\PropertyNotVerifiedException;
use App\Staff;

class StaffObserver
{
    public function saving(Staff $staff)
    {
        $property = $staff->property;

        if (! $property || ! $property->fresh()->verified) {
            throw new PropertyNotVerifiedException('Cannot save staff data when property is not verified.');
        }
    }

    /**
     * Handle the staff "created" event.
     *
     * @param  \App\Staff  $staff
     * @return void
     */
    public function created(Staff $staff)
    {
        //
    }

    /**
     * Handle the staff "updated" event.
     *
     * @param  \App\Staff  $staff
     * @return void
     */
    public function updated(Staff $staff)
    {
        //
    }

    /**
     * Handle the staff "deleted" event.
     *
     * @param  \App\Staff  $staff
     * @return void
     */
    public function deleted(Staff $staff)
    {
        //
    }

    /**
     * Handle the staff "restored" event.
     *
     * @param  \App\Staff  $staff
     * @return void
     */
    public function restored(Staff $staff)
    {
        //
    }

    /**
     * Handle the staff "force deleted" event.
     *
     * @param  \App\Staff  $staff
     * @return void
     */
    public function forceDeleted(Staff $staff)
    {
        //
    }
}
