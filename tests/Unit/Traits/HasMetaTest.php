<?php

namespace Tests\Unit\Traits;

use App\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HasMetaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_store_single_new_meta_values()
    {
        $this->createTenant();

        $property = factory(Property::class)->create();

        $property->putMeta('foo', 'bar');

        $this->assertEquals(['foo' => 'bar'], $property->getMeta());
    }

    /** @test */
    public function it_can_store_an_array_of_new_meta_values()
    {
        $this->createTenant();

        $property = factory(Property::class)->create();

        $property->putMeta(['foo' => 'bar', 'baz' => 'buzz']);

        $this->assertEquals(['foo' => 'bar', 'baz' => 'buzz'], $property->getMeta());
    }

    /** @test */
    public function it_can_get_a_single_meta_value()
    {
        $this->createTenant();

        $property = factory(Property::class)->create(['meta' => ['foo' => 'bar']]);

        $this->assertEquals('bar', $property->getMeta('foo'));
    }

    /** @test */
    public function it_can_get_a_specify_default_value_if_key_does_not_exist()
    {
        $this->createTenant();

        $property = factory(Property::class)->create(['meta' => null]);

        $this->assertEquals('bar', $property->getMeta('foo', 'bar'));
    }

    /** @test */
    public function it_can_get_all_meta_values()
    {
        $this->createTenant();

        $property = factory(Property::class)->create(['meta' => ['foo' => 'bar', 'buz' => 'bazz']]);

        $this->assertEquals(['foo' => 'bar', 'buz' => 'bazz'], $property->getMeta());
    }

    /** @test */
    public function it_will_overwrite_an_existing_value()
    {
        $this->createTenant();

        $property = factory(Property::class)->create(['meta' => ['foo' => 'bar']]);

        $property->putMeta('foo', 'buzz');

        $this->assertEquals('buzz', $property->getMeta('foo'));
    }
}
