<?php

namespace App\Filament\School\Resources;

use App\Filament\School\Resources\SchoolTestScheduleRequestResource\Pages;
use App\Models\Student;
use App\Models\TestScheduleRequest;
use App\Models\TestSession;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SchoolTestScheduleRequestResource extends Resource
{
    protected static ?string $model = TestScheduleRequest::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?string $slug = 'test-schedule-requests';

    public static function getNavigationLabel(): string { return 'Request Jadwal Tes'; }
    public static function getModelLabel(): string { return 'Request Jadwal Tes'; }
    public static function getPluralModelLabel(): string { return 'Request Jadwal Tes'; }
    public static function getNavigationSort(): ?int { return 3; }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('session')
            ->where('requester_user_id', Auth::id());
    }

    public static function form(Schema $schema): Schema
    {
        $defaultSeats = max(1, Student::where('school_user_id', Auth::id())->count());

        return $schema->columns(1)->components([
            Section::make('Ajukan Jadwal Sesi Tes (Cek Ketersediaan Server)')->schema([
                Forms\Components\Select::make('test_session_id')
                    ->label('Pilih Sesi Tes & Kapasitas Server Tersedia')
                    ->options(fn (): array => TestSession::where('is_active', true)
                        ->orderBy('session_date')
                        ->orderBy('start_time')
                        ->get()
                        ->mapWithKeys(fn (TestSession $s) => [$s->id => $s->option_label])
                        ->all())
                    ->disableOptionWhen(fn (string $value): bool => (TestSession::find($value)?->remainingCapacity() ?? 0) <= 0)
                    ->searchable()
                    ->required()
                    ->helperText('Sesi yang kapasitas servernya sudah penuh otomatis tidak dapat dipilih.')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('requested_seats')
                    ->label('Jumlah Peserta Sekolah')
                    ->numeric()
                    ->minValue(1)
                    ->default($defaultSeats)
                    ->required(),
                Forms\Components\TextInput::make('school_name')
                    ->label('Nama Sekolah')
                    ->default(fn () => Auth::user()?->name)
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan untuk Admin / Tutor')
                    ->rows(2)
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('session.title')
                    ->label('Sesi & Server')
                    ->weight('bold')
                    ->description(function (TestScheduleRequest $record): string {
                        $s = $record->session;
                        if (!$s) return '-';
                        return $s->session_date?->format('d M Y') . ' ' . substr((string) $s->start_time, 0, 5) . ' • ' . $s->server_name . " (Sisa {$s->remainingCapacity()}/{$s->max_capacity})";
                    }),
                Tables\Columns\TextColumn::make('requested_seats')
                    ->label('Jumlah Peserta')
                    ->badge()
                    ->color('info')
                    ->suffix(' siswa'),
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
                        default => 'Menunggu Approval',
                    })
                    ->description(fn (TestScheduleRequest $record): ?string => $record->review_notes),
            ])
            ->actions([
                DeleteAction::make()
                    ->label('Batalkan')
                    ->visible(fn (TestScheduleRequest $record): bool => $record->status === 'pending'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSchoolTestScheduleRequests::route('/'),
        ];
    }
}
