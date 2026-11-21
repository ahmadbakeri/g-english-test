<?php

namespace App\Services;

use App\Models\CbtExam;
use App\Models\CbtAttempt;
use App\Models\CbtQuestion;
use App\Models\CbtAnswer;
use Illuminate\Support\Facades\Cache;

class ExamService
{
    /**
     * Start a new attempt for a student
     */
    public function startAttempt(CbtExam $exam, $studentId)
    {
        // Get question IDs
        $questionIds = $this->getQuestionIds($exam);
        
        if ($exam->shuffle_questions) {
            shuffle($questionIds);
        }

        $now = now();
        $endTime = (clone $now)->addMinutes($exam->duration_minutes);

        // Calculate attempt number
        $attemptNumber = CbtAttempt::where('cbt_exam_id', $exam->id)
            ->where('student_id', $studentId)
            ->count() + 1;

        $attempt = CbtAttempt::create([
            'cbt_exam_id' => $exam->id,
            'student_id' => $studentId,
            'attempt_number' => $attemptNumber,
            'start_time' => $now,
            'end_time' => $endTime,
            'status' => 'in_progress',
            'is_completed' => false,
            'violation_count' => 0,
            'max_violations' => 3, // Or read from settings
            'question_order' => $questionIds,
            'is_force_submitted' => false,
        ]);

        return $attempt;
    }

    /**
     * Get question IDs for an exam, with caching
     */
    public function getQuestionIds(CbtExam $exam)
    {
        return Cache::remember("exam_{$exam->id}_question_ids", 60 * 60, function () use ($exam) {
            return $exam->questions()->pluck('id')->toArray();
        });
    }

    /**
     * Get questions by IDs
     */
    public function getQuestions(array $questionIds)
    {
        $questions = CbtQuestion::whereIn('id', $questionIds)->get()->keyBy('id');
        
        // Reorder based on questionIds array
        $ordered = [];
        foreach ($questionIds as $id) {
            if (isset($questions[$id])) {
                $ordered[] = $questions[$id];
            }
        }
        
        return collect($ordered);
    }

    /**
     * Get time remaining in seconds
     */
    public function getTimeRemaining(CbtAttempt $attempt)
    {
        if ($attempt->is_completed) {
            return 0;
        }

        if (is_null($attempt->end_time)) {
            // Jika tidak ada batas waktu (misal duration 0)
            return 999999; // Atau kembalikan null dan tangani di view, tapi integer lebih mudah.
        }

        $now = now();
        if ($now->greaterThanOrEqualTo($attempt->end_time)) {
            return 0;
        }

        return $now->diffInSeconds($attempt->end_time);
    }

    /**
     * Check if attempt time is expired
     */
    public function isTimeExpired(CbtAttempt $attempt)
    {
        return $this->getTimeRemaining($attempt) <= 0;
    }

    /**
     * Calculate score and submit attempt
     */
    public function calculateScoreAndSubmit(CbtAttempt $attempt, $isForceSubmit = false)
    {
        $exam = $attempt->exam;
        $answers = $attempt->answers()->with('question')->get();

        $totalScore = 0;
        $maxScore = 0; // Total possible score if points are variable

        foreach ($answers as $answer) {
            $question = $answer->question;
            $maxScore += $question->points ?? 1;

            if ($answer->selected_answer === $question->correct_answer) {
                $totalScore += $question->points ?? 1;
                $answer->update(['is_correct' => true]);
            } else {
                $answer->update(['is_correct' => false]);
            }
        }

        // Calculate percentage (scale 0-100) if required, or keep raw score
        $finalScore = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;

        $attempt->update([
            'status' => 'finished',
            'is_completed' => true,
            'is_force_submitted' => $isForceSubmit,
            'score' => $finalScore,
        ]);

        return $attempt;
    }
}
