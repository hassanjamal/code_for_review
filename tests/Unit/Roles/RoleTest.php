<?php

namespace Tests\Unit\Roles;

use Spatie\Snapshots\MatchesSnapshots;
use Tests\TestCase;

abstract class RoleTest extends TestCase
{
    use MatchesSnapshots;

    protected $role;
    protected $roleClass;
    protected $guardName;

    protected function setUp(): void
    {
        parent::setUp();
        $this->role = new $this->roleClass;
    }

    /** @test */
    public function it_can_convert_permissions_to_list_for_artisan_command()
    {
        $this->role->permissions = ['foo', 'bar', 'baz'];

        $this->assertEquals('foo|bar|baz', $this->role->getPermissionsForArtisan());
    }

    /** @test */
    public function it_can_get_the_permissions()
    {
        $this->assertMatchesSnapshot($this->role->permissions);
    }

    /** @test */
    public function it_has_the_proper_guard()
    {
        $this->assertEquals($this->guardName, $this->role->guardName);
    }
}
