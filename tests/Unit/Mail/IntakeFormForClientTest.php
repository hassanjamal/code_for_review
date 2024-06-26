<?php

namespace Tests\Unit;

use App\Client;
use App\IntakeForm;
use App\Mail\IntakeFormForClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class IntakeFormForClientTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_mail_is_sent_to_client_with_intake_form_link()
    {
        Mail::fake();

        $this->createTenant();
        $client = factory(Client::class)->create();
        $intakeForm = factory(IntakeForm::class)->create(
            [
                'client_id' => $client->id,
            ]
        );

        Mail::to($client->email)->send(new IntakeFormForClient($client, $intakeForm));

        Mail::assertSent(IntakeFormForClient::class, function ($mail) use ($client, $intakeForm) {
            return $mail->hasTo($client->email);
        });
    }
}
