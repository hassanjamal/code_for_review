<?php

namespace App\Policies;

use App\Alert;
use App\Staff;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlertPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any alerts.
     *
     * @param  \App\Staff  $staff
     * @return mixed
     */
    public function viewAny(Staff $staff)
    {
        //
    }

    /**
     * Determine whether the user can view the alert.
     *
     * @param  \App\Staff  $staff
     * @param  \App\Alert  $alert
     * @return mixed
     */
    public function view(Staff $staff, Alert $alert)
    {
        //
    }

    /**
     * Determine whether the user can create alerts.
     *
     * @param  \App\Staff  $staff
     * @return mixed
     */
    public function create(Staff $staff)
    {
        return $staff->hasPermissionTo('alerts:create');
    }

    /**
     * Determine whether the user can update the alert.
     *
     * @param  \App\Staff  $staff
     * @param  \App\Alert  $alert
     * @return mixed
     */
    public function update(Staff $staff, Alert $alert)
    {
        //
    }

    /**
     * Determine whether the user can delete the alert.
     *
     * @param  \App\Staff  $staff
     * @param  \App\Alert  $alert
     * @return mixed
     */
    public function delete(Staff $staff, Alert $alert)
    {
        //
    }

    /**
     * Determine whether the user can restore the alert.
     *
     * @param  \App\Staff  $staff
     * @param  \App\Alert  $alert
     * @return mixed
     */
    public function restore(Staff $staff, Alert $alert)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the alert.
     *
     * @param  \App\Staff  $staff
     * @param  \App\Alert  $alert
     * @return mixed
     */
    public function forceDelete(Staff $staff, Alert $alert)
    {
        //
    }
}
