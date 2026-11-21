<?php

namespace App\Filament\Resources\Evaluation;

use App\Filament\Resources\Evaluation\Pages\ManageCbtAttempts;
use App\Models\CbtAttempt;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CbtAttemptResource extends Resource
{
    protected static ?string $model = CbtAttempt::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';
    
    protected static ?string $modelLabel = 'Monitoring';
    
    protected static ?string $pluralModelLabel = 'Monitoring Ujian';
    
    public static function getNavigationGroup(): ?string
    {
        return __('Evaluation');
    }

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('cbt_exam_id')
                    ->required()
                    ->numeric(),
                TextInput::make('student_id')
                    ->required()
                    ->numeric(),
                TextInput::make('attempt_number')
                    ->required()
                    ->numeric()
                    ->default(1),
                DateTimePicker::make('start_time')
                    ->required(),
                DateTimePicker::make('end_time'),
                Select::make('status')
                    ->options(['in_progress' => 'In progress', 'finished' => 'Finished', 'locked' => 'Locked'])
                    ->default('in_progress')
                    ->required(),
                TextInput::make('score')
                    ->numeric(),
                Toggle::make('is_completed')
                    ->required(),
                TextInput::make('violation_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('max_violations')
                    ->required()
                    ->numeric()
                    ->default(3),
                TextInput::make('question_order'),
                Toggle::make('is_force_submitted')
                    ->required(),
                DateTimePicker::make('last_auto_save_at'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('cbt_exam_id')
                    ->numeric(),
                TextEntry::make('student_id')
                    ->numeric(),
                TextEntry::make('attempt_number')
                    ->numeric(),
                TextEntry::make('start_time')
                    ->dateTime(),
                TextEntry::make('end_time')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('score')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_completed')
                    ->boolean(),
                TextEntry::make('violation_count')
                    ->numeric(),
                TextEntry::make('max_violations')
                    ->numeric(),
                IconEntry::make('is_force_submitted')
                    ->boolean(),
                TextEntry::make('last_auto_save_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('exam.title')
                    ->label('Ujian')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in_progress' => 'warning',
                        'finished' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('score')
                    ->label('Skor')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_completed')
                    ->label('Selesai?')
                    ->boolean(),
                TextColumn::make('violation_count')
                    ->label('Pelanggaran')
                    ->numeric()
                    ->sortable()
                    ->color(fn (string $state): string => $state > 0 ? 'danger' : 'gray'),
                IconColumn::make('is_force_submitted')
                    ->label('Disubmit Paksa')
                    ->boolean(),
                TextColumn::make('start_time')
                    ->label('Waktu Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label('Waktu Selesai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('cbt_exam_id')
                    ->relationship('exam', 'title')
                    ->label('Pilih Ujian')
                    ->searchable()
                    ->preload(),
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'in_progress' => 'Sedang Mengerjakan',
                        'finished' => 'Selesai',
                        'locked' => 'Terkunci',
                    ])
                    ->label('Status'),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('unlock')
                    ->label('Buka Kunci')
                    ->icon('heroicon-o-lock-open')
                    ->color('warning')
                    ->form([
                        \Filament\Forms\Components\Radio::make('waktu_opsi')
                            ->label('Pengaturan Waktu')
                            ->options([
                                'lanjut' => 'Lanjutkan Waktu Tersisa',
                                'reset' => 'Reset Waktu (Mulai dari Awal)',
                                'tambah' => 'Tambah Ekstra Waktu',
                            ])
                            ->default('lanjut')
                            ->reactive(),
                        \Filament\Forms\Components\TextInput::make('tambahan_menit')
                            ->label('Tambahan Waktu (Menit)')
                            ->numeric()
                            ->visible(fn ($get) => $get('waktu_opsi') === 'tambah')
                            ->required(fn ($get) => $get('waktu_opsi') === 'tambah'),
                    ])
                    ->modalHeading('Buka Kunci Percobaan')
                    ->modalDescription('Ujian siswa akan dibuka kuncinya. Pilih bagaimana Anda ingin mengatur sisa waktu siswa.')
                    ->visible(fn (\App\Models\CbtAttempt $record): bool => true)
                    ->action(function (\App\Models\CbtAttempt $record, array $data) {
                        $updateData = [
                            'is_completed' => false,
                            'is_force_submitted' => false,
                            'status' => 'in_progress',
                            'attempt_number' => 1,
                            'violation_count' => 0,
                            'score' => null,
                        ];

                        if ($data['waktu_opsi'] === 'reset') {
                            $updateData['start_time'] = now();
                            $updateData['end_time'] = now()->addMinutes($record->exam->duration_minutes);
                        } elseif ($data['waktu_opsi'] === 'tambah') {
                            if (now()->greaterThan($record->end_time)) {
                                $updateData['end_time'] = now()->addMinutes($data['tambahan_menit']);
                            } else {
                                $updateData['end_time'] = $record->end_time->copy()->addMinutes($data['tambahan_menit']);
                            }
                        }

                        $record->update($updateData);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Ujian berhasil di-unlock.')
                            ->success()
                            ->send();
                    }),
                DeleteAction::make()->label('Reset/Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCbtAttempts::route('/'),
        ];
    }
}
