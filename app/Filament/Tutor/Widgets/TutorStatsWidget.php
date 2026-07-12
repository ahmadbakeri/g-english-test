<?php

namespace App\Filament\Tutor\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Tutor;
use App\Models\ClassRoom;
use App\Models\Journal;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

class TutorStatsWidget extends BaseStatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $user = auth()->user();
        $tutor = Tutor::where('user_id', $user->id)->first();

        if (!$tutor) {
            return [
                Stat::make('Status', __('Tutor data is not connected'))
                    ->color('danger'),
            ];
        }

        $totalClasses = ClassRoom::where('tutor_id', $tutor->id)->where('is_active', true)->count();
        $totalStudents = ClassRoom::where('tutor_id', $tutor->id)
            ->where('is_active', true)
            ->withCount('students')
            ->get()
            ->sum('students_count');

        $monthJournals = Journal::where('tutor_id', $tutor->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        return [
            Stat::make(__('Active Classes'), $totalClasses)
                ->description(__('Classes you currently teach'))
                ->descriptionIcon('heroicon-m-building-library')
                ->chart([3, 5, 4, 6, $totalClasses])
                ->color('primary'),
            Stat::make(__('Total Students'), $totalStudents)
                ->description(__('Students across all your classes'))
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([5, 8, 6, 9, $totalStudents])
                ->color('success'),
            Stat::make(__('Journals This Month'), $monthJournals)
                ->description(__('Journals filled this month'))
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info'),
        ];
    }
}
