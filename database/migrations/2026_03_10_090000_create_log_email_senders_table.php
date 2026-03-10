<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('log_email_senders')) {
            return;
        }

        Schema::create('log_email_senders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('question_mode', 10)->nullable();
            $table->uuid('question_id')->nullable();
            $table->string('ticket_no')->nullable();
            $table->string('recipient_email');
            $table->string('subject');
            $table->string('template');
            $table->text('body')->nullable();
            $table->string('status')->default('queued');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_email_senders');
    }
};
