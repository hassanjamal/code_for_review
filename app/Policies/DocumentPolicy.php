<?php

namespace App\Policies;

use App\Document;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
{
    use HandlesAuthorization;

    public function viewAny($user)
    {
        //
    }

    public function view($user, Document $document)
    {
        //
    }

    public function create($user)
    {
        return $user->hasPermissionTo('documents:create');
    }

    public function update($user, Document $document)
    {
        //
    }

    public function delete($user, Document $document)
    {
        //
    }

    public function restore($user, Document $document)
    {
        //
    }

    public function forceDelete($user, Document $document)
    {
        //
    }
}
