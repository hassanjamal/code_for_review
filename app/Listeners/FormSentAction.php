<?php

namespace App\Listeners;

use App\Events\FormSent;

class FormSentAction
{
    /**
     * Handle the event.
     *
     * @param  FormSent  $event
     * @return void
     */
    public function handle(FormSent $event)
    {
        activity('forms-log')
            ->performedOn($event->intakeForm)
            ->log('Form Sent');
    }
}
