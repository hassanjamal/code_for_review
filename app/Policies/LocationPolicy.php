<?php

namespace App\Policies;

use App\Location;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any locations.
     *
     * @param  $user
     * @return mixed
     */
    public function viewAny($user)
    {
        $user->hasPermissionTo('view any location');
    }

    /**
     * Determine whether the user can view the location.
     *
     * @param  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function view($user, Location $location)
    {
        if ($user->can('locations:view-all')) {
            return true;
        }

        if ($user->can('locations:view-own')) {
            return $user->property_id === $location->property_id;
        }
    }

    /**
     * Determine whether the user can update the location.
     *
     * @param  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function update($user, Location $location)
    {
        //
    }

    /**
     * Determine whether the user can delete the location.
     *
     * @param  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function delete($user, Location $location)
    {
        //
    }

    /**
     * Determine whether the user can restore the location.
     *
     * @param  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function restore($user, Location $location)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the location.
     *
     * @param  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function forceDelete($user, Location $location)
    {
        //
    }
}
