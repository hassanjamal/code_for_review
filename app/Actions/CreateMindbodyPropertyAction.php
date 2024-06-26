<?php

namespace App\Actions;

use App\PlatformAPI\PlatformGateway;
use App\Property;
use Exception;
use Spatie\QueueableAction\QueueableAction;

class CreateMindbodyPropertyAction
{
    use QueueableAction;

    /**
     * @var \App\PlatformAPI\PlatformGateway
     */
    private $platformGateway;

    public function __construct(PlatformGateway $platformGateway)
    {
        $this->platformGateway = $platformGateway;
    }

    public function execute($siteId, $name)
    {
        if (! $tenant = tenant()) {
            throw new Exception('The action requires a tenant to be initialized.');
        }

        $code = $this->platformGateway->getActivationCode($siteId);

        Property::create([
            'api_provider' => 'mindbody',
            'api_identifier' => $siteId,
            'activation_code' => data_get($code, 'code'),
            'activation_link' => data_get($code, 'link'),
            'name' => $name,
        ]);

        //Todo: Send slack notification of new sign up.
    }
}
