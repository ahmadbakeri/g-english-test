<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\AccessLogsPage;
use App\Filament\Pages\ScanAttendance;
use App\Filament\Resources\Evaluation\TestScheduleRequestResource;
use App\Models\TestScheduleRequest;
use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.welcome-widget';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = -1;

    public function getPendingScheduleRequestsProperty(): int
    {
        return TestScheduleRequest::where('status', 'pending')->count();
    }

    public function getScanUrlProperty(): string
    {
        return ScanAttendance::getUrl();
    }

    public function getScheduleRequestUrlProperty(): string
    {
        return TestScheduleRequestResource::getUrl('index');
    }

    public function getAccessLogsUrlProperty(): string
    {
        return AccessLogsPage::getUrl();
    }
}
