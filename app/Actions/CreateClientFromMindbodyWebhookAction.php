<?php

namespace App\Actions;

use App\Property;
use Illuminate\Support\Carbon;
use Spatie\QueueableAction\QueueableAction;

class CreateClientFromMindbodyWebhookAction
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

            $property = Property::findByApiIdentifier($siteId)->firstOrFail();
            $property->clients()
                ->firstOrCreate(
                    ['id' => makeDoubleCompositeKey($property->id, data_get($data, 'clientUniqueId'))],
                    [
                        'api_id' => data_get($data, 'clientUniqueId'),
                        'api_public_id' => data_get($data, 'clientId'),
                        'first_name' => data_get($data, 'firstName'),
                        'last_name' => data_get($data, 'lastName'),
                        'gender' => data_get($data, 'gender'),
                        'email' => data_get($data, 'email'),
                        //'address_1' => data_get($data, 'addressLine1'),
                        //'address_2' => data_get($data, 'addressLine2'),
                        //'city' => data_get($data, 'city'),
                        //'state' => data_get($data, 'state'),
                        //'postal_code' => data_get($data, 'postalCode'),
                        //'country' => data_get($data, 'country'),
                        //'mobile_phone' => data_get($data, 'mobilePhone'),
                        //'work_phone' => data_get($data, 'workPhone'),
                        //'home_phone' => data_get($data, 'homePhone'),
                        'birth_date' => data_get($data, 'birthDateTime') ? Carbon::parse(data_get($data, 'birthDateTime')) : null,
                        'referred_by' => data_get($data, 'referredBy'),
                        'first_appointment_date' => data_get($data, 'firstAppointmentDateTime') ? Carbon::parse(data_get($data, 'FirstAppointmentDateTime')) : null,
                        'status' => data_get($data, 'status'),
                    ]
                );
        }
    }
}
