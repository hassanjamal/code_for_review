<?php

namespace Tests\Unit;

use App\Profile;
use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_belong_to_a_staff_member()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->create();
        $profile = factory(Profile::class)->create(['profileable_id' => $staff->id, 'profileable_type' => $staff->getMorphClass()]);

        $this->assertTrue($profile->profileable->is($staff));
    }
}
