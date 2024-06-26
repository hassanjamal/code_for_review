<?php

namespace App\Events;

use App\IntakeForm;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FormSubmitted
{
    use Dispatchable, SerializesModels;

    /**
     * @var IntakeForm
     */
    public $intakeForm;

    /**
     * Create a new event instance.
     * @param IntakeForm $intakeForm
     */
    public function __construct(IntakeForm $intakeForm)
    {
        $this->intakeForm = $intakeForm;
    }
}
