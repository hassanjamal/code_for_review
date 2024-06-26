<?php

namespace Tests\Integration\PlatformAPI\Mindbody;

use App\PlatformAPI\Mindbody\MindbodyGateway;
use Tests\Integration\PlatformAPI\PlatformGatewayContractTests;
use Tests\TestCase;

/** @group mindbody */
/** @group wifi */
class MindbodyGatewayTest extends TestCase
{
    use PlatformGatewayContractTests;

    /** @var \App\PlatformAPI\PlatformGateway */
    public $platformGateway;

    public function setUp(): void
    {
        parent::setUp();

        $this->platformGateway = app(MindbodyGateway::class);
    }
}
