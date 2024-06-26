<?php

namespace Tests\Feature;

use App\ManagedImage;
use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class ImageManagerUploadsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_endpoint_is_not_accessible_to_guests()
    {
        $this->createTenant();

        $this->post(routeForTenant('image-manager.store'))->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function the_endpoint_is_accessible_to_staff_with_proper_credentials_otherwise_forbidden()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();
        $this->actingAs($staff)->post(routeForTenant('image-manager.store'))->assertStatus(403);

        $staff->givePermissionTo('imageManager:store');
        $this->actingAs($staff)->post(routeForTenant('image-manager.store'))->assertStatus(302);
    }

    /** @test */
    public function the_image_is_moved_to_the_tenant_folder_and_info_is_saved_in_db()
    {
        // S3 fake needs to be set up before the tenant so we have a root instance and
        // not a tenant_tenant-id prefixed root of the bucket. Laravel Vapor
        // automatically puts the tmp files in the /tmp directory in the root of the
        // bucket and deletes files older than 24 hours automatically.

        // Given there is a valid file in the temp directory
        Storage::fake();
        $uuid = Uuid::uuid4()->toString();
        // Add the tmp file to s3 without tenancy initialized.
        Storage::putFileAs('tmp/', UploadedFile::fake()->image("test-image.jpg", 10, 10), $uuid);

        $tenant = $this->createTenant();

        // And the endpoint is hit with info about that file
        $staff = factory(Staff::class)->state('staff')->create();
        $this->actingAs($staff)->post(routeForTenant('image-manager.store'), [
            'uuid' => $uuid,
            'key' => 'tmp/'.$uuid,
            'bucket' => 'local-df',
            'name' => 'test-image.jpg',
            'content_type' => 'image/jpg',
        ]);

        $this->assertDatabaseHas('managed_images', [
            'key' => $uuid,
            'original_name' => 'test-image.jpg',
            'content_type' => 'image/jpg',
        ]);

        // For the same reason we have to intialize S3 before tenancy we have to end tenancy
        // to check the files. This is due to the way stancl/tenancy sets the root of S3 when
        // a tenant is initialized.
        tenancy()->end();

        // The file info should be stored in the db
        // the file should be copied from tmp/ to the tenant progress-notes/documents folder.
        Storage::assertExists('tmp/'.$uuid);
        Storage::assertExists(ManagedImage::getUploadFolderPathWithTenantPrefix($tenant->id).$uuid);
    }
}
