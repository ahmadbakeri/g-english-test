<?php

namespace App\Http\Middleware\Cbt;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\ExamService;

class ExamTimerMiddleware
{
    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $attempt = $request->route('attempt');

        if (!$attempt) {
            return $next($request);
        }

        // Check if student owns this attempt
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user || !$user->student || $attempt->student_id !== $user->student->id) {
            abort(403, 'Akses ditolak.');
        }

        if ($attempt->is_completed) {
            if ($request->route()->getName() !== 'exam.result') {
                return redirect()->route('exam.result', ['attempt' => $attempt->id]);
            }
            return $next($request);
        }

        if ($attempt->is_force_submitted) {
            // Block direct access if they were kicked. They must go through 'start' again (if they have attempts left)
            return redirect()->route('student.cbt.exams')->with('error', 'Sesi sebelumnya dihentikan karena pelanggaran atau masalah jaringan. Silakan "Mulai Ujian" kembali jika masih memiliki kesempatan.');
        }

        if ($this->examService->isTimeExpired($attempt)) {
            // Waktu habis, force submit
            $this->examService->calculateScoreAndSubmit($attempt, true);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'expired',
                    'message' => 'Waktu ujian telah habis.',
                    'redirect' => route('exam.result', ['attempt' => $attempt->id])
                ], 403);
            }
            
            return redirect()->route('exam.result', ['attempt' => $attempt->id])
                ->with('warning', 'Waktu ujian telah habis. Jawaban otomatis dikumpulkan.');
        }

        return $next($request);
    }
}
