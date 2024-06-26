<?php

namespace App\Jobs;

use App\IntakeForm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteExpiredIntakeForms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        foreach (tenancy()->all() as $tenant) {
            $tenant->run(function () {
                IntakeForm::expired()->delete();
            });
        }
    }
}
