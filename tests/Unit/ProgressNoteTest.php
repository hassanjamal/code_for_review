<?php

namespace Tests\Unit;

use App\Appointment;
use App\Client;
use App\ProgressNote;
use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class ProgressNoteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_staff_member()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();
        $note = factory(ProgressNote::class)->create([
            'staff_id' => $staff->id,
        ]);

        $this->assertTrue($staff->is($note->staff));
    }

    /** @test */
    public function it_belongs_to_a_client()
    {
        $this->createTenant();

        $client = factory(Client::class)->create();
        $note = factory(ProgressNote::class)->create([
            'client_id' => $client->id,
        ]);

        $this->assertTrue($client->is($note->client));
    }

    /** @test */
    public function it_can_belong_to_an_appointment()
    {
        $this->createTenant();

        $appointment = factory(Appointment::class)->create();

        $note = factory(ProgressNote::class)->create([
            'staff_id' => $appointment->staff_id,
            'client_id' => $appointment->client->id,
            'notable_id' => $appointment->id,
            'notable_type' => 'appointments',
        ]);

        $this->assertTrue($note->is($appointment->progressNotes->first()));
    }

    /** @test */
    public function it_casts_draft_status_to_boolean()
    {
        $this->createTenant();

        $noteA = factory(ProgressNote::class)->create(['is_draft' => 1]);
        $noteB = factory(ProgressNote::class)->create(['is_draft' => 0]);

        $this->assertSame(true, $noteA->is_draft);
        $this->assertSame(false, $noteB->is_draft);
    }

    /** @test */
    public function it_casts_exam_status_to_boolean()
    {
        $this->createTenant();

        $noteA = factory(ProgressNote::class)->create(['is_exam' => 1]);
        $noteB = factory(ProgressNote::class)->create(['is_exam' => 0]);

        $this->assertSame(true, $noteA->is_exam);
        $this->assertSame(false, $noteB->is_exam);
    }

    /** @test */
    public function can_be_scoped_to_a_staff_member()
    {
        $this->createTenant();

        $staffA = factory(Staff::class)->create();
        $noteA = factory(ProgressNote::class)->create(['staff_id' => $staffA->id]);

        $staffB = factory(Staff::class)->create();
        $noteB = factory(ProgressNote::class)->create(['staff_id' => $staffB->id]);

        $staffC = factory(Staff::class)->create();
        $noteC = factory(ProgressNote::class)->create(['staff_id' => $staffC->id]);

        $actual = ProgressNote::forStaff($staffA)->get();

        $this->assertCount(1, $actual);
        $this->assertTrue($staffA->is($actual->first()->staff));
    }

    /** @test */
    public function activity_is_logged()
    {
        $this->createTenant();
        activity()->enableLogging();
        $staff = factory(Staff::class)->create();

        // Authenticate as the staff member.
        $this->be($staff);

        // Create activity is logged.
        $progressNote = factory(ProgressNote::class)->create();

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'progress-notes',
            'description' => 'created',
            'subject_id' => $progressNote->id,
            'subject_type' => $progressNote->getMorphClass(),
            'causer_id' => $staff->id,
            'causer_type' => $staff->getMorphClass(),
        ]);

        // Update activity is logged.
        $progressNote->update(['content' => 'new']);
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'progress-notes',
            'subject_type' => $progressNote->getMorphClass(),
            'subject_id' => $progressNote->id,
            'description' => 'updated',
            'causer_id' => $staff->id,
            'causer_type' => $staff->getMorphClass(),
        ]);

        // Delete activity is logged.
        $progressNote->delete();
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'progress-notes',
            'subject_type' => $progressNote->getMorphClass(),
            'subject_id' => $progressNote->id,
            'description' => 'deleted',
            'causer_id' => $staff->id,
            'causer_type' => $staff->getMorphClass(),
        ]);

        // No PHI data should be set in the extra properties field.
        Activity::all()->map(function ($activity) {
            $this->assertCount(0, $activity->properties);
        });
    }

    /** @test */
    public function respects_permissions_of_user()
    {
        $this->createTenant();

        $staffCanViewNoNotes = factory(Staff::class)->create();
        $noteA = factory(ProgressNote::class)->create(['staff_id' => $staffCanViewNoNotes->id]);
        $noteB = factory(ProgressNote::class)->create(['staff_id' => $staffCanViewNoNotes->id]);

        $staffCanViewOwnNotes = factory(Staff::class)->create();
        $staffCanViewOwnNotes->givePermissionTo('notes:view-own');
        $noteC = factory(ProgressNote::class)->create(['staff_id' => $staffCanViewOwnNotes->id]);
        $noteD = factory(ProgressNote::class)->create(['staff_id' => $staffCanViewOwnNotes->id]);

        $staffCanViewAllNotes = factory(Staff::class)->create();
        $staffCanViewAllNotes->givePermissionTo('notes:view-all');
        $noteE = factory(ProgressNote::class)->create(['staff_id' => $staffCanViewAllNotes->id]);
        $noteF = factory(ProgressNote::class)->create(['staff_id' => $staffCanViewAllNotes->id]);

        $noAuth = ProgressNote::all();
        $this->assertCount(6, $noAuth);

        $this->be($staffCanViewNoNotes);
        $staffCanViewNoNotesQuery = ProgressNote::all();
        $this->assertCount(0, $staffCanViewNoNotesQuery);

        $this->be($staffCanViewOwnNotes);
        $staffCanViewOwnNotesQuery = ProgressNote::all();
        $this->assertCount(2, $staffCanViewOwnNotesQuery);
        $this->assertTrue($noteC->is($staffCanViewOwnNotesQuery[0]));
        $this->assertTrue($noteD->is($staffCanViewOwnNotesQuery[1]));

        $this->be($staffCanViewAllNotes);
        $staffCQuery = ProgressNote::all();
        $this->assertCount(6, $staffCQuery);
        $this->assertTrue($noteA->is($staffCQuery[0]));
        $this->assertTrue($noteB->is($staffCQuery[1]));
        $this->assertTrue($noteC->is($staffCQuery[2]));
        $this->assertTrue($noteD->is($staffCQuery[3]));
        $this->assertTrue($noteE->is($staffCQuery[4]));
        $this->assertTrue($noteF->is($staffCQuery[5]));
    }

    /** @test */
    public function it_encrypts_the_content_before_strong_in_db()
    {
        $this->createTenant();
        $note = factory(ProgressNote::class)->create([
            'content' => 'Some Random Content',
        ]);

        $this->assertEquals('Some Random Content', decrypt($note->getRawOriginal('content')));
    }

    /** @test */
    public function it_encrypts_the_meta_before_strong_in_db()
    {
        $this->createTenant();
        $note = factory(ProgressNote::class)->create([
            'meta' => [
                'key1' =>'Some Random Content',
                'key2' => 'Another Random Content',
            ],
        ]);

        $this->assertEquals([
            'key1' =>'Some Random Content',
            'key2' => 'Another Random Content',
        ], json_decode(decrypt($note->getRawOriginal('meta')), true));
    }
}
