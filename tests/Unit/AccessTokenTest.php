<?php

namespace Tests\Unit;

use App\AccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessTokenTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_scoped_to_a_site_id()
    {
        $this->createTenant();

        $tokenToFind = factory(AccessToken::class)->create(['site_id' => 100, 'token' => 'testing-token']);
        $tokenNotFound = factory(AccessToken::class)->create(['site_id' => 101, 'token' => 'not-found-token']);

        $actual = AccessToken::forSite(100)->get();

        $this->assertCount(1, $actual);
        $this->assertTrue($tokenToFind->is($actual->first()));
    }

    /** @test */
    public function an_access_token_cannot_belong_to_multiple_sites()
    {
        $this->markTestIncomplete('Need to add a test to ensure sites cannot have more than 1 token.');
    }

    /** @test */
    public function an_access_token_belongs_to_many_locations()
    {
        $this->markTestIncomplete('Once we have locations, we can test that an access token belongs to many locations.');
    }
}
