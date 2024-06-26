<?php

namespace App\Actions;

use App\Property;
use Illuminate\Support\Carbon;
use Spatie\QueueableAction\QueueableAction;

class UpdateClientFromMindbodyWebhookAction
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
                    ->clients()
                    ->forApiId(data_get($data, 'clientUniqueId'))
                    ->firstOrFail()
                    ->update([
                        'api_public_id' => data_get($data, 'clientId'),
                        'first_name' => data_get($data, 'firstName'),
                        'last_name' => data_get($data, 'lastName'),
                        'middle_name' => data_get($data, 'middleName'),
                        'gender' => data_get($data, 'gender'),
                        'email' => data_get($data, 'email'),
                        'birth_date' => data_get($data, 'birthDateTime') ? Carbon::parse(data_get($data, 'birthDateTime')) : null,
                        'referred_by' => data_get($data, 'referredBy'),
                        'first_appointment_date' => data_get($data, 'firstAppointmentDateTime') ? Carbon::parse(data_get($data, 'FirstAppointmentDateTime')) : null,
                        'photo_url' => data_get($data, 'photoUrl'),
                        'status' => data_get($data, 'status'),
                    ]);
        }
    }
}
