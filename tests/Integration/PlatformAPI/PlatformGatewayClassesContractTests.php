<?php

namespace Tests\Integration\PlatformAPI;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait PlatformGatewayClassesContractTests
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_classes()
    {
        $this->createTenant();
        $classes = $this->platformGateway->getClasses(-99787, Carbon::parse('27th November 2019'), Carbon::parse('27th November 2019'));

        $this->assertInstanceOf(Collection::class, $classes);
        $this->assertCount(2, $classes);


        $providerClass = $classes->where('api_id', 1498)->first();
        $this->assertSame([
            "is_canceled" => false,
            "api_id" => 1498,
            "api_instance_id" => 29,
            "resource_name" => "Yoga Room",
            "class_description_name" => "Yoga",
            "staff_api_id" => 2,
            "location_api_id" => 1,
        ], Arr::except($providerClass->toArray(), ['start_date_time', 'end_date_time', 'clients']));

        $this->asserttrue(Carbon::create("2019-11-27T12:00:00")->eq(Carbon::parse($providerClass->start_date_time)));
        $this->asserttrue(Carbon::create("2019-11-27T13:00:00")->eq(Carbon::parse($providerClass->end_date_time)));
        $this->assertCount(0, $providerClass->clients);

        $providerClass = $classes->where('api_id', 1643)->first();
        $this->assertSame([
            "is_canceled" => false,
            "api_id" => 1643,
            "api_instance_id" => 34,
            "resource_name" => "treatment 1",
            "class_description_name" => "Dance Class",
            "staff_api_id" => 100000003,
            "location_api_id" => 1,
        ], Arr::except($providerClass->toArray(), ['start_date_time', 'end_date_time', 'clients']));

        $this->asserttrue(Carbon::create("2019-11-27T09:00:00")->eq(Carbon::parse($providerClass->start_date_time)));
        $this->asserttrue(Carbon::create("2019-11-27T10:00:00")->eq(Carbon::parse($providerClass->end_date_time)));
        $this->assertCount(2, $providerClass->clients);
        $this->assertSame([
            "client_api_public_id" => "new",
            "signed_in" => true,
        ], $providerClass->clients->where('client_api_public_id', 'new')->first());

        $this->assertSame([
            "client_api_public_id" => "hassan_1",
            "signed_in" => true,
        ], $providerClass->clients->where('client_api_public_id', 'hassan_1')->first());
    }
}
