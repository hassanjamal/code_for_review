<?php

namespace Tests\Unit;

use App\Alert;
use App\Client;
use App\Property;
use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class AlertTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_alert_is_created_by_an_staff()
    {
        $this->createTenant();

        $property = factory(Property::class)->create();

        $staff = factory(Staff::class)->create(['api_id' => 100, 'property_id' => $property->id]);

        $alert = factory(Alert::class)->create(['staff_id' => $staff->id]);

        $this->assertTrue($alert->is($staff->alerts()->first()));
    }

    /** @test */
    public function an_alert_is_created_for_a_client()
    {
        $this->createTenant();

        $property = factory(Property::class)->create();

        $client = factory(Client::class)->create(['property_id' => $property->id]);

        $alert = factory(Alert::class)->create(['client_id' => $client->id]);

        $this->assertTrue($alert->is($client->alerts()->first()));
    }

    /** @test */
    public function activity_is_logged()
    {
        $this->createTenant();
        activity()->enableLogging();
        $staff = factory(Staff::class)->create();
        $this->be($staff);

        // Create activity is logged.
        $alert = factory(Alert::class)->create();

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'alerts',
            'description' => 'created',
            'subject_id' => $alert->id,
            'subject_type' => $alert->getMorphClass(),
            'causer_id' => $staff->id,
            'causer_type' => $staff->getMorphClass(),
        ]);

        // Update activity is logged.
        $alert->update(['text' => 'new']);
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'alerts',
            'subject_type' => $alert->getMorphClass(),
            'subject_id' => $alert->id,
            'description' => 'updated',
            'causer_id' => $staff->id,
            'causer_type' => $staff->getMorphClass(),
        ]);

        // Delete activity is logged.
        $alert->delete();
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'alerts',
            'subject_type' => $alert->getMorphClass(),
            'subject_id' => $alert->id,
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
    public function it_encrypts_the_text_before_strong_in_db()
    {
        $this->createTenant();
        $note = factory(Alert::class)->create([
            'text' => 'Some Random Text',
        ]);

        $this->assertEquals('Some Random Text', decrypt($note->getRawOriginal('text')));
    }
}
