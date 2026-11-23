<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class IncomeChartWidget extends ChartWidget
{
    protected static bool $isLazy = false;

    public function getHeading(): ?string
    {
        return __('Income Chart This Year');
    }

    protected function getData(): array
    {
        // Get income per month for the current year
        $incomeData = [];
        for ($month = 1; $month <= 12; $month++) {
            $total = \App\Models\StudentPayment::whereYear('paid_at', date('Y'))->whereMonth('paid_at', $month)->where('status', 'paid')->sum('amount');
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
