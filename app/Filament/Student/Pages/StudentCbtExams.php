<?php

namespace App\Filament\Student\Pages;

use App\Models\CbtAttempt;
use App\Models\CbtExam;
use Filament\Pages\Page;

class StudentCbtExams extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';

    public static function getNavigationLabel(): string
    {
        return __('Exam');
    }

    public function getTitle(): string
    {
        return __('Available Exams');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Evaluation');
    }

    protected string $view = 'filament.student.pages.student-cbt-exams';

    public $exams = [];
    public array $attemptsByExam = [];

    public function mount()
    {
        $user = auth()->user();
        if ($user && $user->student) {
            $student = $user->student;
            if ($student->course_level === 'G-English Brain Champion' || !empty($student->competition_code)) {
                $this->exams = collect();
                return;
            }

            if ($user->username === 'carmen') {
                $this->exams = CbtExam::with('subject:id,name')
                    ->where('is_active', true)
                    ->latest('id')
                    ->get();
            } else {
                $studentClasses = $student->classes()->pluck('classes.id')->toArray();
                $this->exams = CbtExam::with('subject:id,name')
                    ->whereIn('class_id', $studentClasses)
                    ->where('is_active', true)
                    ->get();
            }

            // Single bulk query for all attempts (eliminates N+1 inside Blade loop)
            $examIds = $this->exams->pluck('id')->all();
            if (!empty($examIds)) {
                $attempts = CbtAttempt::where('student_id', $student->id)
                    ->whereIn('cbt_exam_id', $examIds)
                    ->latest('id')
                    ->get();
                foreach ($attempts as $att) {
                    if (!isset($this->attemptsByExam[$att->cbt_exam_id])) {
                        $this->attemptsByExam[$att->cbt_exam_id] = $att;
                    }
                }
            }
        }
    }
}
