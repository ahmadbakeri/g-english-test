<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Page;
use App\Models\Attendance;
use App\Models\Evaluation;

class GradeReport extends Page
{
    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-document-chart-bar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Evaluation';
    }

    public static function getNavigationLabel(): string
    {
        return 'Grade Report';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }
    
    protected string $view = 'filament.student.pages.grade-report';

    public $reportCards = [];

    public function mount()
    {
        $student = auth()->user()->student;

        if (!$student) {
            abort(403, 'User is not a student');
        }

        $classes = $student->classes()->with('subject')->get();

        foreach ($classes as $class) {
            $totalSessions = Attendance::where('class_id', $class->id)
                ->where('student_id', $student->id)
                ->count();
                
            $presentSessions = Attendance::where('class_id', $class->id)
                ->where('student_id', $student->id)
                ->whereIn('status', ['present', 'late', 'excused'])
                ->count();
                
            $attendanceScore = $totalSessions > 0 ? round(($presentSessions / $totalSessions) * 100) : 0;
            
            $manualEvaluations = Evaluation::where('student_id', $student->id)
                ->where('subject_id', $class->subject_id)
                ->get()
                ->map(function($eval) {
                    return (object)[
                        'date' => $eval->date,
                        'notes' => $eval->notes ?: 'Tugas / Evaluasi',
                        'score' => $eval->score,
                        'is_cbt' => false
                    ];
                });
                
            $cbtAttempts = \App\Models\CbtAttempt::with('exam')
                ->where('student_id', $student->id)
                ->where('is_completed', true)
                ->whereHas('exam', function($q) use ($class) {
                    $q->where('subject_id', $class->subject_id);
                })
                ->get()
                ->map(function($attempt) {
                    return (object)[
                        'date' => $attempt->created_at->toDateString(),
                        'notes' => 'CBT Exam: ' . ($attempt->exam->title ?? 'Unknown'),
                        'score' => $attempt->score,
                        'is_cbt' => true
                    ];
                });
                
            $allEvaluations = collect($manualEvaluations->all())
                ->concat($cbtAttempts->all())
                ->sortByDesc('date')
                ->values();
                
            $cbtAverage = $allEvaluations->count() > 0 ? round($allEvaluations->avg('score')) : 0;
            
            // Formula: 40% Attendance + 60% CBT Exams
            $finalGrade = round(($attendanceScore * 0.40) + ($cbtAverage * 0.60));

            $this->reportCards[] = [
                'class_name' => $class->name,
                'subject_name' => $class->subject->name ?? 'No Subject',
                'total_sessions' => $totalSessions,
                'present_sessions' => $presentSessions,
                'attendance_score' => $attendanceScore,
                'cbt_average' => $cbtAverage,
                'final_grade' => $finalGrade,
                'evaluations' => $allEvaluations
            ];
        }
    }
}
