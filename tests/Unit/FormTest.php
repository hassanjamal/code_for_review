<?php

namespace Tests\Unit;

use App\FormService;
use App\FormTemplate;
use App\IntakeForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_many_intake_form()
    {
        $this->createTenant();

        $form = factory(FormTemplate::class)->create();

        $intakeForm_1 = factory(IntakeForm::class)->create(['form_template_id' => $form->id]);
        $intakeForm_2 = factory(IntakeForm::class)->create(['form_template_id' => $form->id]);


        $this->assertTrue($form->is($intakeForm_1->formTemplate));
        $this->assertTrue($form->is($intakeForm_2->formTemplate));
    }

    /** @test */
    public function it_has_many_form_services()
    {
        $this->createTenant();

        $form = factory(FormTemplate::class)->create();

        $formService_1 = factory(FormService::class)->create(['form_template_id' => $form->id]);
        $formService_2 = factory(FormService::class)->create(['form_template_id' => $form->id]);


        $this->assertTrue($form->is($formService_1->formTemplate));
        $this->assertTrue($form->is($formService_2->formTemplate));
    }
}
