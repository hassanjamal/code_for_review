<?php

namespace Tests\Unit;

use App\Staff;
use App\Template;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_belong_to_a_staff_member()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();
        $template = factory(Template::class)->create([
            'creator_id' => $staff->id,
            'creator_type' => $staff->getMorphClass(),
        ]);

        $this->assertTrue($staff->is($template->creator));
    }

    /** @test */
    public function it_can_belong_to_a_user()
    {
        $this->createTenant();

        $user = factory(User::class)->create();
        $template = factory(Template::class)->create([
            'creator_id' => $user->id,
            'creator_type' => $user->getMorphClass(),
        ]);

        $this->assertTrue($user->is($template->creator));
    }

    /** @test */
    public function activity_is_logged_properly()
    {
        $this->createTenant();
        activity()->enableLogging();
        $staff = factory(Staff::class)->create();

        $this->be($staff);

        $template = $staff->createdTemplates()->save(factory(Template::class)->make(['content' => 'foo']));

        $template->update(['content' => 'bar']);

        $template->delete();

        $this->assertDatabaseHas('activity_log', [
            // Activity for creating.
            'log_name' => 'templates',
            'description' => 'created',
            'subject_id' => $template->id,
            'subject_type' => $template->getMorphClass(),
            'causer_id' => $staff->id,
            'causer_type' => $staff->getMorphClass(),

            // Activity for updating.
            'log_name' => 'templates',
            'description' => 'updated',
            'subject_id' => $template->id,
            'subject_type' => $template->getMorphClass(),
            'causer_id' => $staff->id,
            'causer_type' => $staff->getMorphClass(),

            // Activity for deleting.
            'log_name' => 'templates',
            'description' => 'deleted',
            'subject_id' => $template->id,
            'subject_type' => $template->getMorphClass(),
            'causer_id' => $staff->id,
            'causer_type' => $staff->getMorphClass(),
        ]);
    }
}
