<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (Schema::hasTable('clientquestion') && Schema::hasColumn('clientquestion', 'description')) {
            $this->alterColumnToText($driver, 'clientquestion');
        }

        if (Schema::hasTable('clientquestion2') && Schema::hasColumn('clientquestion2', 'description')) {
            $this->alterColumnToText($driver, 'clientquestion2');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if (Schema::hasTable('clientquestion') && Schema::hasColumn('clientquestion', 'description')) {
            $this->alterColumnToVarchar($driver, 'clientquestion');
        }

        if (Schema::hasTable('clientquestion2') && Schema::hasColumn('clientquestion2', 'description')) {
            $this->alterColumnToVarchar($driver, 'clientquestion2');
        }
    }

    protected function alterColumnToText(string $driver, string $table): void
    {
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement(sprintf('ALTER TABLE `%s` MODIFY `description` TEXT NULL', $table));

            return;
        }

        Schema::table($table, function (Blueprint $blueprint): void {
            $blueprint->text('description')->nullable()->change();
        });
    }

    protected function alterColumnToVarchar(string $driver, string $table): void
    {
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement(sprintf('ALTER TABLE `%s` MODIFY `description` VARCHAR(255) NULL', $table));

            return;
        }

        Schema::table($table, function (Blueprint $blueprint): void {
            $blueprint->string('description', 255)->nullable()->change();
        });
    }
};

