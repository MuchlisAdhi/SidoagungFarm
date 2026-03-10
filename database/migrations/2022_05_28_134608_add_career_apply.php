<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCareerApply extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('careerapply', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('careerid')->nullable();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('bod')->nullable();
            $table->string('lasteducation')->nullable();
            $table->string('major')->nullable();
            $table->boolean('isexperience')->default(0);
            $table->string('currentsalary')->nullable();
            $table->string('expectationsalary')->nullable();
            $table->boolean('isapprove')->default(0);
            $table->string('cvid')->nullable();
            $table->text('experiencelist')->nullable();
            $table->text('rejectreason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('careerapply');
    }
}
