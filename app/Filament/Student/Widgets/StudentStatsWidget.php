<?php

namespace App\Filament\Student\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\TestScore;
use Illuminate\Support\Carbon;

class StudentStatsWidget extends BaseStatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return [
                Stat::make('Status', __('Student data is not connected'))
                    ->color('danger'),
            ];
        }

        $totalClasses = $student->classes()->where('is_active', true)->count();

        $monthAttendance = Attendance::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'present')
            ->count();

        $totalAttendance = Attendance::where('student_id', $student->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        $attendancePercentage = $totalAttendance > 0
            ? round(($monthAttendance / $totalAttendance) * 100)
            : 0;

        $latestScore = TestScore::where('student_id', $student->id)
            ->latest()
            ->first();

        return [
            Stat::make(__('Registered Classes'), $totalClasses)
                ->description(__('Active classes you are enrolled in'))
                ->descriptionIcon('heroicon-m-building-library')
                ->chart([2, 3, $totalClasses, $totalClasses])
                ->color('primary'),
            Stat::make(__('Attendance This Month'), $attendancePercentage . '%')
                ->description($monthAttendance . ' ' . __('of') . ' ' . $totalAttendance . ' ' . __('meetings'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([60, 70, 80, $attendancePercentage])
                ->color($attendancePercentage >= 75 ? 'success' : 'danger'),
            Stat::make(__('Latest Score'), $latestScore ? $latestScore->score : '-')
                ->description($latestScore ? $latestScore->test->name ?? __('Exam') : __('No score yet'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),
        ];
    }
}
