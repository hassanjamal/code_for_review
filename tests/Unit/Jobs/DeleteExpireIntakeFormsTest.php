<?php

namespace Tests\Unit\Jobs;

use App\IntakeForm;
use App\Jobs\DeleteExpiredIntakeForms;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class DeleteExpireIntakeFormsTest extends TestCase
{
    /** @test */
    public function it_deletes_all_expired_intake_forms()
    {
        $tenant = $this->createTenant();

        $form1 = factory(IntakeForm::class)->state('withKioskCode')->create([
            'kiosk_code' => '123-456',
            'kiosk_code_expires_at' => now()->subMinute(),
        ]);

        $form2 = factory(IntakeForm::class)->create();
        $form2->code_expires_at = now()->subMinute();
        $form2->save();

        $form3 = factory(IntakeForm::class)->state('submitted')->create([
            'submitted_at' => now()->subMinute(),
        ]);

        $form4 = factory(IntakeForm::class)->create();

        tenancy()->end();

        Bus::dispatch(new DeleteExpiredIntakeForms());

        tenancy()->initialize($tenant);

        $expiredIntakeForms = IntakeForm::expired()->get();
        $this->assertCount(0, $expiredIntakeForms);

        $validIntakeForms = IntakeForm::get();
        $this->assertCount(1, $validIntakeForms);
        $this->assertEquals($validIntakeForms->first()->id, $form4->id);
    }
}
