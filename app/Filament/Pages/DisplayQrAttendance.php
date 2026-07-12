<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Attendance;
use App\Models\ClassSchedule;
use Illuminate\Support\Carbon;

class DisplayQrAttendance extends Page
{
    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-presentation-chart-line';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Academic');
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationLabel(): string
    {
        return __('Display Attendance QR');
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return __('Display QR & Live Attendance');
    }

    public function getHeading(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    protected string $view = 'filament.pages.display-qr-attendance';

    public function getSchedulesProperty()
    {
        // 0 = Sunday, 1 = Monday, ... 6 = Saturday in PHP
        // Let's get today's day of week
        $dayMap = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
        $today = $dayMap[Carbon::today()->dayOfWeek];

        $schedules = ClassSchedule::with(['classRoom.subject', 'tutor'])->get();
        $todaySchedules = [];
        
        foreach ($schedules as $schedule) {
            if ($schedule->schedule_items) {
                foreach ($schedule->schedule_items as $item) {
                    if (isset($item['day']) && $item['day'] === $today) {
                        $newSchedule = clone $schedule;
                        $newSchedule->start_time = $item['start_time'] ?? null;
                        $newSchedule->end_time = $item['end_time'] ?? null;
                        $newSchedule->day = $item['day'];
                        $todaySchedules[] = $newSchedule;
                    }
                }
            }
        }
        
        return collect($todaySchedules)->sortBy('start_time')->values();
    }

    public function getRecentAttendancesProperty()
    {
        return Attendance::with(['student', 'tutor', 'user'])
            ->whereDate('date', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getQrCodeUrlProperty()
    {
        // Token changes every minute for security rotation
        $minuteBlock = date('Y-m-d-H-i');
        $token = md5($minuteBlock . config('app.key'));
        return url('/absen?token=' . $token);
    }
}
