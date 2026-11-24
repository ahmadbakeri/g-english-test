<?php

namespace App\Filament\Resources\Evaluation;

use App\Filament\Resources\Evaluation\TestScheduleRequestResource\Pages;
use App\Models\Student;
use App\Models\TestScheduleRequest;
use App\Models\TestSession;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TestScheduleRequestResource extends Resource
{
    protected static ?string $model = TestScheduleRequest::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?string $slug = 'evaluation/test-schedule-requests';

    public static function getNavigationLabel(): string { return __('Test Schedule Requests'); }
    public static function getModelLabel(): string { return __('Test Schedule Request'); }
    public static function getPluralModelLabel(): string { return __('Test Schedule Requests'); }
    public static function getNavigationGroup(): ?string { return __('Exams & Evaluation'); }
    public static function getNavigationSort(): ?int { return 4; }

    public static function getNavigationBadge(): ?string
    {
        $pending = TestScheduleRequest::where('status', 'pending')->count();
        return $pending > 0 ? (string) $pending : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make(__('Test Schedule Request Data'))->schema([
                Forms\Components\Select::make('test_session_id')
                    ->label('Sesi Tes & Ketersediaan Server')
                    ->options(fn (): array => TestSession::where('is_active', true)
                        ->orderBy('session_date')
                        ->orderBy('start_time')
                        ->get()
                        ->mapWithKeys(fn (TestSession $s) => [$s->id => $s->option_label])
                        ->all())
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('requested_seats')
                    ->label('Jumlah Kursi / Peserta Diminta')
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->required(),
                Forms\Components\Select::make('student_id')
                    ->label('Siswa (Opsional, untuk request perorangan)')
                    ->options(Student::pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                Forms\Components\TextInput::make('school_name')
                    ->label('Nama Sekolah / Kelas')
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu Approval',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->default('pending')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan Pemohon')
                    ->rows(2)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('review_notes')
                    ->label('Catatan Approval / Penolakan')
                    ->rows(2)
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordAction('edit')
            ->columns([
                Tables\Columns\TextColumn::make('session.title')
                    ->label('Sesi & Server')
                    ->weight('bold')
                    ->description(function (TestScheduleRequest $record): string {
                        $s = $record->session;
                        if (!$s) return '-';
                        $dt = $s->session_date?->format('d M Y') . ' ' . substr((string) $s->start_time, 0, 5);
                        return "{$dt} • {$s->server_name} (Sisa {$s->remainingCapacity()}/{$s->max_capacity})";
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('requester.name')
                    ->label('Pemohon')
                    ->weight('bold')
                    ->description(fn (TestScheduleRequest $record): string => strtoupper($record->requester_role) . ' • ' . ($record->school_name ?: ($record->student?->name ?? '-')))
                    ->searchable(),
                Tables\Columns\TextColumn::make('requested_seats')
                    ->label('Kuota Diminta')
                    ->badge()
                    ->color('info')
                    ->suffix(' kursi'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => 'Pending',
                    })
                    ->description(fn (TestScheduleRequest $record): ?string => $record->review_notes ? \Illuminate\Support\Str::limit($record->review_notes, 35) : null),
            ])
            ->filters([
                Tables\Filters\Filter::make('date_range')
                    ->label('Rentang Tanggal Request')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->columns(2)
                    ->query(fn ($query, array $data) => $query
                        ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                        ->when($data['until'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '<=', $d))),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
                Tables\Filters\SelectFilter::make('test_session_id')
                    ->label('Sesi Tes')
                    ->options(TestSession::pluck('title', 'id')),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->button()
                    ->size('sm')
                    ->visible(fn (TestScheduleRequest $record): bool => $record->status !== 'approved')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Request Jadwal Tes?')
                    ->modalDescription(fn (TestScheduleRequest $record): string => "Sistem akan mengunci {$record->requested_seats} kursi pada {$record->session?->server_name} jika kapasitas masih tersedia.")
                    ->action(function (TestScheduleRequest $record): void {
                        $res = $record->approveBy(Auth::user());
                        Notification::make()
                            ->title($res['ok'] ? 'Jadwal Disetujui' : 'Kapasitas Server Penuh')
                            ->body($res['message'])
                            ->status($res['ok'] ? 'success' : 'danger')
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->button()
                    ->size('sm')
                    ->visible(fn (TestScheduleRequest $record): bool => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Alasan Penolakan')
                            ->default('Kapasitas server sesi ini penuh, silakan pilih sesi lain.')
                            ->required(),
                    ])
                    ->action(function (TestScheduleRequest $record, array $data): void {
                        $record->rejectBy(Auth::user(), $data['reason']);
                        Notification::make()
                            ->title('Request Jadwal Ditolak')
                            ->warning()
                            ->send();
                    }),
                ActionGroup::make([
                    EditAction::make()->slideOver()->modalWidth('2xl'),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTestScheduleRequests::route('/'),
        ];
    }
}
