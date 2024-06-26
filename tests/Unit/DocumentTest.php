<?php

namespace Tests\Unit;

use App\Client;
use App\Document;
use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function belongs_to_a_staff_member()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->create();
        $document = factory(Document::class)->create(['staff_id' => $staff->id]);

        $this->assertTrue($staff->is($document->staff));
    }

    /** @test */
    public function belongs_to_a_client()
    {
        $this->createTenant();
        $client = factory(Client::class)->create();
        $document = factory(Document::class)->create(['client_id' => $client->id]);

        $this->assertTrue($client->is($document->client));
    }

    /** @test */
    public function activity_is_tracked()
    {
        $this->createTenant();

        activity()->enableLogging();

        $staff = factory(Staff::class)->create();
        $this->be($staff);

        $document = factory(Document::class)->create();

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'documents',
            'description' => 'created',
            'subject_id' => $document->id,
            'subject_type' => $document->getMorphClass(),
            'causer_id' => $staff->id,
            'causer_type' => $staff->getMorphClass(),
        ]);

        // Update activity is logged.
        $document->update(['content' => 'new']);
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'documents',
            'subject_type' => $document->getMorphClass(),
            'subject_id' => $document->id,
            'description' => 'updated',
            'causer_id' => $staff->id,
            'causer_type' => $staff->getMorphClass(),
        ]);

        // Delete activity is logged.
        $document->delete();
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'documents',
            'subject_type' => $document->getMorphClass(),
            'subject_id' => $document->id,
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
    public function can_get_upload_folders()
    {
        // Upload folder name.
        $this->assertEquals('documents', Document::getUploadFolderName());

        // Upload folder path for tenant.
        $this->assertEquals('tenant_1234/documents/', Document::getUploadFolderPathWithTenantPrefix('1234'));
    }
}
