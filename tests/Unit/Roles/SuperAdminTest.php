<?php

namespace Tests\Unit\Roles;

use App\Roles\Tenants\Admin\SuperAdmin;

/** @see SuperAdmin */
class SuperAdminTest extends RoleTest
{
    protected $roleClass = SuperAdmin::class;
    protected $guardName = 'web';

    /** @test */
    public function it_has_the_proper_role_name()
    {
        $this->assertEquals('Super Admin', $this->role->roleName);
    }
}
