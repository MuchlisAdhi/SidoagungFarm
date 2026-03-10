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
            if (! Schema::hasColumn('clientquestion', 'ticket_status')) {
                $table->string('ticket_status', 20)->default('new')->after('replied');
            }
        });

        Schema::table('clientquestion2', function (Blueprint $table) {
            if (! Schema::hasColumn('clientquestion2', 'ticket_status')) {
                $table->string('ticket_status', 20)->default('new')->after('replied');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientquestion', function (Blueprint $table) {
            if (Schema::hasColumn('clientquestion', 'ticket_status')) {
                $table->dropColumn('ticket_status');
            }
        });

        Schema::table('clientquestion2', function (Blueprint $table) {
            if (Schema::hasColumn('clientquestion2', 'ticket_status')) {
                $table->dropColumn('ticket_status');
            }
        });
    }
};
