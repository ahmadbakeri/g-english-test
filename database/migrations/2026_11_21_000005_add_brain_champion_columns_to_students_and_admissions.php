<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('admissions')) {
            Schema::table('admissions', function (Blueprint $table) {
                if (!Schema::hasColumn('admissions', 'school_user_id')) {
                    $table->foreignId('school_user_id')->nullable()->after('student_type')->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('admissions', 'nisn')) {
                    $table->string('nisn', 20)->nullable()->after('registration_number');
                }
                if (!Schema::hasColumn('admissions', 'competition_code')) {
                    $table->string('competition_code', 30)->nullable()->after('nisn');
                }
            });
        }

        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (!Schema::hasColumn('students', 'school_user_id')) {
                    $table->foreignId('school_user_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('students', 'nisn')) {
                    $table->string('nisn', 20)->nullable()->after('nis');
                }
                if (!Schema::hasColumn('students', 'competition_code')) {
                    $table->string('competition_code', 30)->nullable()->after('nisn');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('admissions')) {
            Schema::table('admissions', function (Blueprint $table) {
                if (Schema::hasColumn('admissions', 'school_user_id')) {
                    $table->dropConstrainedForeignId('school_user_id');
                }
                if (Schema::hasColumn('admissions', 'nisn')) {
                    $table->dropColumn('nisn');
                }
                if (Schema::hasColumn('admissions', 'competition_code')) {
                    $table->dropColumn('competition_code');
                }
            });
        }

        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (Schema::hasColumn('students', 'school_user_id')) {
                    $table->dropConstrainedForeignId('school_user_id');
                }
                if (Schema::hasColumn('students', 'nisn')) {
                    $table->dropColumn('nisn');
                }
                if (Schema::hasColumn('students', 'competition_code')) {
                    $table->dropColumn('competition_code');
                }
            });
        }
    }
};
