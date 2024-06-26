<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertsTable extends Migration
{
    public function up()
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('staff_id', 80);
            $table->string('client_id', 80);
            $table->text('text');

            $table->timestamps();
        });
    }
}
