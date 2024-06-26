<?php

namespace App\Policies;

use App\ProgressNote;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProgressNotePolicy
{
    use HandlesAuthorization;

    public function create($staff, $appointment = null)
    {
        if ($staff->cannot('notes:create')) {
            return false;
        }

        if ($appointment) {
            return ! $appointment->progressNotes()->forStaff($staff)->exists();
        }

        return true;
    }

    public function edit($staff, ProgressNote $note)
    {
        if (! $staff->can('notes:create')) {
            return false;
        }

        if ($staff->can('notes:view-all')) {
            return true;
        }

        // Can save a note if, the user saving is the note owner and the note is not signed.
        return $staff->is($note->staff);
    }

    public function update($staff, ProgressNote $note)
    {
        // Completed notes cannot be updated.
        if (! $note->is_draft) {
            return false;
        }

        // Staff must be able to notes:create to update notes.
        if ($staff->cannot('notes:create')) {
            return false;
        }

        // Can save a note if, the user saving is the note owner and the note is not signed.
        return $staff->is($note->staff);
    }
}
