<?php

namespace Tests\Feature;

use App\Http\Livewire\RolesIndex;
use App\Staff;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_settings_route_is_not_accessible_to_guests()
    {
        $this->createTenant();

        $this->get(routeForTenant('roles.index'))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function index_route_works_for_logged_in_users_and_staff()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->create();
        $user = factory(User::class)->create();

        $this->actingAs($staff)->get(routeForTenant('roles.index'))->assertOk();

        $this->actingAs($user)->get(routeForTenant('roles.index'))->assertOk();
    }

    /** @test */
    public function roles_are_displayed()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->create();

        $role = Role::create(['name' => 'new role', 'guard_name' => 'staff']);

        $this->actingAs($staff);

        Livewire::test(RolesIndex::class)->assertSee(ucfirst($role->name));
    }
}
