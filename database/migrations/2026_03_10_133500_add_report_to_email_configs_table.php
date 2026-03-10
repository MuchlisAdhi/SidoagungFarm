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
        if (! Schema::hasTable('email_configs') || Schema::hasColumn('email_configs', 'report')) {
            return;
        }

        Schema::table('email_configs', function (Blueprint $table) {
            $table->string('report')->nullable()->after('from_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('email_configs') || ! Schema::hasColumn('email_configs', 'report')) {
            return;
        }

        Schema::table('email_configs', function (Blueprint $table) {
            $table->dropColumn('report');
        });
    }
};
