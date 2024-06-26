<?php

namespace Tests\Feature;

use App\ProgressNote;
use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProgressNoteImagesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_endpoint_is_not_accessible_by_guests()
    {
        $this->createTenant();
        $note = factory(ProgressNote::class)->create();

        $this->post(routeForTenant('progress-notes.images.store', $note), $this->validParams())
            ->assertStatus(302)
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function the_route_requires_proper_permissions_to_be_accessed()
    {
        $this->createTenant();

        $note = factory(ProgressNote::class)->create();
        $staff = $note->staff;
        //$staff->givePermissionTo('notes:view-all');

        $this->actingAs($staff)->post(routeForTenant('progress-notes.images.store', $note), $this->validParams())
            ->assertStatus(404);
    }

    /** @test */
    public function the_image_is_moved_to_the_tenant_folder_and_details_in_local_database()
    {
        $this->withoutExceptionHandling();
        // S3 fake needs to be set up before the tenant so we have a root instance and
        // not a tenant_tenant-id prefixed root of the bucket. Laravel Vapor
        // automatically puts the tmp files in the /tmp directory in the root of the
        // bucket and deletes files older than 24 hours automatically.

        // Given there is a valid file in the temp directory
        Storage::fake();
        $uuid = Str::uuid();
        // Add the tmp file to s3 without tenancy initialized.
        Storage::putFileAs('tmp/', UploadedFile::fake()->image("test-image.jpg", 10, 10), $uuid);

        $tenant = $this->createTenant();
        $note = factory(ProgressNote::class)->create();
        $staff = $note->staff;
        $staff->givePermissionTo(['notes:create', 'notes:view-all']);

        // And the endpoint is hit with info about that file
        $this->actingAs($staff)->post(routeForTenant('progress-notes.images.store', $note), [
            'key' => 'tmp/'.$uuid,
            'bucket' => 'local-df',
            'content_type' => 'image/jpg',
        ]);

        $this->assertDatabaseHas('progress_note_images', [
            'progress_note_id' => $note->id,
            'key' => $uuid,
            'bucket' => 'local-df',
            'content_type' => 'image/jpg',
        ]);

        // For the same reason we have to intialize S3 before tenancy we have to end tenancy
        // to check the files. This is due to the way stancl/tenancy sets the root of S3 when
        // a tenant is initialized.
        tenancy()->end();

        // The file info should be stored in the db
        // the file should be copied from tmp/ to the tenant progress-notes/documents folder.
        Storage::assertExists('tmp/'.$uuid);
        Storage::assertExists('tenant_'.$tenant->id.'/progress-note-images/'.$uuid);
    }

    /** @test */
    public function a_404_is_thrown_if_the_note_does_not_exist()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->state('staff')->create();

        $this->actingAs($staff)->post(route('progress-notes.images.store', 1), $this->validParams())
            ->assertStatus(404);
    }

    /** @test */
    public function the_image_cannot_be_saved_by_a_staff_member_that_does_not_own_the_progress_note()
    {
        $this->createTenant();

        $note = factory(ProgressNote::class)->create();
        $invalidStaff = factory(Staff::class)->state('staff')->create();

        $this->actingAs($invalidStaff)->post(routeForTenant('progress-notes.images.store', $note), $this->validParams())
            ->assertStatus(403);
    }

    /** @test */
    public function the_key_must_be_a_string_and_is_required()
    {
        $this->createTenant();

        $note = factory(ProgressNote::class)->create();
        $note->staff->givePermissionTo(['notes:create', 'notes:view-all']);

        $this->actingAs($note->staff)->post(routeForTenant('progress-notes.images.store', $note), [
            'uuid' => $uuid =Str::uuid(),
            'key' => '',//'tmp/'.$uuid,
            'bucket' => 'local-df',
            'content_type' => 'image/jpg',
        ])->assertSessionHasErrors('key');

        $this->actingAs($note->staff)->post(routeForTenant('progress-notes.images.store', $note), [
            'uuid' => $uuid =Str::uuid(),
            'key' => true,//'tmp/'.$uuid,
            'bucket' => 'local-df',
            'content_type' => 'image/jpg',
        ])->assertSessionHasErrors('key');
    }

    /** @test */
    public function the_bucket_must_be_a_string_and_is_required()
    {
        $this->createTenant();

        $note = factory(ProgressNote::class)->create();
        $note->staff->givePermissionTo(['notes:create', 'notes:view-all']);

        $this->actingAs($note->staff)->post(routeForTenant('progress-notes.images.store', $note), [
            'uuid' => $uuid =Str::uuid(),
            'key' => 'tmp/'.$uuid,
            'bucket' => '', //local-df',
            'content_type' => 'image/jpg',
        ])->assertSessionHasErrors('bucket');

        $this->actingAs($note->staff)->post(routeForTenant('progress-notes.images.store', $note), [
            'uuid' => $uuid =Str::uuid(),
            'key' => 'tmp/'.$uuid,
            'bucket' => [], //'local-df',
            'content_type' => 'image/jpg',
        ])->assertSessionHasErrors('bucket');
    }

    /** @test */
    public function the_content_type_must_be_a_string_and_is_required()
    {
        $this->createTenant();

        $note = factory(ProgressNote::class)->create();
        $note->staff->givePermissionTo(['notes:create', 'notes:view-all']);

        $this->actingAs($note->staff)->post(routeForTenant('progress-notes.images.store', $note), [
            'uuid' => $uuid =Str::uuid(),
            'key' => 'tmp/'.$uuid,
            'bucket' => 'local-df',
            'content_type' => '', //'image/jpg',
        ])->assertSessionHasErrors('content_type');

        $this->actingAs($note->staff)->post(routeForTenant('progress-notes.images.store', $note), [
            'uuid' => $uuid =Str::uuid(),
            'key' => 'tmp/'.$uuid,
            'bucket' => 'local-df',
            'content_type' => 10, //'image/jpg',
        ])->assertSessionHasErrors('content_type');
    }

    protected function validParams($overrides = [])
    {
        return [
            'uuid' => $uuid = Str::uuid(),
            'key' => 'tmp/'.$uuid,
            'bucket' => 'local-df',
            'content_type' => 'image/jpg',
        ];
    }
}
