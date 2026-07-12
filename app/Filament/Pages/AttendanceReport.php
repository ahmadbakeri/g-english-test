<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Tutor;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceReport extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-chart-bar';
    public static function getNavigationLabel(): string { return __('Attendance Report'); }
    protected static ?string $title = 'Attendance Recap Report';
    public static function getNavigationGroup(): ?string
    {
        return __('Reports');
    }
    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    protected string $view = 'filament.pages.attendance-report';

    public $classId = '';
    public $month;
    public $year;
    public $reportData = [];
    public $selectedClass = null;
    public $dates = [];
    public $summary = [];

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function generateReport()
    {
        if (empty($this->classId)) return;

        $this->selectedClass = ClassRoom::with('students')->find($this->classId);
        if (!$this->selectedClass) return;

        $startDate = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Get all dates in the month
        $this->dates = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $this->dates[] = $current->copy();
            $current->addDay();
        }

        // Get attendances for this class this month
        $attendances = Attendance::where('class_id', $this->classId)
            ->where('role_type', 'student')
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->groupBy(function ($att) {
                return $att->student_id . '-' . Carbon::parse($att->date)->format('Y-m-d');
            });

        $this->reportData = [];
        $this->summary = ['present' => 0, 'late' => 0, 'absent' => 0, 'excused' => 0];

        foreach ($this->selectedClass->students as $student) {
            $row = ['student' => $student, 'days' => [], 'totals' => ['present' => 0, 'late' => 0, 'absent' => 0, 'excused' => 0]];

            foreach ($this->dates as $date) {
                $key = $student->id . '-' . $date->format('Y-m-d');
                $att = $attendances->get($key)?->first();
                $status = $att ? $att->status : null;
                $row['days'][] = $status;

                if ($status) {
                    $row['totals'][$status] = ($row['totals'][$status] ?? 0) + 1;
                    $this->summary[$status] = ($this->summary[$status] ?? 0) + 1;
                }
            }

            $this->reportData[] = $row;
        }
    }

    public function exportPdf()
    {
        $this->generateReport();

        if (empty($this->reportData)) return;

        $pdf = Pdf::loadView('reports.attendance-recap', [
            'reportData' => $this->reportData,
            'dates' => $this->dates,
            'selectedClass' => $this->selectedClass,
            'month' => $this->month,
            'year' => $this->year,
            'summary' => $this->summary,
        ])->setPaper('a4', 'landscape');

        $monthName = Carbon::create($this->year, $this->month, 1)->translatedFormat('F Y');
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "Rekap_Absensi_{$this->selectedClass->name}_{$monthName}.pdf");
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
