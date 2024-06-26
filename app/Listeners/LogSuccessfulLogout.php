<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    /**
     * Handle the event.
     *
     * @param  Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        activity('auth-log')
            ->withProperties(['ip' => request()->ip()])
            ->performedOn($event->user)
            ->log('logged out');
    }
}
