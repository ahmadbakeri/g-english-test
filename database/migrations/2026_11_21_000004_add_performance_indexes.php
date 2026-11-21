<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasIndex('attendances', 'attendances_date_role_student_idx') && !Schema::hasIndex('attendances', ['date', 'role_type', 'student_id'])) {
                $table->index(['date', 'role_type', 'student_id'], 'attendances_date_role_student_idx');
            }
            if (!Schema::hasIndex('attendances', 'attendances_date_role_tutor_idx') && !Schema::hasIndex('attendances', ['date', 'role_type', 'tutor_id'])) {
                $table->index(['date', 'role_type', 'tutor_id'], 'attendances_date_role_tutor_idx');
            }
        });

        Schema::table('cbt_attempts', function (Blueprint $table) {
            if (!Schema::hasIndex('cbt_attempts', 'cbt_attempts_exam_student_idx') && !Schema::hasIndex('cbt_attempts', ['cbt_exam_id', 'student_id'])) {
                $table->index(['cbt_exam_id', 'student_id'], 'cbt_attempts_exam_student_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasIndex('attendances', 'attendances_date_role_student_idx')) {
                $table->dropIndex('attendances_date_role_student_idx');
            }
            if (Schema::hasIndex('attendances', 'attendances_date_role_tutor_idx')) {
                $table->dropIndex('attendances_date_role_tutor_idx');
            }
        });

        Schema::table('cbt_attempts', function (Blueprint $table) {
            if (Schema::hasIndex('cbt_attempts', 'cbt_attempts_exam_student_idx')) {
                $table->dropIndex('cbt_attempts_exam_student_idx');
            }
        });
    }
};
