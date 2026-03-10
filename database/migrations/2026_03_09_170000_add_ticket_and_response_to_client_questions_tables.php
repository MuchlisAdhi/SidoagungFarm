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
        Schema::table('clientquestion', function (Blueprint $table) {
            if (! Schema::hasColumn('clientquestion', 'ticket_no')) {
                $table->string('ticket_no')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('clientquestion', 'response_message')) {
                $table->text('response_message')->nullable()->after('description');
            }
            if (! Schema::hasColumn('clientquestion', 'responded_at')) {
                $table->timestamp('responded_at')->nullable()->after('replied');
            }
        });

        Schema::table('clientquestion2', function (Blueprint $table) {
            if (! Schema::hasColumn('clientquestion2', 'ticket_no')) {
                $table->string('ticket_no')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('clientquestion2', 'response_message')) {
                $table->text('response_message')->nullable()->after('description');
            }
            if (! Schema::hasColumn('clientquestion2', 'responded_at')) {
                $table->timestamp('responded_at')->nullable()->after('replied');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientquestion', function (Blueprint $table) {
            if (Schema::hasColumn('clientquestion', 'responded_at')) {
                $table->dropColumn('responded_at');
            }
            if (Schema::hasColumn('clientquestion', 'response_message')) {
                $table->dropColumn('response_message');
            }
            if (Schema::hasColumn('clientquestion', 'ticket_no')) {
                $table->dropColumn('ticket_no');
            }
        });

        Schema::table('clientquestion2', function (Blueprint $table) {
            if (Schema::hasColumn('clientquestion2', 'responded_at')) {
                $table->dropColumn('responded_at');
            }
            if (Schema::hasColumn('clientquestion2', 'response_message')) {
                $table->dropColumn('response_message');
            }
            if (Schema::hasColumn('clientquestion2', 'ticket_no')) {
                $table->dropColumn('ticket_no');
            }
        });
    }
};
