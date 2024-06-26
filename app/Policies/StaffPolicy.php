<?php

namespace App\Policies;

use App\Staff;
use Illuminate\Auth\Access\HandlesAuthorization;

class StaffPolicy
{
    use HandlesAuthorization;

    public function uploadFiles(Staff $staff)
    {
        return $staff->hasPermissionTo('imageManager:store');
    }
}
