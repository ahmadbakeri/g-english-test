<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\ClassRoom;
use App\Models\Test;
use App\Models\TestScore;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentReport extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';
    public static function getNavigationLabel(): string { return __('Report Card'); }
    protected static ?string $title = 'Student Report Card';
    public static function getNavigationGroup(): ?string
    {
        return __('Reports');
    }
    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    protected string $view = 'filament.pages.student-report';

    public $classId = '';
    public $reportData = [];
    public $selectedClass = null;

    public function generateReport()
    {
        if (empty($this->classId)) return;

        $this->selectedClass = ClassRoom::with(['students', 'subject', 'academicYear'])->find($this->classId);
        if (!$this->selectedClass) return;

        // Get tests for this class
        $tests = Test::where('class_id', $this->classId)->orderBy('date')->get();
        $testIds = $tests->pluck('id')->toArray();

        // Get scores
        $scores = TestScore::whereIn('test_id', $testIds)->get()->groupBy('student_id');

        // Get attendances for this class
        $attendances = Attendance::where('class_id', $this->classId)
            ->where('role_type', 'student')
            ->get()
            ->groupBy('student_id');

        $this->reportData = [];

        foreach ($this->selectedClass->students as $student) {
            $studentScores = $scores->get($student->id) ?? collect();
            $studentAtts = $attendances->get($student->id) ?? collect();

            $testResults = [];
            $totalScore = 0;
            
            foreach ($tests as $test) {
                $scoreRecord = $studentScores->where('test_id', $test->id)->first();
                $score = $scoreRecord ? $scoreRecord->score : 0;
                $testResults[] = [
                    'name' => $test->name,
                    'type' => $test->type,
                    'score' => $score,
                    'max_score' => $test->max_score,
                ];
                $totalScore += $score;
            }

            $avgScore = count($tests) > 0 ? round($totalScore / count($tests), 2) : 0;

            $attSummary = [
                'present' => $studentAtts->where('status', 'present')->count(),
                'late' => $studentAtts->where('status', 'late')->count(),
                'absent' => $studentAtts->where('status', 'absent')->count(),
                'excused' => $studentAtts->where('status', 'excused')->count(),
            ];

            $this->reportData[] = [
                'student' => $student,
                'tests' => $testResults,
                'avg_score' => $avgScore,
                'attendance' => $attSummary,
            ];
        }
    }

    public function exportPdf()
    {
        $this->generateReport();

        if (empty($this->reportData)) return;

        $pdf = Pdf::loadView('reports.student-raport', [
            'reportData' => $this->reportData,
            'selectedClass' => $this->selectedClass,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "Raport_{$this->selectedClass->name}.pdf");
    }

    public function getClassOptionsProperty()
    {
        if (auth()->user()->isTutor() && auth()->user()->tutor) {
            return ClassRoom::where('tutor_id', auth()->user()->tutor->id)
                ->where('is_active', true)->pluck('name', 'id')->toArray();
        }
        return ClassRoom::where('is_active', true)->pluck('name', 'id')->toArray();
    }
}
