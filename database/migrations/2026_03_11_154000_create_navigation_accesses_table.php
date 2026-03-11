<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navigation_accesses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id')->unique();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->foreign('role_id')
                ->references('id')
                ->on(config('permission.table_names.roles', 'roles'))
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navigation_accesses');
    }
};

