<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCareer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('career', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('position')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->text('qualification')->nullable();
            $table->boolean('publish')->default(0);
            $table->string('postedon')->nullable();
            $table->string('closingdate')->nullable();
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
        Schema::dropIfExists('career');
    }
}
