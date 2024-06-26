<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('profileable_id', 80);
            $table->string('profileable_type', 50);
            $table->text('signature_post_script')->nullable();
            $table->timestamps();
        });
    }
}
