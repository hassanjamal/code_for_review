<?php

namespace Tests\Unit\Roles;

use App\Roles\Tenants\Staff\Staff;

/** @see \App\Roles\Tenants\Staff\Staff */
class StaffTest extends RoleTest
{
    protected $roleClass = Staff::class;
    protected $guardName = 'staff';

    /** @test */
    public function it_has_the_proper_role_name()
    {
        $this->assertEquals('Staff', $this->role->roleName);
    }
}
