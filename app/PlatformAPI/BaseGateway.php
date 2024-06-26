<?php

namespace App\PlatformAPI;

abstract class BaseGateway
{
    /**
     * @var \Zttp\Zttp
     */
    protected $apiClient;

    abstract protected function getApiClient($site);
}
