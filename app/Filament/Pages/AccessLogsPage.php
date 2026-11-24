<?php

namespace App\Filament\Pages;

use App\Models\ActivityLog;
use App\Support\TablePdfExporter;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;

class AccessLogsPage extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-finger-print';
    protected static ?string $slug = 'access-logs';
    protected static ?int $navigationSort = 2;
    protected string $view = 'filament.admin.pages.access-logs-page';

    public string $search = '';
    public string $roleFilter = '';
    public ?string $dateFrom = null;
    public ?string $dateUntil = null;

    public static function getNavigationLabel(): string
    {
        return 'Log Akses Pengguna';
    }

    public function getTitle(): string
    {
        return 'Aktivitas Akses Pengguna';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('System');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function mount(): void
    {
        $this->dateFrom = today()->toDateString();
        $this->dateUntil = today()->toDateString();
    }

    protected function filteredQuery()
    {
        $needle = mb_strtolower(trim($this->search));
        $query = ActivityLog::query()
            ->with(['user.student:id,user_id,school_name,class_name'])
            ->latest('created_at');

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateUntil) {
            $query->whereDate('created_at', '<=', $this->dateUntil);
        }
        if ($this->roleFilter !== '') {
            $query->whereHas('user', fn ($q) => $q->where('role', $this->roleFilter));
        }

        $items = $query->limit(300)->get();

        if ($needle !== '') {
            $items = $items->filter(function (ActivityLog $log) use ($needle): bool {
                $hay = implode(' ', [
                    $log->user?->username,
                    $log->user?->name,
                    $log->user?->email,
                    $log->user?->student?->school_name,
                    $log->page,
                    $log->ip,
                ]);
                return str_contains(mb_strtolower($hay), $needle);
            })->values();
        }

        return $items;
    }

    public function getLogsProperty()
    {
        return $this->filteredQuery();
    }

    public function getActiveUsersCountProperty(): int
    {
        return $this->logs->pluck('user_id')->filter()->unique()->count();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $logs = $this->logs;
                    $rows = [];
                    foreach ($logs as $i => $log) {
                        $u = $log->user;
                        $rows[] = [
                            $i + 1,
                            $u?->username ?: ($u?->email ?: '-'),
                            $u?->name ?: 'Guest',
                            strtoupper((string) ($u?->role ?: '-')),
                            $log->page ?: '-',
                            $log->ip ?: '-',
                            $log->created_at?->format('d/m/Y H:i:s') ?? '-',
                        ];
                    }

                    return TablePdfExporter::download(
                        filename: 'log-akses-pengguna-' . now()->format('Y-m-d') . '.pdf',
                        title: 'Log Aktivitas Akses Pengguna',
                        subtitle: TablePdfExporter::periodSubtitle($this->dateFrom, $this->dateUntil),
                        headers: ['No', 'Username', 'Nama Pengguna', 'Role', 'Halaman', 'IP Address', 'Waktu'],
                        rows: $rows,
                        summary: [
                            'Total Aktivitas' => $logs->count(),
                            'User Aktif Unik' => $this->activeUsersCount . ' user',
                        ],
                    );
                }),
        ];
    }
}
