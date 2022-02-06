<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Teachers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('dialing_code')->nullable();
            $table->string('phone')->unique();
            $table->string('password');
            $table->boolean('verified')->comment('0 ->not verified, 1 -> verified')->default(0);
            $table->boolean('status')->comment('1->active, 0->bloked')->default(0);
            $table->tinyInteger('gender')->comment('1->online, 0 ->not')->nullable();
            $table->date('birth')->nullable();
            $table->unsignedBigInteger('country_id');
            $table->float('balance')->default(0);
            $table->boolean('online')->comment('1->online, 0 ->not')->default(0);
            $table->timestamps();

            //relations
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
        });

        Schema::create('teacher_verified', function (Blueprint $table) {
            $table->string('username')->index();
            $table->integer('code');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('teacher_password_resets', function (Blueprint $table) {
            $table->string('username')->index();
            $table->integer('code');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('subject_teacher', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('teacher_id');

            //relations
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('teacher_verified');
        Schema::dropIfExists('teacher_password_resets');
        Schema::dropIfExists('subject_teacher');
    }
}
