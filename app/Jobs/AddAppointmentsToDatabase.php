<?php

namespace App\Jobs;

use App\Actions\SendIntakeFormLinkToClientAction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddAppointmentsToDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $appointments;

    private $property;

    public function __construct($appointments, $property)
    {
        $this->appointments = $appointments;
        $this->property = $property;
    }

    public function handle()
    {
        $locations = $this->property->load('clients')->locations()->get(['id', 'property_id', 'api_id']);

        $this->appointments->each(function ($a) use ($locations) {
            $this->property
                ->appointments()
                ->updateOrCreate(
                    ['id' => $a->id],
                    [
                        'api_id' => $a->api_id,
                        'property_id' => $a->property_id,
                        'location_id' => $a->location_id,
                        'location_api_id' => $a->location_api_id,
                        'client_api_public_id' => $a->client_api_public_id,
                        'staff_api_id' => $a->staff_api_id,
                        'staff_id' => $a->staff_id,
                        'duration' => $a->duration,
                        'status' => $a->status,
                        'start_date_time' => $a->start_date_time,
                        'end_date_time' => $a->end_date_time,
                        'notes' => $a->notes,
                        'staff_requested' => $a->staff_requested,
                        'service_id' => $a->service_id,
                        'service_name' => $a->service_name,
                        'room_name' => $a->room_name,
                        'first_appointment' => $a->first_appointment,
                    ]);

            if ($a->first_appointment) {
                $action = app(SendIntakeFormLinkToClientAction::class);
                $action->execute($a);
            }
        });
    }
}
