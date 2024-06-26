<?php

namespace App\Mail;

use App\Client;
use App\IntakeForm;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IntakeFormForClient extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Client
     */
    public $client;

    /**
     * @var IntakeForm
     */
    public $intakeForm;

    /**
     * Create a new message instance.
     * @param Client     $client
     * @param IntakeForm $intakeForm
     */
    public function __construct(Client $client, IntakeForm $intakeForm)
    {
        $this->client = $client;
        $this->intakeForm = $intakeForm;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@qn2020.test') // TODO sender email address needs to be updated
                    ->markdown('emails.clients.intakeForm');
    }
}
