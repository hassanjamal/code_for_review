<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->string('id', 80);
            $table->primary('id');
            $table->string('api_id', 36);
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('location_id');
            $table->string('location_api_id', 36);
            $table->string('client_api_public_id')->nullable();
            $table->integer('staff_api_id');
            $table->string('staff_id', 80); // QN composite staff id.
            $table->integer('duration')->nullable(); // in minutes
            $table->string('status')->nullable();
            $table->datetime('start_date_time')->nullable();
            $table->datetime('end_date_time')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('staff_requested');
            $table->string('service_id', 36)->nullable();
            $table->string('service_name', 100);
            $table->boolean('first_appointment')->nullable();
            $table->string('room_name', 100)->nullable();
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('location_id')->references('id')->on('locations');
        });
    }
}
