<?php

namespace Tests\Unit\Actions;

use App\Actions\CreateMindbodyPropertyAction;
use App\PlatformAPI\Mindbody\FakeMindbodyGateway;
use App\PlatformAPI\PlatformGateway;
use App\Property;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @see CreateMindbodyPropertyAction */
class CreateMindbodyPropertyActionTest extends TestCase
{
    use RefreshDatabase;

    private $action;

    public function setUp(): void
    {
        parent::setUp();

        app()->instance(PlatformGateway::class, new FakeMindbodyGateway);

        $this->action = app(CreateMindbodyPropertyAction::class);
    }

    /** @test */
    public function a_new_property_is_created_for_the_tenant()
    {
        $this->createTenant();

        $this->action->execute(-99787, 'Test Name');

        $properties = Property::all();

        $this->assertCount(1, $properties);

        tap($properties->first(), function ($p) {
            $this->assertEquals('mindbody', $p->api_provider);
            $this->assertEquals(-99787, $p->api_identifier);
            $this->assertEquals('Test Name', $p->name);
            $this->assertEquals('code-for:-99787', $p->activation_code);
            $this->assertEquals('link-for:-99787', $p->activation_link);
            $this->assertFalse($p->verified);
        });
    }

    /** @test */
    public function an_exception_is_thrown_if_a_tenant_cannot_be_identified()
    {
        $this->expectException(Exception::class);

        $this->action->execute(-99787, 'Test Name');
    }
}
