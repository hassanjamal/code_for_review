<?php

namespace App\Observers;

use App\PlatformAPI\PlatformGateway;
use App\Property;

class PropertyObserver
{
    public function created(Property $property)
    {
        //
    }

    public function updating(Property $property)
    {
        $this->syncWithAPI($property);
    }

    public function updated(Property $property)
    {
        //
    }

    public function deleted(Property $property)
    {
        //
    }

    public function restored(Property $property)
    {
        //
    }

    public function forceDeleted(Property $property)
    {
        //
    }

    /**
     * Resets the properties verification status to "unverified".
     *
     * @param \App\Property $property
     */
    protected function syncWithAPI(Property $property): void
    {
        if ($property->isDirty('api_identifier')) {
            $originalApiIdentifer = $property->getOriginal('api_identifier');

            // Cannot update the api_identifier after property is verified.
            if ($property->verified) {
                $property->setAttribute('api_identifier', $originalApiIdentifer);

            }
            // Get a new activation code, ignoring any exceptions here.
            try {
                $codes = app(PlatformGateway::class)->getActivationCode($property->api_identifier);

                $property->setAttribute('activation_code', $codes['code']);
                $property->setAttribute('activation_link', $codes['link']);
            } catch (\Exception $e) {
                $property->setAttribute('activation_code', null);
                $property->setAttribute('activation_link', null);
            }

            tenant()->deleteKey('mb:'.$originalApiIdentifer);
        }
    }
}
