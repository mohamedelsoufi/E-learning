<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VideoCalls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_calls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('available_classes_id');
            $table->date('date');
            $table->tinyInteger('status')->comment('');
            $table->string('agora_token');
            $table->timestamps();

            //relations
            $table->foreign('available_classes_id')->references('id')->on('available_classes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_calls');
    }
}
