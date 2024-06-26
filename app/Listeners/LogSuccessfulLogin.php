<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        activity('auth-log')
            ->withProperties(['ip' => request()->ip()])
            ->performedOn($event->user)
            ->log('logged in');
    }
}
