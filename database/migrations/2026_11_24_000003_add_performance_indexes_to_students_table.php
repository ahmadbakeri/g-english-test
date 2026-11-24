<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $existing = collect(DB::select('SHOW INDEX FROM students'))
            ->pluck('Key_name')
            ->unique()
            ->all();

        Schema::table('students', function (Blueprint $table) use ($existing) {
            if (!in_array('idx_students_ext_hierarchy', $existing, true)) {
                $table->index(['student_type', 'deleted_at', 'education_level', 'school_name', 'class_name'], 'idx_students_ext_hierarchy');
            }
            if (!in_array('idx_students_ext_program', $existing, true)) {
                $table->index(['student_type', 'deleted_at', 'course_level'], 'idx_students_ext_program');
            }
            if (!in_array('idx_students_name', $existing, true)) {
                $table->index('name', 'idx_students_name');
            }
            if (!in_array('idx_students_nisn', $existing, true)) {
                $table->index('nisn', 'idx_students_nisn');
            }
            if (!in_array('idx_students_comp_code', $existing, true)) {
                $table->index('competition_code', 'idx_students_comp_code');
            }
        });
    }

    public function down(): void
    {
        $existing = collect(DB::select('SHOW INDEX FROM students'))
            ->pluck('Key_name')
            ->unique()
            ->all();

        Schema::table('students', function (Blueprint $table) use ($existing) {
            foreach ([
                'idx_students_ext_hierarchy',
                'idx_students_ext_program',
                'idx_students_name',
                'idx_students_nisn',
                'idx_students_comp_code',
            ] as $indexName) {
                if (in_array($indexName, $existing, true)) {
                    $table->dropIndex($indexName);
                }
            }
        });
    }
};
