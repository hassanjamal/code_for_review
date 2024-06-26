<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->string('id', 80);
            $table->primary('id');
            $table->string('api_id', 36);
            $table->unsignedBigInteger('property_id');
            $table->string('api_public_id', 36);
            $table->text('first_name')->nullable();
            $table->text('last_name')->nullable();
            $table->text('middle_name')->nullable();
            $table->text('gender')->nullable();
            $table->text('email')->nullable();
            $table->dateTime('birth_date')->nullable();
            $table->text('referred_by')->nullable();
            //$table->string('membership_name')->nullable();
            //$table->string('membership_status')->nullable();
            $table->dateTime('first_appointment_date')->nullable();
            $table->text('photo_url')->nullable();
            $table->string('status')->nullable();
            $table->string('membership_id', 32)->nullable();
            $table->string('membership_name', 32)->nullable();
            //$table->string('insurance_company_name')->nullable();
            //$table->string('insurance_policy_number')->nullable();
            $table->datetime('merged_at')->nullable();
            $table->string('merged_by', 80)->nullable();
            $table->string('merged_to', 80)->nullable();

            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('properties');
        });
    }
}
