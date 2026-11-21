<?php

namespace App\Filament\Resources\HR\StudentResource\Pages;

use App\Filament\Resources\HR\StudentResource;
use App\Models\Student;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\View\PanelsRenderHook;
use Filament\Support\Facades\FilamentView;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::CONTENT_END,
            fn (): HtmlString => new HtmlString(<<<'HTML'
                <script>
                    document.addEventListener('livewire:navigated', () => {
                        window.__collapseAllStudentGroups && window.__collapseAllStudentGroups();
                    });
                    window.__collapseAllStudentGroups = function () {
                        setTimeout(() => {
                            document.querySelectorAll('.fi-ta-group-header').forEach((header) => {
                                // Ensure Filament table x-data has all rendered group titles in collapsedGroups
                                const tableRoot = header.closest('[x-data]');
                                if (tableRoot && tableRoot._x_dataStack && tableRoot._x_dataStack[0]) {
                                    const data = tableRoot._x_dataStack[0];
                                    if (Array.isArray(data.collapsedGroups) && data.collapsedGroups.length === 0) {
                                        const titles = [];
                                        tableRoot.querySelectorAll('.fi-ta-group-header h4, .fi-ta-group-header .fi-ta-group-heading').forEach((el) => {
                                            const txt = el.textContent.replace(/^[^:]+:\s*/, '').trim();
                                            if (txt && !titles.includes(txt)) titles.push(txt);
                                        });
                                    }
                                }
                            });
                        }, 50);
                    };
                </script>
            HTML),
            scopes: static::class,
        );
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'internal';
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Siswa')
                ->icon('heroicon-o-user-group')
                ->badge(fn (): int => Student::count()),
            'internal' => Tab::make('Siswa Internal')
                ->icon('heroicon-o-building-library')
                ->badge(fn (): int => Student::where('student_type', 'internal')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('student_type', 'internal')),
            'external' => Tab::make('Siswa Eksternal')
                ->icon('heroicon-o-globe-alt')
                ->badge(fn (): int => Student::where('student_type', 'external')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('student_type', 'external')),
        ];
    }

    public function updatedActiveTab(): void
    {
        parent::updatedActiveTab();

        if ($this->activeTab === 'internal') {
            $this->tableGrouping = 'class_name';
        } elseif ($this->activeTab === 'external') {
            $this->tableGrouping = 'school_name';
        } else {
            $this->tableGrouping = 'class_name';
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('resend_school_account_email')
                ->label('Kirim Ulang Akun Sekolah (Email)')
                ->icon('heroicon-o-envelope')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\Select::make('school_user_id')
                        ->label('Pilih Akun Sekolah (Brain Champion)')
                        ->options(fn () => \App\Models\User::where('role', 'school')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $u = $state ? \App\Models\User::find($state) : null;
                            if ($u) {
                                $set('email', $u->email);
                            }
                        })
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('email')
                        ->label('Email Tujuan')
                        ->email()
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('new_password')
                        ->label('Password / PIN Baru')
                        ->default(\Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6)))
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $schoolUser = \App\Models\User::findOrFail($data['school_user_id']);
                    $schoolUser->update([
                        'email' => strtolower(trim((string) $data['email'])),
                        'password' => \Illuminate\Support\Facades\Hash::make($data['new_password']),
                    ]);

                    $participants = Student::where('school_user_id', $schoolUser->id)
                        ->where('course_level', 'G-English Brain Champion')
                        ->get()
                        ->map(fn (Student $s) => [
                            'competition_code' => $s->competition_code ?: $s->nis,
                            'nisn' => $s->nisn,
                            'name' => $s->name,
                            'class_name' => $s->class_name,
                            'gender' => $s->gender,
                        ])
                        ->toArray();

                    \Illuminate\Support\Facades\Mail::to($schoolUser->email)->queue(
                        new \App\Mail\SchoolAccountCredentialsMail(
                            schoolName: $schoolUser->name,
                            coordinatorName: 'Koordinator ' . $schoolUser->name,
                            loginUrl: url('/login'),
                            username: (string) $schoolUser->username,
                            plainPassword: (string) $data['new_password'],
                            participants: $participants,
                        )
                    );

                    \Filament\Notifications\Notification::make()
                        ->title("Email akun sekolah berhasil dikirim ke {$schoolUser->email}")
                        ->success()
                        ->send();
                }),
            Actions\Action::make('print_brain_champion_roster')
                ->label('Cetak Daftar Hadir Lomba (Per Kelas)')
                ->icon('heroicon-o-printer')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Select::make('grade')
                        ->label('Pilih Jenjang Kelas Brain Champion')
                        ->options([
                            'all' => 'Semua Kelas (Kelas 4 s/d Kelas 9)',
                            '4' => 'Kelas 4 (SD / MI)',
                            '5' => 'Kelas 5 (SD / MI)',
                            '6' => 'Kelas 6 (SD / MI)',
                            '7' => 'Kelas 7 (SMP / MTs)',
                            '8' => 'Kelas 8 (SMP / MTs)',
                            '9' => 'Kelas 9 (SMP / MTs)',
                        ])
                        ->default('all')
                        ->required(),
                ])
                ->action(function (array $data) {
                    return redirect()->to(route('report.brain-champion-roster', ['grade' => $data['grade'] ?? 'all']));
                }),
            Actions\ExportAction::make()->exporter(\App\Filament\Exports\StudentExporter::class),
            Actions\CreateAction::make()->label(__('Add Student')),
        ];
    }
}
