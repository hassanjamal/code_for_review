<?php

namespace App\Traits;

trait HasCompositeKey
{
    public function makeCompositeKey()
    {
        switch ($className = class_basename($this)) {

            case 'Staff':
            case 'Client':
                return makeDoubleCompositeKey($this->property_id, $this->api_id);
            case 'Appointment':
                return makeTripleCompositeKey($this->property_id, $this->location_api_id, $this->api_id);
            default:
                throw new \Exception('There is no method to create a key for this class: '.$className);
        }
    }
}
