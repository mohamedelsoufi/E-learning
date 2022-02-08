<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ClassTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('level_id');
            $table->integer('students_number');
            $table->integer('company_percentage');
            $table->integer('long')->comment('in minutes');
            $table->float('cost');
            $table->timestamps();

            //relations
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade');
        });

        Schema::create('students_number', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_type_id');
            $table->integer('min_students_number');
            $table->integer('max_students_number');
            $table->tinyInteger('status')->default(1)->comment('1->active, 0-> un active');
            $table->timestamps();

            //relations
            $table->foreign('class_type_id')->references('id')->on('class_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_types');
        Schema::dropIfExists('students_number');
    }
}
