<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class AttendanceChartWidget extends ChartWidget
{
    protected ?string $heading = 'Statistik Kehadiran (Bulan Ini)';

    protected function getData(): array
    {
        $currentMonth = date('n');
        $currentYear = date('Y');

        $hadir = \App\Models\Attendance::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)->where('status', 'hadir')->count();
        $sakit = \App\Models\Attendance::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)->where('status', 'sakit')->count();
        $izin = \App\Models\Attendance::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)->where('status', 'izin')->count();
        $alpa = \App\Models\Attendance::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)->where('status', 'alpa')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' => [$hadir, $sakit, $izin, $alpa],
                    'backgroundColor' => [
                        '#22c55e', // green (hadir)
                        '#eab308', // yellow (sakit)
                        '#3b82f6', // blue (izin)
                        '#ef4444', // red (alpa)
                    ],
                ],
            ],
            'labels' => [__('Present'), __('Sick'), __('Excused'), __('Absent')],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
