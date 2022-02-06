<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Curriculums extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('curriculums', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('locale')->nullable();
            $table->bigInteger('parent');
            $table->unsignedBigInteger('country_id');
            $table->timestamps();

            //relations
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
        });

        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('locale')->nullable();
            $table->bigInteger('parent');
            $table->unsignedBigInteger('curriculum_id');
            $table->timestamps();

            //relations
            $table->foreign('curriculum_id')->references('id')->on('curriculums')->onDelete('cascade');
        });

        Schema::create('years', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('locale')->nullable();
            $table->bigInteger('parent');
            $table->unsignedBigInteger('level_id');
            $table->timestamps();

            //relations
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade');
        });

        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('locale')->nullable();
            $table->bigInteger('parent');
            $table->unsignedBigInteger('year_id');
            $table->timestamps();

            //relations
            $table->foreign('year_id')->references('id')->on('years')->onDelete('cascade');
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('locale')->nullable();
            $table->bigInteger('parent');
            $table->unsignedBigInteger('term_id');
            $table->timestamps();

            //relations
            $table->foreign('term_id')->references('id')->on('terms')->onDelete('cascade');
        });

        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('subject_id');
            $table->timestamps();

            //relations
            $table->foreign('subject_id')->references('id')->on('materials')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('curriculums');
        Schema::dropIfExists('levels');
        Schema::dropIfExists('years');
        Schema::dropIfExists('terms');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('materials');
    }
}
