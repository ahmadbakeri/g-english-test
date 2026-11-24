<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tests')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE `tests` MODIFY COLUMN `type` VARCHAR(32) NOT NULL DEFAULT 'daily'");
            } else {
                Schema::table('tests', function (Blueprint $table) {
                    $table->string('type', 32)->default('daily')->change();
                });
            }
        }
    }

    public function down(): void
    {
        // Keep widened VARCHAR safely
    }
};
