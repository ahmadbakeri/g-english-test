<?php

namespace App\Filament\Pages;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $title = 'Dashboard';
    protected static ?int $navigationSort = -2;

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\WelcomeWidget::class,
            \App\Filament\Widgets\StatsOverviewWidget::class,
            \App\Filament\Widgets\QuickLinksWidget::class,
            \App\Filament\Widgets\RecentActivityWidget::class,
        ];
    }
}
