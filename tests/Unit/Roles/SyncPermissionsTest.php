<?php

namespace Tests\Unit\Roles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SyncPermissionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_syncs_permissions_as_specified_in_config()
    {
        $tenant = $this->createTenant();

        //  Given I have some permissions in config array
        config(['permissions.staff' => ['can do something']]);

        $this->assertDatabaseMissing('permissions', [
            'name' => 'can do something',
            'guard_name' => 'staff',
        ]);

        // And the sync roles and permissions command is run
        \Artisan::call('qn:sync-roles-and-permissions');

        // The artisan command ends tenancy after each sync operation.
        // We'll need to re-init tenancy.
        tenancy()->initialize($tenant);

        // the permissions and guard should be in the database.
        $this->assertDatabaseHas('permissions', [
            'name' => 'can do something',
            'guard_name' => 'staff',
        ]);
    }

    /** @test */
    public function it_removes_permissions_that_are_no_longer_specified_in_the_role_class()
    {
        $tenant = $this->createTenant();

        // Given I have a permission in the database.
        Permission::create(['name' => 'should be removed', 'guard_name' => 'staff']);
        $role = Role::findByName('Staff', 'staff');
        $role->givePermissionTo('should be removed');

        // The role should  now have the new permission.
        $this->assertTrue($role->hasPermissionTo('should be removed'));

        // When I run the sync permissions command.
        \Artisan::call('qn:sync-roles-and-permissions');

        // The artisan command ends tenancy after each sync operation.
        // We'll need to re-init tenancy.
        tenancy()->initialize($tenant);

        // The permissions should now be removed from the role.
        $role = Role::findByName('Staff', 'staff');
        $this->assertFalse($role->hasPermissionTo('should be removed'));
    }
}
