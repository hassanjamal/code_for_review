<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('api_provider');
            $table->string('api_identifier');
            $table->string('activation_code')->nullable();
            $table->string('activation_link')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['api_provider', 'api_identifier']);
        });
    }
}
