<?php

namespace App\Filament\Parent\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ParentStatsWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = -1;

    protected function getStats(): array
    {
        $user = Auth::user();
        $children = $user->children;
        $childrenCount = $children->count();

        // Calculate total attendances this month for all children
        $childStudentIds = $children->pluck('id')->toArray();
        $childUserIds = $children->pluck('user_id')->filter()->toArray();
        $attendanceCount = \App\Models\Attendance::where(function ($q) use ($childStudentIds, $childUserIds) {
                $q->whereIn('student_id', $childStudentIds);
                if (!empty($childUserIds)) {
                    $q->orWhereIn('user_id', $childUserIds);
                }
            })
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        return [
            Stat::make('Total Anak', $childrenCount)
                ->description('Jumlah anak yang terdaftar di Genza')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Presensi Bulan Ini', $attendanceCount)
                ->description('Total kehadiran bulan ini')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),
        ];
    }
}
