<?php

namespace Tests\Feature;

use App\Http\Livewire\RoleSelector;
use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RoleSelectorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function staff_can_be_assigned_role()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();

        $this->assertFalse($staff->hasRole('staff'));

        Livewire::test(RoleSelector::class, ['person' => $staff])
            ->call('assignRole', 'Staff');

        $this->assertTrue($staff->fresh()->hasRole('Staff'));
    }
}
