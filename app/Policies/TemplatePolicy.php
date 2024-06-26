<?php

namespace App\Policies;

use App\Template;
use Illuminate\Auth\Access\HandlesAuthorization;

class TemplatePolicy
{
    use HandlesAuthorization;

    public function viewAny($user)
    {
        //
    }

    public function view($user, Template $template)
    {
        //
    }

    public function create($user)
    {
        return $user->hasPermissionTo('templates:create');
    }

    public function update($user, Template $template)
    {
        // Templates can only be updated the creator.
        return $user->hasPermissionTo('templates:create') &&
            (string) $user->id === $template->creator_id;
    }

    public function delete($user, Template $template)
    {
        return $user->hasPermissionTo('templates:delete') &&
            (string) $user->id === $template->creator_id;
    }
}
