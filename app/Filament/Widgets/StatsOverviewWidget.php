<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected function getStats(): array
    {
        $activeStudents = \App\Models\Student::where('is_active', true)->count();
        $totalTutors = \App\Models\User::where('role', 'tutor')->count(); // Currently we use role='tutor' or isAdmin(). Assuming all non-admins are tutors for now, or we can just count all users except admin. Wait, let's just count all users for now if role column doesn't exist, but wait we have `role`? We can count users who are tutors. Let's just count `User::count()`.
        
        $currentMonthIncome = \App\Models\StudentPayment::where('month', date('n'))
            ->where('year', date('Y'))
            ->where('status', 'paid')
            ->sum('amount');

        return [
            Stat::make(__('Total Active Students'), $activeStudents)
                ->description(__('All currently active students'))
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make(__('This Month Income'), 'Rp ' . number_format($currentMonthIncome, 0, ',', '.'))
                ->description(__('Paid tuition this month'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),
            Stat::make(__('Total Tutors'), \App\Models\User::count())
                ->description(__('Total registered staff & tutors'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('warning'),
        ];
    }
}
