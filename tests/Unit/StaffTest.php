<?php

namespace Tests\Unit;

use App\Profile;
use App\ProgressNote;
use App\Staff;
use App\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_have_many_progress_notes()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();

        $noteA = factory(ProgressNote::class)->create([
            'staff_id' => $staff->id,
        ]);

        $noteB = factory(ProgressNote::class)->create([
            'staff_id' => $staff->id,
        ]);

        $this->assertTrue($noteA->is($staff->progressNotes[0]));
        $this->assertTrue($noteB->is($staff->progressNotes[1]));
    }

    /** @test */
    public function has_a_default_profile()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();

        $this->assertNotNull($staff->profile);
    }

    /** @test */
    public function can_have_a_profile()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();
        $profile = factory(Profile::class)->create([
            'profileable_id' => $staff->id,
            'profileable_type' => $staff->getMorphClass(),
        ]);

        $this->assertTrue($profile->is($staff->profile));
    }

    /** @test */
    public function can_make_a_composite_id_for_itself()
    {
        $staff = new Staff(['property_id' => 321, 'api_id' => 12345]);

        $this->assertEquals('321:12345', $staff->makeCompositeKey());
    }

    /** @test */
    public function log_is_created_and_can_be_retrieved_as_staff_action_when_a_new_progress_note_is_created()
    {
        $this->createTenant();
        activity()->enableLogging();

        $staff = factory(Staff::class)->create();
        $this->be($staff);
        $progressNotes = factory(ProgressNote::class)->create();

        $activity = $staff->actions->last();

        $this->assertEquals('progress-notes', $activity->log_name);
        $this->assertEquals($progressNotes->getmorphClass(), $activity->subject_type);
        $this->assertEquals($progressNotes->id, $activity->subject_id);
        $this->assertEquals('created', $activity->description);
        $this->assertEquals($staff->id, $activity->causer_id);
        $this->assertEquals($staff->getmorphClass(), $activity->causer_type);
    }

    /** @test */
    public function can_be_the_creator_of_many_templates()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();
        $template = factory(Template::class)->create(['creator_id' => $staff->id, 'creator_type' => $staff->getMorphClass()]);

        $this->assertTrue($staff->createdTemplates->first()->is($template));
    }
}
