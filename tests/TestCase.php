<?php

namespace Tests;

use Drfraker\SnipeMigrations\SnipeMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Arr;
use Illuminate\Testing\Assert;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, SnipeMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        // We don't want to create logs for all of our models in tests.
        // If you want to turn this on, add activity()->enableLogging(); in your test.
        activity()->disableLogging();

        // Inertia Assertions
        TestResponse::macro('props', function ($key = null) {
            $props = json_decode(json_encode($this->original->getData()['page']['props']), JSON_OBJECT_AS_ARRAY);
            if ($key) {
                return Arr::get($props, $key);
            }
            return $props;
        });
        TestResponse::macro('assertHasProp', function ($key) {
            Assert::assertTrue(Arr::has($this->props(), $key));
            return $this;
        });
        TestResponse::macro('assertPropValue', function ($key, $value) {
            $this->assertHasProp($key);
            if (is_callable($value)) {
                $value($this->props($key));
            } else {
                Assert::assertEquals($this->props($key), $value);
            }
            return $this;
        });
        TestResponse::macro('assertPropCount', function ($key, $count) {
            $this->assertHasProp($key);
            Assert::assertCount($count, $this->props($key));
            return $this;
        });
        TestResponse::macro('assertComponentIs', function ($component) {
            Assert::assertEquals($component, $this->original->getData()['page']['component']);
            return $this;
        });
    }

    protected function createTenant($overrides = [], $initialize = true)
    {
        $tenant = tenancy()->create($overrides['domains'] ?? ['acme.qn2020.test'], [
            'name' => $overrides['name'] ?? 'foo',
            'phone' => $overrides['phone']  ?? '444',
            'email' => $overrides['email']  ?? 'email',
        ]);

        if ($initialize) {
            tenancy()->initialize($tenant);
        }

        // Add any site id's necessary to the tenants data object using the site_ids key.
        if (isset($overrides['site_ids'])) {
            foreach ($overrides['site_ids'] as $id) {
                $tenant->put("mb:{$id}", $id);
            }
        } else {
            // If there are no specific site_ids to add, just add the defaults for all tests.
            $tenant->put(['mb:-99787' => -99787, 'mb:16134' => 16134]);
        }


        return $tenant;
    }

    protected function tearDown(): void
    {
        // Delete all tenant databases;
        tenancy()->all()->map->delete();

        parent::tearDown();
    }
}
