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
        Schema::table('admissions', function (Blueprint $table) {
            $table->string('birth_place')->nullable()->after('name');
            $table->enum('gender', ['L', 'P'])->nullable()->after('birth_date');
            $table->string('religion')->nullable()->after('gender');
            $table->string('social_media')->nullable()->after('phone');
            $table->string('education_level')->nullable()->after('level');
            $table->string('course_level')->nullable()->after('education_level');
            $table->string('class_name')->nullable()->after('course_level');
            $table->string('school_name')->nullable()->after('previous_school');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('birth_place')->nullable()->after('name');
            $table->string('religion')->nullable()->after('gender');
            $table->string('social_media')->nullable()->after('phone');
            $table->string('education_level')->nullable()->after('social_media');
            $table->string('course_level')->nullable()->after('education_level');
            $table->string('class_name')->nullable()->after('course_level');
            $table->string('school_name')->nullable()->after('class_name');
            
            // Note: student_email is covered by user->email.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admissions', function (Blueprint $table) {
            $table->dropColumn([
                'birth_place',
                'gender',
                'religion',
                'social_media',
                'education_level',
                'course_level',
                'class_name',
                'school_name'
            ]);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'birth_place',
                'religion',
                'social_media',
                'education_level',
                'course_level',
                'class_name',
                'school_name'
            ]);
        });
    }
};
