<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgressNoteImagesTable extends Migration
{
    public function up()
    {
        Schema::create('progress_note_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('progress_note_id');
            $table->string('key');
            $table->string('bucket');
            $table->string('content_type');
            $table->timestamps();

            $table->foreign('progress_note_id')->references('id')->on('progress_notes');
        });
    }
}
