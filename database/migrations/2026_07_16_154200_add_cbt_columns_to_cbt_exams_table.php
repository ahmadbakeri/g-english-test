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
        Schema::table('cbt_exams', function (Blueprint $table) {
            $table->boolean('random_questions')->default(true)->after('end_time');
            $table->integer('max_concurrent_users')->nullable()->after('random_questions');
            $table->boolean('shuffle_questions')->default(true)->after('max_concurrent_users');
            $table->boolean('shuffle_options')->default(false)->after('shuffle_questions');
            $table->boolean('show_result_immediately')->default(true)->after('shuffle_options');
            $table->boolean('allow_review')->default(false)->after('show_result_immediately');
            $table->integer('auto_save_interval')->default(30)->after('allow_review')->comment('Dalam detik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cbt_exams', function (Blueprint $table) {
            $table->dropColumn([
                'random_questions',
                'max_concurrent_users',
                'shuffle_questions',
                'shuffle_options',
                'show_result_immediately',
                'allow_review',
                'auto_save_interval'
            ]);
        });
    }
};
