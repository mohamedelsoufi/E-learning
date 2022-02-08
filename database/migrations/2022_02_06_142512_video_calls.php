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
            $table->string('agora_token');
            $table->tinyInteger('status')->default(1)->comment('1->active, 0-> un active');
            $table->timestamps();

            //relations
            $table->foreign('available_classes_id')->references('id')->on('available_classes')->onDelete('cascade');
        });

        Schema::create('student_call', function (Blueprint $table) {
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('video_call_id');

            //relations
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('video_call_id')->references('id')->on('video_calls')->onDelete('cascade');
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
        Schema::dropIfExists('student_call');
    }
}
