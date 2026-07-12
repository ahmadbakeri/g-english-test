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
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropForeign(['class_id']);
            $table->dropUnique(['class_id', 'student_id', 'date']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->unsignedBigInteger('class_id')->nullable()->change();
            $table->unsignedBigInteger('student_id')->nullable()->change();
            
            $table->foreign('class_id')->references('id')->on('classes')->nullOnDelete();
            $table->foreign('student_id')->references('id')->on('students')->nullOnDelete();

            $table->string('role_type')->default('student')->after('id');
            $table->foreignId('tutor_id')->nullable()->after('student_id')->constrained('tutors')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->after('tutor_id')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['tutor_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['role_type', 'tutor_id', 'user_id']);
        });
    }
};
