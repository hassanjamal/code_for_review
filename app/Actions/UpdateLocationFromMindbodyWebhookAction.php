<?php

namespace App\Actions;

use App\Property;
use Spatie\QueueableAction\QueueableAction;

class UpdateLocationFromMindbodyWebhookAction
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
                    ->locations()
                    ->forApiId(data_get($data, 'locationId'))
                    ->update([
                        'api_id' => data_get($data, 'locationId'),
                        'name' => data_get($data, 'name'),
                        'address' => data_get($data, 'addressLine1'),
                        'address_2' => data_get($data, 'addressLine2'),
                        'phone' => data_get($data, 'phone'),
                        'city' => data_get($data, 'city'),
                        'state_province' => data_get($data, 'state'),
                        'postal_code' => data_get($data, 'postalCode'),
                        'latitude' => data_get($data, 'latitude'),
                        'longitude' => data_get($data, 'longitude'),
                    ]);
        }
    }
}
