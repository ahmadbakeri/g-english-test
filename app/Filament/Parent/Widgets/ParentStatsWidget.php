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

        $unpaidCount = \App\Models\StudentPayment::whereIn('student_id', $childStudentIds)
            ->where('status', 'unpaid')
            ->count();

        return [
            Stat::make('Total Anak Terdaftar', $childrenCount)
                ->description('Siswa aktif terhubung ke akun Anda')
                ->descriptionIcon('heroicon-m-user-group')
                ->icon('heroicon-o-user-group')
                ->chart([1, $childrenCount, $childrenCount])
                ->color('primary')
                ->extraAttributes(['class' => 'stat-card stat-indigo']),

            Stat::make('Presensi Bulan Ini', $attendanceCount . ' Sesi')
                ->description('Total kehadiran tercatat bulan ini')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->icon('heroicon-o-clipboard-document-check')
                ->chart([2, 4, 6, max(1, $attendanceCount)])
                ->color('success')
                ->extraAttributes(['class' => 'stat-card stat-emerald']),

            Stat::make('Tagihan SPP Aktif', $unpaidCount > 0 ? "{$unpaidCount} Belum Lunas" : 'Lunas')
                ->description($unpaidCount > 0 ? 'Menunggu penyelesaian pembayaran' : 'Seluruh tagihan SPP sudah lunas')
                ->descriptionIcon('heroicon-m-banknotes')
                ->icon('heroicon-o-banknotes')
                ->chart($unpaidCount > 0 ? [3, 2, $unpaidCount] : [1, 1, 1])
                ->color($unpaidCount > 0 ? 'warning' : 'info')
                ->extraAttributes(['class' => $unpaidCount > 0 ? 'stat-card stat-amber' : 'stat-card stat-sky']),
        ];
    }
}
