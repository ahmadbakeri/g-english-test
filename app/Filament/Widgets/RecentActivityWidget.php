<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\AccessLogsPage;
use App\Models\ActivityLog;
use Filament\Widgets\Widget;

class RecentActivityWidget extends Widget
{
    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 4;

    protected string $view = 'filament.widgets.recent-activity-widget';

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function getTodayActiveUsersProperty(): int
    {
        return (int) ActivityLog::query()
            ->whereDate('created_at', today())
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');
    }

    public function getLogsProperty()
    {
        return ActivityLog::query()
            ->with(['user.student:id,user_id,school_name,class_name'])
            ->latest('created_at')
            ->limit(15)
            ->get();
    }

    public function getDetailUrlProperty(): string
    {
        return AccessLogsPage::getUrl();
    }

    public static function roleMeta(?string $role): array
    {
        return match ($role) {
            'admin' => ['label' => 'Admin', 'class' => 'gx-pill-danger'],
            'tutor' => ['label' => 'Tutor', 'class' => 'gx-pill-gold'],
            'school' => ['label' => 'Sekolah', 'class' => 'gx-pill-primary'],
            'siswa' => ['label' => 'Siswa', 'class' => 'gx-pill-success'],
            'parent' => ['label' => 'Orang Tua', 'class' => 'gx-pill-muted'],
            'kemenag' => ['label' => 'Kemenag', 'class' => 'gx-pill-gold'],
            'kemendikdasmen' => ['label' => 'Kemendikdasmen', 'class' => 'gx-pill-primary'],
            default => ['label' => ucfirst((string) ($role ?: 'User')), 'class' => 'gx-pill-muted'],
        };
    }
}
