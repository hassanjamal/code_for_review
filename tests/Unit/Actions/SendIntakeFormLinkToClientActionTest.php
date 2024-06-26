<?php

namespace Tests\Unit\Actions;

use App\Actions\SendIntakeFormLinkToClientAction;
use App\Appointment;
use App\Client;
use App\FormService;
use App\FormTemplate;
use App\Mail\IntakeFormForClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendIntakeFormLinkToClientActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_does_not_send_email_if_form_doesnt_exist()
    {
        Mail::fake();

        $this->createTenant();
        $client = factory(Client::class)->create(['api_public_id' => 'hassan_1']);
        $appointment = factory(Appointment::class)->create([
            'client_api_public_id' => $client->api_public_id,
        ]);

        app(SendIntakeFormLinkToClientAction::class)->execute($appointment);

        Mail::assertNotSent(IntakeFormForClient::class);
    }

    /** @test */
    public function it_does_not_send_email_if_client_doesnt_exist()
    {
        Mail::fake();

        $this->createTenant();
        $form = factory(FormTemplate::class)->create();
        $formService = factory(FormService::class)->create([
            'form_template_id' => $form->id,
            'service_id' => 5,
        ]);
        $appointment = factory(Appointment::class)->create([
            'client_api_public_id' => 'hassan_1',
        ]);

        app(SendIntakeFormLinkToClientAction::class)->execute($appointment);

        Mail::assertNotSent(IntakeFormForClient::class);
    }

    /** @test */
    public function it_does_not_send_email_if_service_id_does_not_exist()
    {
        Mail::fake();
        $this->createTenant();

        $client = factory(Client::class)->create(['api_public_id' => 'hassan_1']);
        $form = factory(FormTemplate::class)->create();
        $formService = factory(FormService::class)->create([
            'form_template_id' => $form->id,
            'service_id' => 5,
        ]);
        $appointment = factory(Appointment::class)->create([
            'client_api_public_id' => $client->api_public_id,
            'service_id' => 10,
        ]);

        app(SendIntakeFormLinkToClientAction::class)->execute($appointment);

        Mail::assertNotSent(IntakeFormForClient::class);
    }

    /** @test */
    public function it_sends_a_mail_to_client()
    {
        Mail::fake();
        $this->createTenant();

        $client = factory(Client::class)->create(['api_public_id' => 'hassan_1']);
        $form = factory(FormTemplate::class)->create();
        $formService = factory(FormService::class)->create([
            'form_template_id' => $form->id,
            'service_id' => 5,
        ]);
        $appointment = factory(Appointment::class)->create([
            'client_api_public_id' => $client->api_public_id,
            'service_id' => 5,
        ]);

        app(SendIntakeFormLinkToClientAction::class)->execute($appointment);

        Mail::assertSent(IntakeFormForClient::class, function ($mail) use ($client) {
            return $mail->hasTo($client->email);
        });
    }

    /** @test */
    public function it_will_not_send_duplicate_email()
    {
        Mail::fake();
        $this->createTenant();

        $client = factory(Client::class)->create(['api_public_id' => 'hassan_1']);
        $form = factory(FormTemplate::class)->create();
        $formService = factory(FormService::class)->create([
            'form_template_id' => $form->id,
            'service_id' => 5,
        ]);
        $appointment = factory(Appointment::class)->create([
            'client_api_public_id' => $client->api_public_id,
            'service_id' => 5,
        ]);

        app(SendIntakeFormLinkToClientAction::class)->execute($appointment);

        Mail::assertSent(IntakeFormForClient::class, function ($mail) use ($client) {
            return $mail->hasTo($client->email);
        });
        $appointment->refresh();

        app(SendIntakeFormLinkToClientAction::class)->execute($appointment);
        Mail::assertSent(IntakeFormForClient::class, 1);
    }
}
