<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Students extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id')->unique();
            $table->string('username');
            $table->string('email')->nullable()->unique();
            $table->string('dialing_code')->nullable();
            $table->string('phone')->unique();
            $table->string('password');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('curriculum_id')->nullable();
            $table->unsignedBigInteger('year_id')->nullable();
            $table->float('balance')->default(0);
            $table->boolean('verified')->comment('0 ->not verified, 1 -> verified')->default(0);
            $table->boolean('status')->comment('1->active, 0->bloked')->default(1);
            $table->boolean('online')->comment('1->online, 0 ->not')->default(0);
            $table->tinyInteger('gender')->comment('1->male, 0 ->female')->default(0);
            $table->date('birth')->nullable();
            $table->string('token_firebase')->nullable();
            $table->timestamps();

            //relations
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('year_id')->references('id')->on('years')->onDelete('cascade');
            $table->foreign('curriculum_id')->references('id')->on('curriculums')->onDelete('cascade');
        });

        Schema::create('student_verified', function (Blueprint $table) {
            $table->string('username')->index();
            $table->integer('code');
            $table->timestamp('created_at')->nullable();
        });
        
        Schema::create('student_password_resets', function (Blueprint $table) {
            $table->string('username')->index();
            $table->integer('code');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
        Schema::dropIfExists('student_verified');
        Schema::dropIfExists('student_password_resets');
    }
}
