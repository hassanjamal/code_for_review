<?php

namespace App\Actions;

use App\Property;
use Spatie\QueueableAction\QueueableAction;

class CancelAppointmentFromMindbodyWebhookAction
{
    use QueueableAction;

    public function execute($data)
    {
        $siteId = data_get($data, "siteId");

        $tenant = tenancy()->all()->filter(function ($tenant) use ($siteId) {
            return array_key_exists('mb:' . $siteId, data_get($tenant, 'data'));
        })->first();

        if ($tenant) {
            tenancy()->initialize($tenant);

            Property::findByApiIdentifier($siteId)
                    ->first()
                    ->appointments()
                    ->forApiId(data_get($data, 'appointmentId'))
                    ->update([
                        'status' => 'Cancelled',
                    ]);
        }
    }
}
