<?php

namespace App\Http\Middleware\Cbt;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\CbtExam;
use Illuminate\Support\Facades\Auth;

class ExamAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $exam = $request->route('exam');
        
        // If route parameter is an attempt, we extract exam from attempt
        if (!$exam && $request->route('attempt')) {
            $attempt = $request->route('attempt');
            $exam = $attempt->exam;
        }

        if (!$exam) {
            abort(404, 'Ujian tidak ditemukan.');
        }

        $user = Auth::user();
        if ($user->role !== 'student' && $user->role !== 'siswa') {
            abort(403, 'Akses ditolak. Hanya siswa yang dapat mengikuti ujian.');
        }

        $student = $user->student;
        if (!$student) {
            abort(403, 'Data siswa tidak ditemukan.');
        }

        // Check if student belongs to the exam's class
        if (!$student->classes()->where('classes.id', $exam->class_id)->exists()) {
            abort(403, 'Ujian ini bukan untuk kelas Anda.');
        }

        // Check if exam is active
        if (!$exam->is_active) {
            abort(403, 'Ujian ini sedang tidak aktif.');
        }

        // Check time constraints
        $now = now();
        if ($exam->start_time && $now->lessThan($exam->start_time)) {
            abort(403, 'Ujian ini belum dimulai.');
        }
        if ($exam->end_time && $now->greaterThan($exam->end_time)) {
            abort(403, 'Ujian ini sudah berakhir.');
        }

        // Check existing attempt for completion or limits
        $existingAttempt = $student->cbtAttempts()->where('cbt_exam_id', $exam->id)->first();
        if ($existingAttempt) {
            if ($existingAttempt->is_completed) {
                // If it's completed, only allow access to the result route
                if ($request->route()->getName() !== 'exam.result') {
                    abort(403, 'Anda telah menyelesaikan ujian ini.');
                }
            } else {
                // If it's not completed, check attempt limit on the start route
                // The start route doesn't have the 'attempt' parameter
                if (!$request->route('attempt') && $existingAttempt->attempt_number >= $exam->max_attempts) {
                    abort(403, 'Batas percobaan (resume) habis. Silakan hubungi admin/guru untuk membuka kunci ujian.');
                }
            }
        }

        // Check max concurrent users
        if ($exam->max_concurrent_users) {
            $activeUsers = \App\Models\CbtAttempt::where('cbt_exam_id', $exam->id)
                ->where('is_completed', false)
                ->where('status', 'in_progress')
                ->count();

            // Only block if this is a new attempt request (not already in progress)
            if (!$request->route('attempt') && $activeUsers >= $exam->max_concurrent_users) {
                abort(403, 'Server sedang penuh. Silakan coba beberapa saat lagi.');
            }
        }

        return $next($request);
    }
}
