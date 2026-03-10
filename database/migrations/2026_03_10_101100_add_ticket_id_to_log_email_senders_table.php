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
        Schema::table('log_email_senders', function (Blueprint $table) {
            if (! Schema::hasColumn('log_email_senders', 'ticket_id')) {
                $table->uuid('ticket_id')->nullable()->after('question_id');
                $table->index('ticket_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_email_senders', function (Blueprint $table) {
            if (Schema::hasColumn('log_email_senders', 'ticket_id')) {
                $table->dropIndex(['ticket_id']);
                $table->dropColumn('ticket_id');
            }
        });
    }
};
