<?php

namespace App\Actions;

use App\Exceptions\PropertyNotVerifiedException;
use App\PlatformAPI\PlatformGateway;
use App\Property;
use Spatie\QueueableAction\QueueableAction;

class VerifyMindbodyApiConnectionAction
{
    use QueueableAction;

    /**
     * @var \App\PlatformAPI\PlatformGateway
     */
    private $platformGateway;

    /**
     * @var \App\Actions\SyncLocationsToPropertyAction
     */
    private $syncMindbodyLocationsToPropertyAction;

    public function __construct(PlatformGateway $platformGateway, SyncLocationsToPropertyAction $syncMindbodyLocationsToPropertyAction)
    {
        $this->platformGateway = $platformGateway;
        $this->syncMindbodyLocationsToPropertyAction = $syncMindbodyLocationsToPropertyAction;
    }

    public function execute($siteId)
    {
        $response = $this->platformGateway->authenticateApp($siteId);

        $property = tap(Property::where('api_identifier', $siteId)->first(), function ($property) use ($response, $siteId) {
            if (! $property->verified) {
                throw new PropertyNotVerifiedException();
            }

            $property->putMeta(['access_token' => $response->token]);
        });

        tenant()->put('mb:'.$siteId, $siteId);

        $this->syncMindbodyLocationsToPropertyAction->execute($property->id);
    }
}
