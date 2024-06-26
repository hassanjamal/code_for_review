<?php

namespace Tests\Feature;

use App\IntakeForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class KioskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_kiosk_route_is_accessible_to_guests()
    {
        $this->createTenant();

        $this->get(routeForTenant('kiosk_form.show'))
             ->assertOk()
             ->assertComponentIs('Forms/Kiosk/Create');
    }

    /**
     * @test
     * @dataProvider KioskInputValidation
     * @param $formInput
     * @param $formInputValue
     */
    public function test_form_validation($formInput, $formInputValue)
    {
        $this->createTenant();

        $response = $this->json('POST', routeForTenant('kiosk_form.store'), [
            $formInput => $formInputValue,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($formInput);
    }

    public function KioskInputValidation()
    {
        return [
            'Kiosk Code is required' => ['kioskCode', ''],
            'Kiosk Code is string' => ['kioskCode', ['123-199', '123-001']],
        ];
    }

    /**
     * @test
     */
    public function it_throws_an_error_when_code_is_expired()
    {
        $this->createTenant();

        factory(IntakeForm::class)->state('withKioskCode')->create([
            'kiosk_code' => '123-456',
            'kiosk_code_expires_at' => now()->subMinute(),
        ]);

        $response = $this->json('POST', routeForTenant('kiosk_form.store'), [
            'kioskCode' => '123-456',
        ]);

        $response->assertStatus(302);
    }

    /**
     * @test
     */
    public function it_redirects_to_intake_form_create_route()
    {
        $this->createTenant();

        $intakeForm = factory(IntakeForm::class)->state('withKioskCode')->create([
            'kiosk_code' => '123-456',
        ]);

        $this->json('POST', routeForTenant('kiosk_form.store'), [
            'kioskCode' => '123-456',
        ])->assertOk()->assertComponentIs('Forms/'.$intakeForm->formTemplate->component);
    }

    /**
     * @test
     */
    public function it_expires_the_kiosk_code_after_10_second_once_submitted_successfully()
    {
        $this->createTenant();

        $intakeForm = factory(IntakeForm::class)->state('withKioskCode')->create([
            'kiosk_code' => '123-456',
        ]);

        $response = $this->json('POST', routeForTenant('kiosk_form.store'), [
            'kioskCode' => '123-456',
        ]);

        $knownTime = now()->addSeconds(10);
        Carbon::setTestNow($knownTime);
        $intakeForm->refresh();

        $this->assertTrue($intakeForm->isKioskCodeExpired);
    }
}
