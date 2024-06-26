<?php

namespace Tests\Unit\Actions;

use App\Client;
use App\Events\FormSubmitted;
use App\FormTemplate;
use App\IntakeForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class FormSubmittedActionTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function it_logs_form_submitted_activity()
    {
        $this->createTenant();
        activity()->enableLogging();


        $client = factory(Client::class)->create();
        $form = factory(FormTemplate::class)->create();
        $intakeForm = factory(IntakeForm::class)->create([
            'form_template_id' => $form->id,
            'client_id' => $client->id,
        ]);

        event(new FormSubmitted($intakeForm));

        $activity = Activity::all()->last();

        $this->assertEquals('forms-log', $activity->log_name);
        $this->assertEquals($intakeForm->getmorphClass(), $activity->subject_type);
        $this->assertEquals($intakeForm->id, $activity->subject_id);
        $this->assertEquals('Form Submitted', $activity->description);
    }
}
