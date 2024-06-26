<?php

namespace Tests\Feature;

use App\Alert;
use App\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function alerts_cannot_be_added_by_guests()
    {
        $this->createTenant();

        $this->post(routeForTenant('alerts.store'), ['text' => 'foo'])
            ->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function alerts_cannot_be_added_by_authenticated_users_without_proper_permissions()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->create(); // No role.

        $this->actingAs($staff, 'staff')->post(routeForTenant('alerts.store'), ['text' => 'foo', 'clientId' => '100'])
            ->assertStatus(403);

        $this->assertCount(0, Alert::all());
    }

    /** @test */
    public function alerts_can_be_added_by_authenticated_users_with_proper_permissions()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->state('staff')->create();

        $this->actingAs($staff, 'staff')->post(routeForTenant('alerts.store'), ['text' => 'foo', 'clientId' => '100'])
            ->assertStatus(302);

        $this->assertDatabaseHas('alerts', [
            'staff_id' => $staff->id,
            'client_id' => '100',
        ]);

        // The text of the alert is "foo", but it will be encrypted.
        $this->assertEquals('foo', decrypt(Alert::first()->getRawOriginal('text')));
    }

    /** @test */
    public function alert_text_is_a_required_field()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->state('staff')->create();

        $this->actingAs($staff)->post(routeForTenant('alerts.store'), ['text' => null, 'clientId' => '100'])
            ->assertStatus(302)
            ->assertSessionHasErrors('text');

        $this->assertCount(0, Alert::all());
    }

    /** @test */
    public function client_id_is_a_required_field()
    {
        $this->createTenant();
        $staff = factory(Staff::class)->state('staff')->create();

        $this->actingAs($staff)->post(routeForTenant('alerts.store'), ['text' => 'foo', 'clientId' => null])
            ->assertStatus(302)
            ->assertSessionHasErrors('clientId');

        $this->assertCount(0, Alert::all());
    }

    /** @test */
    public function alerts_cannot_be_deleted_by_guests()
    {
        $this->createTenant();

        $alert = factory(Alert::class)->create();

        $this->delete(routeForTenant('alerts.delete', [$alert->id]))
            ->assertRedirect(routeForTenant('login'));
    }

    /** @test */
    public function alerts_can_be_deleted_by_authenticated_users_with_proper_permissions()
    {
        $this->createTenant();

        $staff = factory(Staff::class)->state('staff')->create();
        $alert = factory(Alert::class)->create(['staff_id' => $staff->id]);

        $this->actingAs($staff)->delete(routeForTenant('alerts.delete', [$alert->id]))->assertStatus(302);

        $this->assertCount(0, Alert::all());
    }
}
