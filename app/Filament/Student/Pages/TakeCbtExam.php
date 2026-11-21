<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Page;
use Livewire\Attributes\Url;

class TakeCbtExam extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'take-cbt-exam/{record}';

    public $record;
    public $exam;
    public $attempt;
    #[Url]
    public int $currentQuestionIndex = 0;
    public $answers = [];
    public $isFinished = false;

    public function mount($record)
    {
        $this->record = $record;
        $this->exam = \App\Models\CbtExam::with('questions')->findOrFail($record);

        $user = auth()->user();
        if (!$user || !$user->student) {
            abort(403);
        }

        $student = $user->student;

        // Find active attempt or previous attempts
        $this->attempt = \App\Models\CbtAttempt::where('cbt_exam_id', $this->exam->id)
            ->where('student_id', $student->id)
            ->whereNull('end_time')
            ->first();

        if ($this->attempt) {
            // Load existing answers
            $existingAnswers = \App\Models\CbtAnswer::where('cbt_attempt_id', $this->attempt->id)->get();
            foreach ($existingAnswers as $ans) {
                if ($ans->selected_answer) {
                    $this->answers[$ans->cbt_question_id] = $ans->selected_answer;
                }
            }
            
            // Check if time is up
            if (now()->greaterThanOrEqualTo($this->attempt->start_time->copy()->addMinutes($this->exam->duration_minutes))) {
                $this->submitExam();
            }
        }
    }

    public function startExam()
    {
        $user = auth()->user();
        $student = $user->student;

        $attemptsCount = \App\Models\CbtAttempt::where('cbt_exam_id', $this->exam->id)
            ->where('student_id', $student->id)
            ->count();

        if ($attemptsCount >= $this->exam->max_attempts) {
            \Filament\Notifications\Notification::make()
                ->title('Maximum attempts reached')
                ->danger()
                ->send();
            return redirect(route('filament.student.pages.student-cbt-exams'));
        }

        $this->attempt = \App\Models\CbtAttempt::create([
            'cbt_exam_id' => $this->exam->id,
            'student_id' => $student->id,
            'start_time' => now(),
        ]);
    }

    public function selectAnswer($questionId, $option)
    {
        $this->answers[$questionId] = $option;
        
        // Save answer to DB
        \App\Models\CbtAnswer::updateOrCreate(
            [
                'cbt_attempt_id' => $this->attempt->id,
                'cbt_question_id' => $questionId,
            ],
            [
                'selected_answer' => $option,
            ]
        );
    }

    public function cancelExam()
    {
        if (!$this->attempt || $this->attempt->end_time) {
            return;
        }

        $this->attempt->update([
            'end_time' => now(),
            'score' => 0,
            'status' => 'locked',
        ]);

        $this->isFinished = true;
    }

    public function submitExam()
    {
        if (!$this->attempt || $this->attempt->end_time) {
            return;
        }

        $score = 0;
        foreach ($this->exam->questions as $question) {
            $selected = $this->answers[$question->id] ?? null;
            if ($selected === $question->correct_answer) {
                $score += $question->points;
            }
        }

        $this->attempt->update([
            'end_time' => now(),
            'score' => $score,
        ]);

        $this->isFinished = true;
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->exam->questions) - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function setQuestion($index)
    {
        $this->currentQuestionIndex = $index;
    }

    protected string $view = 'filament.student.pages.take-cbt-exam';
}
