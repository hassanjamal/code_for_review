<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffTable extends Migration
{
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->string('id', 80);
            $table->primary('id');
            $table->string('api_id', '36')->index();
            $table->unsignedBigInteger('property_id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('api_role')->nullable();
            $table->string('api_access_token')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('properties');
        });
    }
}
