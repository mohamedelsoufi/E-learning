<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AvailableClasses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('available_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('class_type_id');
            $table->date('date');
            $table->time('from');
            $table->time('to');
            $table->integer('long')->comment('in minutes');
            $table->integer('max_student_number');
            $table->float('cost');
            $table->text('note');
            $table->timestamps();

            //relations
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('class_type_id')->references('id')->on('class_types')->onDelete('cascade');
        });

        Schema::create('student_class', function (Blueprint $table) {
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('available_class_id');

            //relations
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('available_class_id')->references('id')->on('available_classes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('available_classes');
        Schema::dropIfExists('student_class');
    }
}
