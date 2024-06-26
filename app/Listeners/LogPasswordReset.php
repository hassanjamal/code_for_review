<?php

namespace App\Listeners;

use Illuminate\Auth\Events\PasswordReset;

class LogPasswordReset
{
    /**
     * Handle the event.
     *
     * @param  PasswordReset  $event
     * @return void
     */
    public function handle(PasswordReset $event)
    {
        activity('auth-log')
            ->withProperties(['ip' => request()->ip()])
            ->performedOn($event->user)
            ->log('password reset');
    }
}
