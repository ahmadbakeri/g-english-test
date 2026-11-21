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
    protected static bool $isLazy = false;

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

        $latestEvaluation = \App\Models\Evaluation::where('student_id', $student->id)->latest('date')->first();
        $latestCbt = \App\Models\CbtAttempt::with('exam')->where('student_id', $student->id)->where('is_completed', true)->latest('updated_at')->first();

        $latestScoreValue = '-';
        $latestScoreTitle = __('No score yet');

        if ($latestEvaluation && $latestCbt) {
            if (\Carbon\Carbon::parse($latestEvaluation->date)->greaterThanOrEqualTo($latestCbt->updated_at)) {
                $latestScoreValue = $latestEvaluation->score;
                $latestScoreTitle = $latestEvaluation->notes ?: __('Manual Evaluation');
            } else {
                $latestScoreValue = $latestCbt->score;
                $latestScoreTitle = 'CBT: ' . ($latestCbt->exam->title ?? __('Exam'));
            }
        } elseif ($latestEvaluation) {
            $latestScoreValue = $latestEvaluation->score;
            $latestScoreTitle = $latestEvaluation->notes ?: __('Manual Evaluation');
        } elseif ($latestCbt) {
            $latestScoreValue = $latestCbt->score;
            $latestScoreTitle = 'CBT: ' . ($latestCbt->exam->title ?? __('Exam'));
        }

        return [
            Stat::make(__('Registered Classes'), $totalClasses)
                ->description(__('Active classes you are enrolled in'))
                ->descriptionIcon('heroicon-m-building-library')
                ->icon('heroicon-o-building-library')
                ->chart([2, 3, $totalClasses, $totalClasses])
                ->color('primary')
                ->extraAttributes(['class' => 'stat-card stat-indigo']),
            Stat::make(__('Attendance This Month'), $attendancePercentage . '%')
                ->description($monthAttendance . ' ' . __('of') . ' ' . $totalAttendance . ' ' . __('meetings'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->icon('heroicon-o-check-circle')
                ->chart([60, 70, 80, $attendancePercentage])
                ->color($attendancePercentage >= 75 ? 'success' : 'danger')
                ->extraAttributes(['class' => 'stat-card ' . ($attendancePercentage >= 75 ? 'stat-emerald' : 'stat-rose')]),
            Stat::make(__('Latest Score'), $latestScoreValue)
                ->description($latestScoreTitle)
                ->descriptionIcon('heroicon-m-academic-cap')
                ->icon('heroicon-o-academic-cap')
                ->color('info')
                ->extraAttributes(['class' => 'stat-card stat-sky']),
        ];
    }
}
