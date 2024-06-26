<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    /**
     * Handle the event.
     * @param Failed $event
     * @return void
     */
    public function handle(Failed $event)
    {
        if ($event->user) {
            activity('auth-log')
                ->withProperties(['ip' => request()->ip()])
                ->performedOn($event->user)
                ->log('login failed');
        }
    }
}
