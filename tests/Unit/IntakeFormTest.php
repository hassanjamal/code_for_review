<?php

namespace Tests\Unit;

use App\Appointment;
use App\Client;
use App\FormSubmission;
use App\FormTemplate;
use App\IntakeForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class IntakeFormTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_code_is_generated_when_a_intake_form_model_is_created()
    {
        $this->createTenant();
        $intakeForm = factory(IntakeForm::class)->create();

        $this->assertNotNull($intakeForm->code);
    }

    /** @test */
    public function a_link_is_generated_for_a_intake_form_model()
    {
        $this->createTenant();
        $intakeForm = factory(IntakeForm::class)->create();

        $this->assertNotNull($intakeForm->link);
    }

    /** @test */
    public function it_belongs_to_a_client()
    {
        $this->createTenant();
        $client = factory(Client::class)->create();
        $intakeForm = factory(IntakeForm::class)->create(
            [
                'client_id' => $client->id,
            ]
        );

        $this->assertTrue($client->is($intakeForm->client));
    }

    /** @test */
    public function it_can_get_form_schema_by_ref_code()
    {
        $this->createTenant();
        $form = factory(FormTemplate::class)->create([
            'schema' => [
                'groups' => [
                    [
                        'legend' => 'Client Details',
                        'fields' => [
                            [
                                'type' => 'input',
                                'inputType' => 'text',
                                'label' => 'Name',
                                'model' => 'name',
                                'placeholder' => 'Full Name',
                                'required' => true,
                                'max' => 3,
                                'validator' => ["string", 'required'],
                            ],
                            [
                                'type' => "input",
                                'inputType' => "number",
                                'id' => "current_age",
                                'label' => "Age",
                                'model' => 'age',
                            ],
                        ],
                    ],
                    [
                        'legend' => 'Other Details',
                        'fields' => [
                            [
                                'type' => 'input',
                                'inputType' => 'text',
                                'label' => 'Color',
                                'model' => 'color',
                                'validator' => ['required', 'string'],
                            ],
                            [
                                'type' => "input",
                                'inputType' => "text",
                                'id' => "email",
                                'label' => "Email",
                                'model' => 'email',

                            ],
                        ],
                    ],
                ],

            ],
        ]);
        $intakeForm = factory(IntakeForm::class)->create(
            [
                'form_template_id' => $form->id,
            ]
        );

        $this->assertEquals([
            'groups' => [
                [
                    'legend' => 'Client Details',
                    'fields' => [
                        [
                            'type' => 'input',
                            'inputType' => 'text',
                            'label' => 'Name',
                            'model' => 'name',
                            'placeholder' => 'Full Name',
                            'required' => true,
                            'max' => 3,
                            'validator' => ["string", 'required'],
                        ],
                        [
                            'type' => "input",
                            'inputType' => "number",
                            'id' => "current_age",
                            'label' => "Age",
                            'model' => 'age',
                        ],
                    ],
                ],
                [
                    'legend' => 'Other Details',
                    'fields' => [
                        [
                            'type' => 'input',
                            'inputType' => 'text',
                            'label' => 'Color',
                            'model' => 'color',
                            'validator' => ['required', 'string'],
                        ],
                        [
                            'type' => "input",
                            'inputType' => "text",
                            'id' => "email",
                            'label' => "Email",
                            'model' => 'email',

                        ],
                    ],
                ],
            ],
        ], $intakeForm->formTemplate->schema);
    }

    /** @test */
    public function it_can_have_one_form_submission()
    {
        $this->createTenant();
        $intakeForm = factory(IntakeForm::class)->create();
        $formSubmission = factory(FormSubmission::class)->create([
            'intake_form_id' => $intakeForm->id,
        ]);

        $this->assertTrue($formSubmission->is($intakeForm->formSubmission));
    }

    /** @test */
    public function it_can_have_one_appointment()
    {
        $this->createTenant();

        $intakeForm = factory(IntakeForm::class)->create();
        $appointment = factory(Appointment::class)->create([]);

        $appointment->intakeForm()->save($intakeForm);

        $this->assertTrue($intakeForm->is($appointment->intakeForm));
    }

    /**
     * @test
     */
    public function it_returns_true_when_code_is_expired()
    {
        $this->createTenant();

        $intakeForm = factory(IntakeForm::class)->create([]);

        $knownTime = now()->addHours(5)->addMinutes(1);
        Carbon::setTestNow($knownTime);
        $intakeForm->refresh();

        $this->assertTrue($intakeForm->isExpired);
    }

    /** @test */
    public function is_expired_return_false_within_5_hours()
    {
        $this->createTenant();

        $intakeForm = factory(IntakeForm::class)->create([]);

        $knownTime = now()->addHours(5)->subMinutes(1);
        Carbon::setTestNow($knownTime);
        $intakeForm->refresh();

        $this->assertFalse($intakeForm->isExpired);
    }

    /** @test */
    public function it_can_query_db_using_scope_find_by_kiosk_code()
    {
        $this->createTenant();
        factory(IntakeForm::class)->state('withKioskCode')->create([
            'kiosk_code' => '123-456',
        ]);

        $intakeForm = IntakeForm::findByKioskCode('123-456')->first();
        $this->assertNotNull($intakeForm);
    }

    /** @test */
    public function it_can_check_if_kiosk_code_is_expired()
    {
        $this->createTenant();
        factory(IntakeForm::class)->state('withKioskCode')->create([
            'kiosk_code' => '123-456',
            'kiosk_code_expires_at' => now()->subMinute(),
        ]);

        $intakeForm = IntakeForm::findByKioskCode('123-456')->first();
        $this->assertTrue($intakeForm->isKioskCodeExpired);
    }

    /** @test */
    public function it_can_fetch_all_expired_forms()
    {
        $this->createTenant();

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

        $intakeForms = IntakeForm::expired()->get();

        $this->assertCount(3, $intakeForms);
    }
}
