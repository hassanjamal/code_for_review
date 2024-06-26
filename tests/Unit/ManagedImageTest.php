<?php

namespace Tests\Unit;

use App\ManagedImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;

class ManagedImageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_upload_folders()
    {
        // Upload folder name.
        $this->assertEquals('image-manager', ManagedImage::getUploadFolderName());

        // Upload folder path for tenant.
        $this->assertEquals('tenant_1234/image-manager/', ManagedImage::getUploadFolderPathWithTenantPrefix('1234'));
    }
}
