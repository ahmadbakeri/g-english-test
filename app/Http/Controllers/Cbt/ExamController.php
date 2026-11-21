<?php

namespace App\Http\Controllers\Cbt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CbtExam;
use App\Models\CbtAttempt;
use App\Services\ExamService;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    /**
     * Start a new exam attempt
     */
    public function start(CbtExam $exam)
    {
        $user = Auth::user();
        $student = $user->student;

        // Check if there is already an attempt
        $existingAttempt = $student->cbtAttempts()
            ->where('cbt_exam_id', $exam->id)
            ->first();

        if ($existingAttempt) {
            // Middleware already handles is_completed and max_attempts checks for start route.
            // Check if time is expired
            if (!$this->examService->isTimeExpired($existingAttempt)) {
                // Increment attempt_number (login count) since they are resuming
                $existingAttempt->increment('attempt_number');
                // Reset violation count for this new session
                $existingAttempt->update([
                    'violation_count' => 0,
                    'is_force_submitted' => false
                ]);
                
                return redirect()->route('exam.show', ['attempt' => $existingAttempt->id]);
            } else {
                // If expired, calculate score and finish it
                $this->examService->calculateScoreAndSubmit($existingAttempt, true);
                return redirect()->route('exam.result', ['attempt' => $existingAttempt->id])
                    ->with('error', 'Waktu ujian telah habis.');
            }
        }

        // Start new attempt
        $attempt = $this->examService->startAttempt($exam, $student->id);

        return redirect()->route('exam.show', ['attempt' => $attempt->id]);
    }

    /**
     * Show the exam page interface
     */
    public function show(CbtAttempt $attempt)
    {
        // View not implemented yet, just passing data
        $exam = $attempt->exam;
        
        // Fetch questions using service to utilize caching and ordering
        $questions = $this->examService->getQuestions($attempt->question_order ?? []);

        // Fetch already answered questions
        $answers = $attempt->answers()->pluck('selected_answer', 'cbt_question_id')->toArray();

        return view('cbt.exam', compact('attempt', 'exam', 'questions', 'answers'));
    }

    /**
     * Show the result page
     */
    public function result(CbtAttempt $attempt)
    {
        $user = Auth::user();
        if (!$user || !$user->student || $attempt->student_id !== $user->student->id) {
            abort(403, 'Akses ditolak.');
        }

        if (!$attempt->is_completed) {
            return redirect()->route('exam.show', ['attempt' => $attempt->id]);
        }

        $exam = $attempt->exam;
        
        // Cek pengaturan exam show_result_immediately
        if (!$exam->show_result_immediately) {
            return view('cbt.result_hidden', compact('attempt', 'exam'));
        }

        return view('cbt.result', compact('attempt', 'exam'));
    }
}
