<?php

namespace Tests\Feature;

use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AssignRolesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_search_for_staff()
    {
        $this->createTenant();

        $staffA = factory(Staff::class)->create(['first_name' => 'Test-First-NameA', 'last_name' => 'Test-Last-NameA']);
        $staffB = factory(Staff::class)->create(['first_name' => 'Test-First-NameB', 'last_name' => 'Test-Last-NameB']);

        Livewire::test('roles-assign')
            ->set('search', $staffA->first_name)
            ->assertSee($staffA->first_name)
            ->assertSee($staffA->last_name)
            ->assertDontSee($staffB->first_name)
            ->assertDontSee($staffB->last_name);
    }
}
