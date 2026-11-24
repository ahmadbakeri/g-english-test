<?php

namespace App\Filament\Resources\Evaluation;

use App\Filament\Resources\Evaluation\TestSessionResource\Pages;
use App\Models\CbtExam;
use App\Models\Test;
use App\Models\TestSession;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TestSessionResource extends Resource
{
    protected static ?string $model = TestSession::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-server-stack';
    protected static ?string $slug = 'evaluation/test-sessions';

    public static function getNavigationLabel(): string { return __('Test Sessions & Server'); }
    public static function getModelLabel(): string { return __('Test Session'); }
    public static function getPluralModelLabel(): string { return __('Test Sessions & Server'); }
    public static function getNavigationGroup(): ?string { return __('Exams & Evaluation'); }
    public static function getNavigationSort(): ?int { return 3; }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make(__('Server Capacity & Session Schedule'))->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Nama Sesi Tes')
                    ->placeholder('Contoh: Sesi 1 Pagi — Brain Champion SD')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Select::make('cbt_exam_id')
                    ->label('Ujian CBT (Opsional)')
                    ->options(CbtExam::pluck('title', 'id'))
                    ->searchable()
                    ->nullable(),
                Forms\Components\Select::make('test_id')
                    ->label('Tes Akademik (Opsional)')
                    ->options(Test::pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
                Forms\Components\DatePicker::make('session_date')
                    ->label('Tanggal Sesi')
                    ->default(now())
                    ->required(),
                Forms\Components\TextInput::make('server_name')
                    ->label('Nama Server / Ruang')
                    ->default('Server Utama')
                    ->required(),
                Forms\Components\TimePicker::make('start_time')
                    ->label('Jam Mulai')
                    ->default('08:00')
                    ->required(),
                Forms\Components\TimePicker::make('end_time')
                    ->label('Jam Selesai')
                    ->default('10:00')
                    ->required(),
                Forms\Components\TextInput::make('max_capacity')
                    ->label('Kapasitas Maksimal Server (Peserta/Sesi)')
                    ->numeric()
                    ->minValue(1)
                    ->default(35)
                    ->suffix('peserta')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Sesi Dibuka untuk Request')
                    ->default(true),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan Sesi')
                    ->rows(2)
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('session_date', 'asc')
            ->recordAction('edit')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Sesi & Server')
                    ->weight('bold')
                    ->description(fn (TestSession $record): string => $record->server_name . ' • ' . ($record->cbtExam?->title ?: ($record->test?->name ?: 'Umum')))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('session_date')
                    ->label('Waktu Sesi')
                    ->date('d M Y')
                    ->description(fn (TestSession $record): string => substr((string) $record->start_time, 0, 5) . ' - ' . substr((string) $record->end_time, 0, 5))
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_capacity')
                    ->label('Kapasitas Server')
                    ->badge()
                    ->getStateUsing(function (TestSession $record): string {
                        $used = $record->approvedSeats();
                        return "Terisi {$used} / {$record->max_capacity}";
                    })
                    ->description(function (TestSession $record): string {
                        $rem = $record->remainingCapacity();
                        $pending = $record->pendingSeats();
                        return "Sisa: {$rem} kursi • Pending: {$pending}";
                    })
                    ->color(function (TestSession $record): string {
                        $rem = $record->remainingCapacity();
                        if ($rem <= 0) return 'danger';
                        if ($rem <= 5) return 'warning';
                        return 'success';
                    }),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Dibuka'),
            ])
            ->filters([
                Tables\Filters\Filter::make('date_range')
                    ->label('Rentang Tanggal Sesi')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->columns(2)
                    ->query(fn ($query, array $data) => $query
                        ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('session_date', '>=', $d))
                        ->when($data['until'] ?? null, fn ($q, $d) => $q->whereDate('session_date', '<=', $d))),
            ])
            ->actions([
                EditAction::make()->button()->size('sm')->slideOver()->modalWidth('2xl'),
                ActionGroup::make([
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTestSessions::route('/'),
        ];
    }
}
