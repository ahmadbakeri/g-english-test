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
        Schema::table('cbt_attempts', function (Blueprint $table) {
            $table->boolean('is_completed')->default(false)->after('score');
            $table->integer('violation_count')->default(0)->after('is_completed');
            $table->integer('max_violations')->default(3)->after('violation_count');
            $table->json('question_order')->nullable()->after('max_violations');
            $table->boolean('is_force_submitted')->default(false)->after('question_order');
            $table->timestamp('last_auto_save_at')->nullable()->after('is_force_submitted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cbt_attempts', function (Blueprint $table) {
            $table->dropColumn([
                'is_completed',
                'violation_count',
                'max_violations',
                'question_order',
                'is_force_submitted',
                'last_auto_save_at'
            ]);
        });
    }
};
