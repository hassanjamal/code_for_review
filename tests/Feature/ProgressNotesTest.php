<?php

namespace Tests\Feature;

use App\ProgressNote;
use App\Staff;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressNotesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_index_page_is_not_viewable_by_guests()
    {
        $this->createTenant();

        $this->assertTrue(auth()->guest());

        $this->get(routeForTenant('progress-notes.index'))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function can_get_formatted_date_of_service()
    {
        $this->createTenant();
        $note = factory(ProgressNote::class)->create([
            'date_of_service' => now()->parse('January 1st 2020'),
        ]);

        $this->assertEquals('Jan 01, 2020', $note->formatted_date_of_service);
    }
    /** @test */
    public function the_index_page_is_not_viewable_by_authenticated_users_without_proper_permissions()
    {
        $this->createTenant();
        $user = factory(User::class)->create(); // Cannot view properties

        $this->actingAs($user)->get(routeForTenant('progress-notes.index'))
             ->assertStatus(403);
    }

    /** @test */
    public function the_index_page_is_viewable_by_authenticated_users_with_proper_permissions()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->create();
        $staff->givePermissionTo('notes:view-all');

        $this->actingAs($staff)->get(routeForTenant('progress-notes.index'))->assertOk();
    }

    /** @test */
    public function the_index_page_is_not_viewable_by_authenticated_staff_without_proper_permissions()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->create(); // Has no permissions or roles, cannot view properties.

        $this->actingAs($staff)->get(routeForTenant('progress-notes.index'))->assertStatus(403);
    }

    /** @test */
    public function the_index_page_is_viewable_by_authenticated_staff_with_proper_permissions()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->state('staff')->create(); // Has staff role.

        $this->actingAs($staff)->get(routeForTenant('progress-notes.index'))->assertOk();
    }

    /** @test */
    public function the_create_page_is_not_viewable_by_guests()
    {
        $this->createTenant();

        $this->assertTrue(auth()->guest());

        $this->get(routeForTenant('progress-notes.create'))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function the_create_page_is_viewable_by_authenticated_users()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->create();
        $staff->givePermissionTo('notes:view-all');


        $this->actingAs($staff)->get(routeForTenant('progress-notes.create'))
             ->assertOk()
             ->assertComponentIs('ProgressNotes/Create');
    }

    /** @test */
    public function the_edit_page_is_viewable_by_authenticated_staff_members()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->state('staff')->create();
        $ProgressNote = factory(ProgressNote::class)->create();

        $this->actingAs($staff)->get(routeForTenant('progress-notes.edit', $ProgressNote))
             ->assertComponentIs('ProgressNotes/Edit')
             ->assertHasProp('progressNote', $ProgressNote);

        $unAuthorizedUser = factory(Staff::class)->create();
        $this->actingAs($unAuthorizedUser)->get(routeForTenant('progress-notes.edit', $ProgressNote))
             ->assertStatus(404);
    }
}
