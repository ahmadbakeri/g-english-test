<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Page;

class StudentCbtExams extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Exam';
    protected static ?string $title = 'Available Exams';

    public static function getNavigationGroup(): ?string
    {
        return __('Evaluation');
    }

    protected string $view = 'filament.student.pages.student-cbt-exams';

    public $exams = [];

    public function mount()
    {
        $user = auth()->user();
        if ($user && $user->student) {
            if ($user->student->course_level === 'G-English Brain Champion' || !empty($user->student->competition_code)) {
                $this->exams = collect();
                return;
            }
            $studentClasses = $user->student->classes()->pluck('classes.id')->toArray();
            $this->exams = \App\Models\CbtExam::with('subject')
                ->whereIn('class_id', $studentClasses)
                ->where('is_active', true)
                ->get();
        }
    }
}
