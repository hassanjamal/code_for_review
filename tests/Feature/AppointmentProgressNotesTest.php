<?php

namespace Tests\Feature;

use App\Appointment;
use App\Client;
use App\ProgressNote;
use App\Staff;
use App\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AppointmentProgressNotesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function create_view_cannot_be_accessed_by_guests()
    {
        $this->createTenant();

        $appointment = factory(Appointment::class)->create();

        $this->get(routeForTenant('appointment.progress-notes.create', $appointment))->assertRedirect('app/login');
    }

    /** @test */
    public function create_view_can_be_accessed_by_staff_members_with_proper_permissions()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();
        $staff->givePermissionTo(['notes:view-all', 'notes:create', 'clients:view-from-all-properties']); // Create notes is required.

        $appointment = factory(Appointment::class)->create(['property_id' => $staff->property->id]);

        $this->actingAs($staff, 'staff')->get(routeForTenant('appointment.progress-notes.create', $appointment))->assertStatus(200);

        $staff->revokePermissionTo('notes:create');
        $this->actingAs($staff, 'staff')->get(routeForTenant('appointment.progress-notes.create', $appointment))->assertStatus(403);
    }

    /** @test */
    public function the_create_view_cannot_be_accessed_when_a_note_for_the_appointment_by_the_auth_user_exists()
    {
        $this->createTenant();

        // Set up an appointment that has a note by the logged in user.
        $staff = factory(Staff::class)->state('staff')->create();
        $appointment = factory(Appointment::class)->create(['property_id' => $staff->property->id]);
        $appointment->progressNotes()->create([
            'staff_id' => $staff->id,
            'content' => '<p>Appointment note text</p>',
            'client_id' => factory(Client::class)->create()->id,
            'date_of_service' => now(),
            'is_draft' => true,
        ]);

        $this->actingAs($staff, 'staff')->get(routeForTenant('appointment.progress-notes.create', $appointment))->assertStatus(403);
    }

    /** @test */
    public function create_view_loads_the_proper_view_and_props()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->state('staff')->create();
        $appointment = factory(Appointment::class)->create(['property_id' => $staff->property->id]);

        $this->actingAs($staff, 'staff')->get(routeForTenant('appointment.progress-notes.create', $appointment))->assertComponentIs('ProgressNotes/Appointments/Create')->assertPropValue('appointment', function (
                $propAppointment
            ) use ($appointment) {
            $this->assertEquals($propAppointment['id'], $appointment->id);
        })->assertPropValue('client', function ($propClient) use ($appointment) {
            $this->assertEquals($propClient['id'], $appointment->client->id);
        })->assertHasProp('templates')->assertHasProp('images');
    }

    /** @test */
    public function if_a_copied_note_id_is_supplied_the_content_will_be_set_to_the_copied_note_content()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->state('staff')->create();
        $appointment = factory(Appointment::class)->create(['property_id' => $staff->property->id]);
        $noteToCopy = factory(ProgressNote::class)->create(['content' => 'from copied note']);

        $this->actingAs($staff, 'staff')->get(routeForTenant('appointment.progress-notes.create', ['appointment' => $appointment, 'copiedNoteId' => $noteToCopy->id]))
            ->assertOk()
            ->assertPropValue('copiedContent', function ($propCopiedContent) {
                $this->assertEquals('from copied note', $propCopiedContent);
            });
    }

    /** @test */
    public function templates_on_create_view_get_tags_replaced()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->state('staff')->create();

        $appointmentForJohn = factory(Appointment::class)->create([
            'property_id' => $staff->property->id,
            'service_name' => 'Test Appointment',
            'client_api_public_id' => factory(Client::class)->create([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'birth_date' => now()->parse('January 1st 1980'),
                'gender' => 'male',
            ])->api_public_id,
        ]);

        $appointmentForJane = factory(Appointment::class)->create([
            'property_id' => $staff->property->id,
            'service_name' => 'Test Appointment',
            'client_api_public_id' => factory(Client::class)->create([
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'birth_date' => now()->parse('January 1st 1990'),
                'gender' => 'female',
            ])->api_public_id,
        ]);

        $templateForJohn = factory(Template::class)->create([
            'creator_id' => $staff->id,
            'creator_type' => $staff->getMorphClass(),
            'content' => 'Hello {$client:first_name} {$client:last_name} ({$client:full_name}).
                          You are a {$client:age} year old {$client:gender}, and you were born {$client:dob}.
                          You are here for your {$appointment:service_name} visit.
                          Pronouns work too, {$client:him_her}, {$client:his_her}, {$client:he_she}',
            'default_group_name' => 'TEST',
        ]);

        $templateForJane = factory(Template::class)->create([
            'creator_id' => $staff->id,
            'creator_type' => $staff->getMorphClass(),
            'content' => 'Hello {$client:first_name} {$client:last_name} ({$client:full_name}).
                          You are a {$client:age} year old {$client:gender}, and you were born {$client:dob}.
                          You are here for your {$appointment:service_name} visit.
                          Pronouns work too, {$client:him_her}, {$client:his_her}, {$client:he_she}',
            'default_group_name' => 'TEST',
        ]);

        $this->actingAs($staff, 'staff')->get(routeForTenant('appointment.progress-notes.create', $appointmentForJohn))->assertComponentIs('ProgressNotes/Appointments/Create')->assertPropValue('templates', function (
                $propTemplates
            ) {
            $this->assertEquals(
                'Hello John Doe (John Doe).
                          You are a 40 year old male, and you were born Jan 1, 1980.
                          You are here for your Test Appointment visit.
                          Pronouns work too, him, his, he',
                $propTemplates['TEST'][0]['content']);
        });

        $this->actingAs($staff, 'staff')->get(routeForTenant('appointment.progress-notes.create', $appointmentForJane))->assertComponentIs('ProgressNotes/Appointments/Create')->assertPropValue('templates', function (
                $propTemplates
            ) {
            $this->assertEquals(
                'Hello Jane Doe (Jane Doe).
                          You are a 30 year old female, and you were born Jan 1, 1990.
                          You are here for your Test Appointment visit.
                          Pronouns work too, her, her, she',
                $propTemplates['TEST'][0]['content']);
        });
    }

    /** @test */
    public function proper_permissions_are_required_to_store_notes()
    {
        $this->createTenant();

        $appointment = factory(Appointment::class)->create([
            'start_date_time' => now()->toDateTimeString(),
        ]);

        $staff = $appointment->staff;
        $staff->givePermissionTo('notes:view-own');

        $this->actingAs($staff)->post(routeForTenant('appointment.progress-notes.store', $appointment), [
            'content' => '<p>this is a note</p>',
        ])->assertStatus(403);
    }

    /** @test */
    public function when_storing_a_note_it_is_saved_with_proper_values_and_redirect()
    {
        $this->createTenant();

        $appointment = factory(Appointment::class)->create([
            'start_date_time' => now()->toDateTimeString(),
        ]);

        $staff = $appointment->staff;
        $staff->givePermissionTo(['notes:create', 'notes:view-all', 'clients:view-from-all-properties']);

        $this->assertTrue($staff->can('notes:create'));

        $this->assertNull($appointment->progressNotes()->forStaff($staff)->first());

        $this->actingAs($staff)->post(routeForTenant('appointment.progress-notes.store', $appointment), [
            'content' => '<p>this is a note</p>',
        ])->assertRedirect(routeForTenant('appointment.progress-notes.edit', [
            $appointment,
            $appointment->progressNotes()->forStaff($staff)->first(),
        ]));

        $staffNotes = $appointment->progressNotes()->forStaff($staff)->get();
        $this->assertCount(1, $staffNotes);
        $actual = $staffNotes->first();

        $this->assertEquals(true, $actual->is_draft);
        $this->assertTrue($staff->is($actual->staff));
        $this->assertTrue($appointment->client->is($actual->client));
        $this->assertEquals('appointments', $actual->notable_type);
        $this->assertEquals($appointment->id, $actual->notable_id);
        $this->assertEquals($appointment->start_date_time, $actual->date_of_service);
    }

    /** @test */
    public function the_edit_view_is_not_accessible_by_guests()
    {
        $this->createTenant();

        $appointment = factory(Appointment::class)->create();
        $note = factory(ProgressNote::class)->create([
            'notable_type' => 'appointments',
            'notable_id' => $appointment->id,
        ]);
        $this->get(routeForTenant('appointment.progress-notes.edit', [
            $appointment,
            $note,
        ]))->assertRedirect('app/login');
    }

    /** @test */
    public function the_edit_view_is_accessible_by_staff_with_proper_permissions()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();
        $staff->givePermissionTo(['notes:view-all', 'notes:create', 'clients:view-from-all-properties']); // Create notes is required.

        $appointment = factory(Appointment::class)->create([
            'property_id' => $staff->property->id,
            'staff_id' => $staff->id,
        ]);
        $note = factory(ProgressNote::class)->create([
            'notable_type' => 'appointments',
            'notable_id' => $appointment->id,
            'staff_id' => $staff->id,
        ]);

        $this->actingAs($staff, 'staff')->get(routeForTenant('appointment.progress-notes.edit', [
            $appointment,
            $note,
        ]))->assertStatus(200)->assertHasProp('client')->assertHasProp('appointment')->assertHasProp('templates')->assertHasProp('images');

        $staff->revokePermissionTo('notes:create');
        $this->actingAs($staff, 'staff')->get(routeForTenant('appointment.progress-notes.edit', [
            $appointment,
            $note,
        ]))->assertStatus(403);
    }

    /** @test */
    public function proper_permissions_are_required_to_update_a_note()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();
        $staff->givePermissionTo(['notes:view-all', 'notes:create']); // notes:create is required.

        $appointment = factory(Appointment::class)->create([
            'property_id' => $staff->property->id,
            'staff_id' => $staff->id,
        ]);
        $note = factory(ProgressNote::class)->create([
            'notable_type' => 'appointments',
            'notable_id' => $appointment->id,
            'staff_id' => $staff->id,
            'is_draft' => true,
        ]);

        $this->actingAs($staff, 'staff')->put(routeForTenant('appointment.progress-notes.update', [
            $appointment,
            $note,
        ]), [
            'content' => 'foo',
        ])->assertRedirect(routeForTenant('appointment.progress-notes.edit', [$appointment, $note]));

        $staff->revokePermissionTo('notes:create');
        $this->actingAs($staff, 'staff')->put(routeForTenant('appointment.progress-notes.update', [
            $appointment,
            $note,
        ]), ['content' => 'foo'])->assertStatus(403);
    }

    /** @test */
    public function completed_notes_cannot_be_updated()
    {
        $this->createTenant();

        $appointment = factory(Appointment::class)->create([
            'start_date_time' => now()->toDateTimeString(),
        ]);

        $appointment->staff->givePermissionTo(['notes:view-all', 'notes:create']);

        $note = factory(ProgressNote::class)->create([
            'notable_type' => 'appointments',
            'notable_id' => $appointment->id,
            'content' => 'initial content',
            'staff_id' => $appointment->staff_id,
            'date_of_service' => Carbon::parse($appointment->start_date_time),
            'is_draft' => false, // This means it is completed.
        ]);

        $staff = $note->staff;
        $client = $note->client;

        $this->actingAs($staff)->put(routeForTenant('appointment.progress-notes.update', [$appointment, $note]), [
            'content' => 'updated content',
        ])->assertStatus(403);

        $this->assertCount(1, ProgressNote::all());
        $updatedNote = $note->fresh();

        // Assert nothing was updated.
        $this->assertEquals($appointment->id, $updatedNote->notable_id);
        $this->assertEquals('appointments', $updatedNote->notable_type);
        $this->assertEquals($client->id, $updatedNote->client_id);
        $this->assertEquals(Carbon::parse($appointment->start_date_time), $updatedNote->date_of_service);
        $this->assertEquals($staff->id, $updatedNote->staff_id);
        $this->assertEquals(false, $updatedNote->is_draft);
    }

    /** @test */
    public function the_appointment_must_exist_or_a_404_is_thrown()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->state('staff')->create();

        // Does not exist in the database.
        $appointment = factory(Appointment::class)->make();

        $this->actingAs($staff, 'staff')->get(routeForTenant('appointment.progress-notes.create', $appointment))->assertStatus(404);
    }

    /** @test */
    public function save_notes_validation_fields_work()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->state('staff')->create();
        $appointment = factory(Appointment::class)->create(['property_id' => $staff->property->id]);

        $this->actingAs($staff)->post(routeForTenant('appointment.progress-notes.store', $appointment), [
            'content' => true,
        ])->assertSessionHasErrors(['content']);
    }

    /** @test */
    public function update_notes_validation_fields_work()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->state('staff')->create();
        $appointment = factory(Appointment::class)->create([
            'property_id' => $staff->property->id,
            'staff_id' => $staff->id,
        ]);
        $note = factory(ProgressNote::class)->create([
            'notable_type' => 'appointments',
            'notable_id' => $appointment->id,
            'content' => 'initial content',
            'staff_id' => $appointment->staff_id,
            'date_of_service' => Carbon::parse($appointment->start_date_time),
            'is_draft' => true,
        ]);

        // Content must be a string.
        $this->actingAs($staff)->put(routeForTenant('appointment.progress-notes.update', [$appointment, $note]), [
            'content' => true,
        ])->assertSessionHasErrors(['content']);
    }
}
