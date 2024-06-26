<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManagedImagesTable extends Migration
{
    public function up()
    {
        Schema::create('managed_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key');
            $table->string('original_name');
            $table->string('content_type');
            $table->timestamps();
        });
    }
}
