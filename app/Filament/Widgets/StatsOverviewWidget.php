<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $activeStudents = \App\Models\Student::where('is_active', true)->count();
        $totalStaff = \App\Models\User::whereIn('role', ['admin', 'tutor'])->count();
        
        $currentMonthIncome = \App\Models\StudentPayment::where('month', date('n'))
            ->where('year', date('Y'))
            ->where('status', 'paid')
            ->sum('amount');

        return [
            Stat::make(__('Total Active Students'), $activeStudents)
                ->description(__('All currently active students'))
                ->descriptionIcon('heroicon-m-user-group')
                ->icon('heroicon-o-users')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success')
                ->extraAttributes(['class' => 'stat-card stat-emerald']),
            Stat::make(__('This Month Income'), 'Rp ' . number_format($currentMonthIncome, 0, ',', '.'))
                ->description(__('Paid tuition this month'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->icon('heroicon-o-banknotes')
                ->color('info')
                ->extraAttributes(['class' => 'stat-card stat-sky']),
            Stat::make(__('Total Tutors & Staff'), $totalStaff)
                ->description(__('Total registered staff & tutors'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->icon('heroicon-o-academic-cap')
                ->color('warning')
                ->extraAttributes(['class' => 'stat-card stat-amber']),
        ];
    }
}
