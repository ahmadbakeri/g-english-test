<?php

namespace App\Filament\Resources\Academic\CbtExams\Widgets;

use App\Models\CbtExam;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExamStatsOverview extends BaseWidget
{
    public ?CbtExam $record = null;

    protected function getStats(): array
    {
        if (!$this->record) {
            return [];
        }

        $activeUsers = $this->record->attempts()
            ->where('status', 'in_progress')
            ->where('is_completed', false)
            ->count();

        $submittedUsers = $this->record->attempts()
            ->where('status', 'finished')
            ->count();

        $averageScore = $this->record->attempts()
            ->where('status', 'finished')
            ->avg('score') ?? 0;

        $totalViolations = $this->record->attempts()
            ->sum('violation_count') ?? 0;

        return [
            Stat::make('Siswa Sedang Mengerjakan', $activeUsers)
                ->description('Saat ini aktif')
                ->descriptionIcon('heroicon-m-user-group')
                ->color($activeUsers > 0 ? 'success' : 'gray'),
            
            Stat::make('Siswa Sudah Selesai', $submittedUsers)
                ->description('Telah submit ujian')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('primary'),

            Stat::make('Rata-rata Skor', number_format($averageScore, 2))
                ->description('Dari ujian yang sudah disubmit')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
                
            Stat::make('Total Pelanggaran Deteksi', $totalViolations)
                ->description('Tab switch / Blur')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($totalViolations > 0 ? 'danger' : 'success'),
        ];
    }
}
