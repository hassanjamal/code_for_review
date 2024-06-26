<?php

namespace Tests\Feature;

use App\Http\Livewire\UserSettings;
use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserSettingsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_staff_member_can_save_a_signature()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();

        $this->assertNull($staff->profile->signature_post_script);

        $this->actingAs($staff);

        Livewire::test(UserSettings::class)
            ->set('signaturePostScript', 'test signature')
            ->call('saveSignature');

        $this->assertEquals('test signature', $staff->fresh()->profile->signature_post_script);
    }
}
