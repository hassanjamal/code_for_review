<?php

namespace Tests\Unit;

use App\FormSubmission;
use App\IntakeForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormSubmissionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_intake_form()
    {
        $this->createTenant();

        $intakeForm = factory(IntakeForm::class)->create();
        $formSubmission = factory(FormSubmission::class)->create([
            'intake_form_id' => $intakeForm->id,
        ]);

        $this->assertTrue($intakeForm->is($formSubmission->intakeForm));
    }

    /** @test */
    public function it_encrypts_the_form_model_before_storing_in_db()
    {
        $this->createTenant();

        $intakeForm = factory(IntakeForm::class)->create();
        $formSubmission = factory(FormSubmission::class)->create([
            'intake_form_id' => $intakeForm->id,
            'form_model' => [
                'key1' =>'Some Random Content',
                'key2' => 'Another Random Content',
            ],
        ]);

        $this->assertEquals([
            'key1' =>'Some Random Content',
            'key2' => 'Another Random Content',
        ], json_decode(decrypt($formSubmission->getRawOriginal('form_model')), true));
    }
}
