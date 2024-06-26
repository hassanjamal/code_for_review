<?php

namespace Tests\Integration\PlatformAPI\Mindbody;

use App\PlatformAPI\Mindbody\FakeMindbodyGateway;
use Tests\Integration\PlatformAPI\PlatformGatewayContractTests;
use Tests\TestCase;

/** @group mindbody */
class FakeMindbodyGatewayTest extends TestCase
{
    use PlatformGatewayContractTests;

    /** @var \App\PlatformAPI\PlatformGateway */
    public $platformGateway;

    public function setUp(): void
    {
        parent::setUp();

        $this->platformGateway = app(FakeMindbodyGateway::class);
    }
}
