<?php

namespace App\Observers;

use App\IntakeForm;
use Illuminate\Support\Str;

class IntakeFormObserver
{
    public function creating(IntakeForm $intakeForm)
    {
        $this->generateAccessCode($intakeForm);
        $this->setCodeExpiresAt($intakeForm);
    }

    protected function generateAccessCode(IntakeForm $intakeForm): void
    {
        $intakeForm->code = Str::uuid()->toString();
    }

    protected function setCodeExpiresAt(IntakeForm $intakeForm): void
    {
        $intakeForm->code_expires_at = now()->addHours(5);
    }
}
