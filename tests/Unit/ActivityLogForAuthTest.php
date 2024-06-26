<?php

namespace Tests\Unit;

use App\Property;
use App\Staff;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class ActivityLogForAuthTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        activity()->enableLogging();
    }

    /** @test */
    public function log_is_created_when_a_tenant_staff_login()
    {
        $this->createTenant();

        factory(Property::class)->create();

        $this->post(routeForTenant('login.mindbody'), [
            'username' => 'valid-test-staff',
            'password' => '@tempPW1234',
            'apiIdentifier' => '-99787',
        ], ['REMOTE_ADDR' => '10.1.0.1']);

        $authStaff = Staff::first();

        $activity = Activity::all()->last();


        $this->assertEquals('auth-log', $activity->log_name);
        $this->assertEquals($authStaff->getmorphClass(), $activity->subject_type);
        $this->assertEquals($authStaff->id, $activity->subject_id);
        $this->assertEquals('logged in', $activity->description);
        $this->assertNotNull($activity->getExtraProperty('ip'));
        $this->assertEquals('10.1.0.1', $activity->getExtraProperty('ip'));
    }

    /** @test */
    public function log_is_created_when_a_tenant_user_login()
    {
        $this->createTenant();

        $user = factory(User::class)->create();

        $this->post(routeForTenant('login'), [
            'email' => $user->email,
            'password' => 'password',
        ], ['REMOTE_ADDR' => '10.1.0.1']);

        $activity = Activity::all()->last();

        $this->assertEquals('auth-log', $activity->log_name);
        $this->assertEquals($user->getmorphClass(), $activity->subject_type);
        $this->assertEquals($user->id, $activity->subject_id);
        $this->assertEquals('logged in', $activity->description);
        $this->assertNotNull($activity->getExtraProperty('ip'));
        $this->assertEquals('10.1.0.1', $activity->getExtraProperty('ip'));
    }

    /** @test */
    public function log_is_created_when_a_tenant_user_log_fails()
    {
        $this->createTenant();

        $user = factory(User::class)->create();

        $this->post(routeForTenant('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ], ['REMOTE_ADDR' => '10.1.0.1']);

        $activity = Activity::all()->last();

        $this->assertEquals('auth-log', $activity->log_name);
        $this->assertEquals($user->getmorphClass(), $activity->subject_type);
        $this->assertEquals($user->id, $activity->subject_id);
        $this->assertEquals('login failed', $activity->description);
        $this->assertNotNull($activity->getExtraProperty('ip'));
        $this->assertEquals('10.1.0.1', $activity->getExtraProperty('ip'));
    }

    /** @test */
    public function log_is_created_when_a_tenant_user_logs_out()
    {
        $tenant = $this->createTenant();

        $user = factory(User::class)->create();

        $this->be($user)->post(routeForTenant('logout'));

        $tenant->run(function () use ($user) {
            $activity = Activity::all()->last();

            $this->assertEquals('auth-log', $activity->log_name);
            $this->assertEquals($user->getmorphClass(), $activity->subject_type);
            $this->assertEquals($user->id, $activity->subject_id);
            $this->assertEquals('logged out', $activity->description);
        });
    }

    /** @test */
    public function log_is_created_when_a_tenant_staff_logs_out()
    {
        $tenant = $this->createTenant();

        $user = factory(Staff::class)->state('staff')->create();

        $this->be($user)->post(routeForTenant('logout'));

        $tenant->run(function () use ($user) {
            $activity = Activity::all()->last();

            $this->assertEquals('auth-log', $activity->log_name);
            $this->assertEquals($user->getmorphClass(), $activity->subject_type);
            $this->assertEquals($user->id, $activity->subject_id);
            $this->assertEquals('logged out', $activity->description);
        });
    }

    /** @test */
    public function log_is_created_when_a_tenant_user_reset_password()
    {
        $this->createTenant();
        activity()->enableLogging();

        $user = factory(User::class)->create();
        $token = Password::createToken($user);

        $this->post(routeForTenant('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newPassword',
            'password_confirmation' => 'newPassword',
        ], ['REMOTE_ADDR' => '10.1.0.1']);

        $activity = Activity::all()->first();

        $this->assertEquals('auth-log', $activity->log_name);
        $this->assertEquals($user->getmorphClass(), $activity->subject_type);
        $this->assertEquals($user->id, $activity->subject_id);
        $this->assertEquals('password reset', $activity->description);
        $this->assertNotNull($activity->getExtraProperty('ip'));
        $this->assertEquals('10.1.0.1', $activity->getExtraProperty('ip'));
    }
}
