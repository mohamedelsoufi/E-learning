<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Questions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->text('question');
            $table->timestamps();

            //relations
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
        });

        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('answerable_id')->nullable()->comment('student or teacher');
            $table->string('answerable_type')->nullable();
            $table->unsignedBigInteger('question_id');
            $table->text('text')->nullable();
            $table->boolean('recommendation')->default(0)->comment('1-> rocommend');
            $table->timestamps();

            //relations
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
        Schema::dropIfExists('answers');
    }
}
