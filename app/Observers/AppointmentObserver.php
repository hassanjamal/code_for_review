<?php

namespace App\Observers;

use App\Appointment;
use App\Exceptions\PropertyNotVerifiedException;

class AppointmentObserver
{
    public function saving(Appointment $appointment)
    {
        $property = $appointment->property;

        if (! $property || ! $property->fresh()->verified) {
            throw new PropertyNotVerifiedException('Cannot save appointment data when property is not verified.');
        }
    }

    public function creating(Appointment $appointment)
    {
        if (is_null($appointment->id)) {
            $appointment->id = $appointment->makeCompositeKey();
        }
    }
}
