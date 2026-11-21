<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cbt_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('cbt_attempts', 'external_submission_id')) {
                $table->string('external_submission_id', 191)->nullable()->unique();
            }
            if (!Schema::hasColumn('cbt_attempts', 'sync_status')) {
                $table->enum('sync_status', ['pending', 'synced', 'internal'])->default('internal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cbt_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('cbt_attempts', 'external_submission_id')) {
                $table->dropUnique(['external_submission_id']);
                $table->dropColumn('external_submission_id');
            }
            if (Schema::hasColumn('cbt_attempts', 'sync_status')) {
                $table->dropColumn('sync_status');
            }
        });
    }
};
