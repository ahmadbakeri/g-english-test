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
        Schema::table('cbt_questions', function (Blueprint $table) {
            $table->index('cbt_exam_id');
        });

        Schema::table('cbt_attempts', function (Blueprint $table) {
            $table->index(['student_id', 'cbt_exam_id']);
            $table->index(['is_completed', 'cbt_exam_id']);
        });

        Schema::table('cbt_answers', function (Blueprint $table) {
            $table->index('cbt_attempt_id');
            // Jika data sdh ada, unique mungkin gagal kalau ada duplikat, tapi krn table baru/masih kosong tidak apa-apa
            // $table->unique(['cbt_attempt_id', 'cbt_question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cbt_questions', function (Blueprint $table) {
            $table->dropIndex(['cbt_exam_id']);
        });

        Schema::table('cbt_attempts', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'cbt_exam_id']);
            $table->dropIndex(['is_completed', 'cbt_exam_id']);
        });

        Schema::table('cbt_answers', function (Blueprint $table) {
            $table->dropIndex(['cbt_attempt_id']);
        });
    }
};
