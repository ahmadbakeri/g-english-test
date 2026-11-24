<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('test_sessions')) {
            Schema::create('test_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->foreignId('cbt_exam_id')->nullable()->constrained('cbt_exams')->nullOnDelete();
                $table->foreignId('test_id')->nullable()->constrained('tests')->nullOnDelete();
                $table->date('session_date');
                $table->time('start_time');
                $table->time('end_time');
                $table->string('server_name')->default('Server Utama');
                $table->unsignedInteger('max_capacity')->default(35);
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('test_schedule_requests')) {
            Schema::create('test_schedule_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('test_session_id')->constrained('test_sessions')->cascadeOnDelete();
                $table->foreignId('requester_user_id')->constrained('users')->cascadeOnDelete();
                $table->string('requester_role', 32)->default('siswa');
                $table->string('school_name')->nullable();
                $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
                $table->unsignedInteger('requested_seats')->default(1);
                $table->string('status', 24)->default('pending'); // pending, approved, rejected
                $table->text('notes')->nullable();
                $table->text('review_notes')->nullable();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('test_schedule_requests');
        Schema::dropIfExists('test_sessions');
    }
};
