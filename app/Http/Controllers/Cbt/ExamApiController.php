<?php

namespace App\Http\Controllers\Cbt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CbtAttempt;
use App\Models\CbtAnswer;
use App\Services\ExamService;

class ExamApiController extends Controller
{
    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    /**
     * Auto-save answer
     */
    public function autoSave(Request $request, CbtAttempt $attempt)
    {
        $request->validate([
            'question_id' => 'required|exists:cbt_questions,id',
            'answer' => 'required|string',
        ]);

        CbtAnswer::updateOrCreate(
            [
                'cbt_attempt_id' => $attempt->id,
                'cbt_question_id' => $request->question_id,
            ],
            [
                'selected_answer' => $request->answer,
            ]
        );

        $attempt->update(['last_auto_save_at' => now()]);

        return response()->json(['status' => 'success', 'message' => 'Jawaban disimpan.']);
    }

    /**
     * Final submit
     */
    public function submit(Request $request, CbtAttempt $attempt)
    {
        // If there are pending answers in the request, save them first
        if ($request->has('answers') && is_array($request->answers)) {
            foreach ($request->answers as $questionId => $answerStr) {
                CbtAnswer::updateOrCreate(
                    [
                        'cbt_attempt_id' => $attempt->id,
                        'cbt_question_id' => $questionId,
                    ],
                    [
                        'selected_answer' => $answerStr,
                    ]
                );
            }
        }

        $this->examService->calculateScoreAndSubmit($attempt, false);

        return response()->json([
            'status' => 'success', 
            'message' => 'Ujian berhasil diselesaikan.',
            'redirect' => route('exam.result', ['attempt' => $attempt->id])
        ]);
    }

    /**
     * Record violation (anti-cheat: tab switch, lost focus)
     */
    public function violation(Request $request, CbtAttempt $attempt)
    {
        $attempt->increment('violation_count');

        if ($attempt->violation_count > $attempt->max_violations) {
            // TIDAK di-submit otomatis. Hanya di-kick (dikeluarkan dari halaman ujian).
            // Siswa masih bisa kembali JIKA attempt_number belum melebihi max_attempts.
            $attempt->update(['is_force_submitted' => true]);
            
            return response()->json([
                'status' => 'blocked',
                'message' => 'Anda telah melebihi batas pelanggaran maksimal (Anti-Curang). Anda dikeluarkan dari ujian.',
                'redirect' => route('exam.result', ['attempt' => $attempt->id])
            ], 403);
        }

        return response()->json([
            'status' => 'warning',
            'message' => 'Pelanggaran tercatat. Peringatan: ' . $attempt->violation_count . '/' . $attempt->max_violations,
            'violations_left' => $attempt->max_violations - $attempt->violation_count
        ]);
    }

    /**
     * Get time remaining
     */
    public function timeRemaining(Request $request, CbtAttempt $attempt)
    {
        return response()->json([
            'status' => 'success',
            'time_remaining_seconds' => $this->examService->getTimeRemaining($attempt)
        ]);
    }
}
