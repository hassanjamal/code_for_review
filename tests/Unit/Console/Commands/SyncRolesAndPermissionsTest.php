<?php

namespace Tests\Unit\Console\Commands;

use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SyncRolesAndPermissionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_syncs_permissions_when_run()
    {
        $tenant = $this->createTenant();

        // Given I have a staff role with permission to do something.
        $role = Role::updateOrCreate(['name' => 'Staff', 'guard_name' => 'staff']);
        $permission = Permission::create(['name' => 'permission-that-should-be-removed-on-sync', 'guard_name' => 'staff']);
        $role->givePermissionTo($permission);

        $staff = factory(Staff::class)->state('staff')->create();
        $staff->assignRole($role);

        // The employee should have the permission that will be removed on sync and view all notes.
        $this->assertTrue($staff->can('permission-that-should-be-removed-on-sync'));
        // This is a permission that staff members have by default
        $this->assertTrue($staff->can('notes:view-all'));

        // And I run the command
        \Artisan::call('qn:sync-roles-and-permissions');

        // The artisan command ends tenancy, so we need to reinitialize it.
        tenancy()->initialize($tenant);

        // The staff member role should no longer have the permission to permission-that-should-be-removed-on-sync
        // because it is not a permission in the Staff Role Class
        // but should still be able to view all notes.
        $this->assertFalse($staff->can('permission-that-should-be-removed-on-sync'));
        $this->assertTrue($staff->can('notes:view-all'));
    }

    /** @test */
    public function can_sync_roles_and_permissions_for_a_specified_tenant()
    {
        config()->set('tenancy.seed_after_migration', false);

        // Given I have more than 1 tenant.
        $tenantToSync = $this->createTenant(['domains' => ['acme.qn2020.test'], 'email' => 'foo@example.com'], false);

        $otherTenant = $this->createTenant(['domains' => ['nosync.qn2020.test'], 'email' => 'bar@example.com'], false);

        // Make sure neither tenant has permissions set.
        tenancy()->initialize($tenantToSync);
        $this->assertCount(0, Permission::all());
        tenancy()->end();

        tenancy()->initialize($otherTenant);
        $this->assertCount(0, Permission::all());
        tenancy()->end();

        // And I call the command to sync roles with a specific tenant
        Artisan::call('qn:sync-roles-and-permissions', ['--tenantId' => $tenantToSync->id]);

        // Only that tenant should be synced.
        tenancy()->initialize($tenantToSync);
        $this->assertTrue(Permission::get()->count() > 0);
        tenancy()->end();

        tenancy()->initialize($otherTenant);
        $this->assertCount(0, Permission::all());
        tenancy()->end();
    }
}
