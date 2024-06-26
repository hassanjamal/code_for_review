<?php

namespace App\Actions;

use App\Property;
use Spatie\QueueableAction\QueueableAction;

class UpdateStaffFromMindbodyWebhookAction
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
                ->staff()
                ->forApiId(data_get($data, 'staffId'))
                ->update([
                    'api_id' => data_get($data, "staffId"),
                    'first_name' => data_get($data, "staffFirstName"),
                    'last_name' => data_get($data, "staffLastName"),
                ]);
        }
    }
}
