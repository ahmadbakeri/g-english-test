<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class IncomeChartWidget extends ChartWidget
{
    protected ?string $heading = 'Grafik Pendapatan Tahun Ini';

    protected function getData(): array
    {
        // Get income per month for the current year
        $incomeData = [];
        for ($month = 1; $month <= 12; $month++) {
            $total = \App\Models\StudentPayment::where('year', date('Y'))
                ->where('month', $month)
                ->where('status', 'lunas')
                ->sum('amount');
            $incomeData[] = $total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pendapatan (Rp)',
                    'data' => $incomeData,
                    'borderColor' => '#0ea5e9',
                    'backgroundColor' => 'rgba(14, 165, 233, 0.1)',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
