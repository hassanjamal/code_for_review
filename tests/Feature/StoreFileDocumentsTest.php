<?php

namespace Tests\Feature;

use App\Client;
use App\Document;
use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreFileDocumentsTest extends TestCase
{
    use RefreshDatabase;

    private $tenant;
    public function setUp(): void
    {
        parent::setUp();
        $this->tenant = $this->createTenant();
    }

    /** @test */
    public function the_store_file_document_route_is_note_accessible_to_guests_or_staff_without_create_permissions()
    {
        $validFormParams = $this->validFormParams();

        // Guest Login should redirect.
        $this->post(routeForTenant('clients.file-documents.store', [1]), $validFormParams)
             ->assertRedirect(routeForTenant('login'));

        $staff = factory(Staff::class)->create();
        $client = factory(Client::class)->create();

        // Staff login without permissions should 403.
        $this->actingAs($staff)->post($this->storeDocumentRoute($client), $validFormParams)
             ->assertStatus(403);
    }

    /** @test */
    public function a_file_document_of_type_image_is_created_when_store_route_is_hit_by_staff_with_permission_and_proper_form_fields()
    {
        // When a file upload is initiated, the app streams the file to the /tmp folder on
        // the S3 bucket. Once the file is added there, we hit this endpoint with the file url
        // so that our app can move it to the appropriate directory.
        // S3 fake needs to be set up before the tenant so we have a root instance and
        // not a tenant_tenant-id prefixed root of the bucket. Laravel Vapor
        // automatically puts the tmp files in the /tmp directory in the root of the
        // bucket and deletes files older than 24 hours automatically.

        // Given there is a valid file in the temp directory


        // Add the tmp file to s3 without tenancy initialized.
        tenancy()->end();
        Storage::fake();
        $uuid = Str::uuid()->toString();
        Storage::putFileAs('tmp/', UploadedFile::fake()->image("test-image.jpg", 10, 10), $uuid);
        tenancy()->initialize($this->tenant);


        [$staff, $client] = $this->createStaffAndClient();
        $staff->givePermissionTo('documents:create');

        // Staff login with permissions should create a document in the database and move the temporary S3
        // file to it's proper folder.
        $this->actingAs($staff)->post($this->storeDocumentRoute($client), [
            'name' => 'test name',
            'uuid' => $uuid,
            'key' => 'tmp/'.$uuid,
            'bucket' => 'local-df',
            'original_name' => 'test-image.jpg',
            'content_type' => 'image/jpg',
        ])->assertRedirect(routeForTenant('documents.show', Document::first()));

        $this->assertDatabaseHas('documents', [
            'client_id' => $client->id,
            'staff_id' => $staff->id,
            'name' => 'test name',
            'type' => 'file',
            'content' => null,
            'key' => $uuid,
            'original_name' => 'test-image.jpg',
            'content_type' => 'image/jpg',
        ]);

        // For the same reason we have to initialize S3 before tenancy we have to end tenancy
        // to check the files. This is due to the way stancl/tenancy sets the root of S3 when
        // a tenant is initialized.
        tenancy()->end();

        // The file info should be stored in the db
        // the file should be copied from tmp/ to the tenant progress-notes/documents folder.
        Storage::assertExists('tmp/'.$uuid);
        Storage::assertExists(Document::getUploadFolderPathWithTenantPrefix($this->tenant->id).$uuid);
    }

    /** @test */
    public function validation_rules_for_storing_file_documents()
    {
        [$staff, $client] = $this->createStaffAndClient();
        $staff->givePermissionTo('documents:create');

        // The name field is required and must be a string with max length 255 chars.
        $this->actingAs($staff)
             ->post($this->storeDocumentRoute($client), $this->validFormParams(['name' => '']))
             ->assertSessionHasErrors('name');

        $this->actingAs($staff)
             ->post($this->storeDocumentRoute($client), $this->validFormParams(['name' => ['array']]))
             ->assertSessionHasErrors('name');

        $this->actingAs($staff)
             ->post($this->storeDocumentRoute($client), $this->validFormParams(['name' => Str::random(256)]))
             ->assertSessionHasErrors('name');

        // The UUID field is required and must be a string.
        $this->actingAs($staff)
             ->post($this->storeDocumentRoute($client), $this->validFormParams([ 'uuid' => '',]))
             ->assertSessionHasErrors('uuid');

        $this->actingAs($staff)
             ->post($this->storeDocumentRoute($client), $this->validFormParams(['uuid' => ['not a string']]))
             ->assertSessionHasErrors('uuid');

        // The key field is required and must be a string.
        $this->actingAs($staff)
             ->post($this->storeDocumentRoute($client), $this->validFormParams(['key' => '']))
             ->assertSessionHasErrors('key');

        $this->actingAs($staff)
             ->post($this->storeDocumentRoute($client), $this->validFormParams(['key' => ['not a string']]))
             ->assertSessionHasErrors('key');

        // The bucket field is required and must be a string.
        $this->actingAs($staff)
             ->post($this->storeDocumentRoute($client), $this->validFormParams(['bucket' => '']))
             ->assertSessionHasErrors('bucket');

        $this->actingAs($staff)
             ->post($this->storeDocumentRoute($client), $this->validFormParams(['bucket' => ['not a string']]))
             ->assertSessionHasErrors('bucket');

        // The original_name field is required and must be a string.
        $this->actingAs($staff)
             ->post($this->storeDocumentRoute($client), $this->validFormParams(['original_name' => '']))
             ->assertSessionHasErrors('original_name');

        $this->actingAs($staff)
             ->post($this->storeDocumentRoute($client), $this->validFormParams(['original_name' => ['not a string']]))
             ->assertSessionHasErrors('original_name');

        // The content_type field is required and must be a string.
        $this->actingAs($staff)
             ->post($this->storeDocumentRoute($client), $this->validFormParams(['content_type' => '']))
             ->assertSessionHasErrors('content_type');

        $this->actingAs($staff)
             ->post($this->storeDocumentRoute($client), $this->validFormParams(['content_type' => ['not a string']]))
             ->assertSessionHasErrors('content_type');
    }

    protected function createStaffAndClient()
    {
        return [
            factory(Staff::class)->create(),
            factory(Client::class)->create(),
        ];
    }

    private function validFormParams($overrides = [])
    {
        return [
            'name' => $overrides['name'] ?? 'test name',
            'uuid' => $overrides['uuid'] ?? 'test uuid',
            'key' => $overrides['key'] ?? 'test key',
            'bucket' => $overrides['bucket'] ?? 'test bucket',
            'original_name' => $overrides['original_name'] ?? 'test original name',
            'content_type' => $overrides['content_type'] ?? 'test content type',
        ];
    }

    protected function storeDocumentRoute($client)
    {
        return routeForTenant('clients.file-documents.store', $client);
    }
}
