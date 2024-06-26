<?php

namespace Tests\Unit\Actions;

use App\Actions\CreateMindbodyPropertyAction;
use App\Actions\CreateMindbodyPropertySessionTypesAction;
use App\PlatformAPI\Mindbody\FakeMindbodyGateway;
use App\PlatformAPI\PlatformGateway;
use App\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** @see CreateMindbodyPropertyAction */
class CreateMindbodyPropertySessionTypesActionTest extends TestCase
{
    use RefreshDatabase;

    private $action;

    public function setUp(): void
    {
        parent::setUp();

        app()->instance(PlatformGateway::class, new FakeMindbodyGateway);

        $this->action = app(CreateMindbodyPropertySessionTypesAction::class);
    }

    /** @test */
    public function it_can_save_session_types_for_a_property()
    {
        $this->createTenant();

        factory(Property::class)->create(['api_identifier' => -99787]);

        $this->action->execute(-99787);

        $property = Property::findByApiIdentifier(-99787)->first();

        $this->assertSame([
            5 => "Office Visit",
            6 => "Outpatient Visit New",
            7 => "add on",
            8 => "Yoga",
            9 => "Advanced Yoga",
            10 => "Initial Consultation (60mins)",
            11 => "Standard Consultation (30mins)",
            12 => "Dance",
        ], $property->session_types);
    }
}
