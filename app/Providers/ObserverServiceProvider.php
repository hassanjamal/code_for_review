<?php

namespace App\Providers;

use App\Appointment;
use App\Client;
use App\IntakeForm;
use App\Location;
use App\Observers\AppointmentObserver;
use App\Observers\ClientObserver;
use App\Observers\IntakeFormObserver;
use App\Observers\LocationObserver;
use App\Observers\PropertyObserver;
use App\Observers\StaffObserver;
use App\Property;
use App\Staff;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Appointment::observe(AppointmentObserver::class);
        Client::observe(ClientObserver::class);
        IntakeForm::observe(IntakeFormObserver::class);
        Location::observe(LocationObserver::class);
        Property::observe(PropertyObserver::class);
        Staff::observe(StaffObserver::class);
    }
}
