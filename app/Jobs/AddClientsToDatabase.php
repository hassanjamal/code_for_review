<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddClientsToDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $clients;

    private $property;

    public function __construct($clients, $property)
    {
        $this->clients = $clients;
        $this->property = $property;
    }

    public function handle()
    {
        $this->clients->each(function ($client) {
            $this->property
                ->clients()
                ->updateOrCreate(
                    ['id' => $client->id],
                    [
                        'property_id' => $client->property_id,
                        'api_id' => $client->api_id,
                        'api_public_id' => $client->api_public_id,
                        'first_name' => $client->first_name,
                        'last_name' => $client->last_name,
                        'gender' => $client->gender,
                        'email' => $client->email,
                        'birth_date' => $client->birth_date,
                        'referred_by' => $client->referred_by,
                        'first_appointment_date' => $client->first_appointment_date,
                        'photo_url' => $client->photo_url,
                        'status' => $client->status,
                        'membership_id' => $client->membership_id,
                        'membership_name' => $client->membership_name,
                    ]
                );
        });
    }
}
