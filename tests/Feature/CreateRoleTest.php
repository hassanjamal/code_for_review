<?php

namespace Tests\Feature;

use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CreateRoleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_role_can_be_created()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();

        $this->actingAs($staff);

        Livewire::test('roles-create')
            ->set('name', 'New Role') // Default guard of component is 'staff'.
            ->call('create')
            ->assertSet('name', '');

        $this->assertTrue(Role::findByName('New Role', 'staff')->exists());
    }

    /** @test */
    public function the_name_field_is_required()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();

        $this->actingAs($staff);

        Livewire::test('roles-create')
                ->set('name', '')
                ->call('create')
                ->assertHasErrors(['name' => 'required']);
    }



    /** @test */
    public function the_name_field_must_be_a_string()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();

        $this->actingAs($staff);

        Livewire::test('roles-create')
                ->set('name', 12)
                ->call('create')
                ->assertHasErrors(['name' => 'string']);
    }
    /** @test */
    public function the_name_is_unique_as_user_types()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();

        Role::create(['name' => 'Existing Name', 'guard_name' => 'staff']);

        $this->actingAs($staff);

        Livewire::test('roles-create')
                ->set('name', 'Existing Name')
                ->assertHasErrors(['name' => 'unique']);
    }
}
