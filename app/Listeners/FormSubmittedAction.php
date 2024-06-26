<?php

namespace App\Listeners;

use App\Events\FormSubmitted;

class FormSubmittedAction
{
    /**
     * Handle the event.
     *
     * @param  FormSubmitted  $event
     * @return void
     */
    public function handle(FormSubmitted $event)
    {
        activity('forms-log')
            ->performedOn($event->intakeForm)
            ->log('Form Submitted');
    }
}
