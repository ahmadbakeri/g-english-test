<?php

namespace App\Filament\Resources\Academic\CbtExams\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AttemptsRelationManager extends RelationManager
{
    protected static string $relationship = 'attempts';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Infolists\Components\Section::make('Informasi Ujian')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('student.name')->label('Siswa'),
                        \Filament\Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'in_progress' => 'warning',
                                'finished' => 'success',
                                default => 'gray',
                            }),
                        \Filament\Infolists\Components\TextEntry::make('violation_count')
                            ->label('Jumlah Pelanggaran Tab/Kamera')
                            ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                        \Filament\Infolists\Components\IconEntry::make('is_force_submitted')
                            ->boolean()
                            ->label('Disubmit Paksa (Kehabisan Waktu/Curang)'),
                    ])->columns(2),
                
                \Filament\Infolists\Components\Section::make('Riwayat Jawaban Siswa')
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('answers')
                            ->label('')
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('question.question_text')->label('Soal')->html(),
                                \Filament\Infolists\Components\TextEntry::make('selected_answer')->label('Jawaban Dipilih')
                                    ->color(fn ($record) => $record->is_correct ? 'success' : 'danger')
                                    ->weight('bold'),
                                \Filament\Infolists\Components\TextEntry::make('question.correct_answer')->label('Kunci Jawaban')
                                    ->color('success')->weight('bold'),
                                \Filament\Infolists\Components\IconEntry::make('is_correct')->label('Benar?')->boolean(),
                            ])->columns(4),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in_progress' => 'warning',
                        'finished' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('violation_count')
                    ->label('Violations')
                    ->sortable()
                    ->color(fn (string $state): string => $state > 0 ? 'danger' : 'gray'),
                Tables\Columns\IconColumn::make('is_force_submitted')
                    ->boolean()
                    ->label('Force Submit'),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Started At')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Finished At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\Action::make('export_excel')
                    ->label('Export Hasil (.xlsx)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function ($livewire) {
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\CbtExamResultsExport($livewire->getOwnerRecord()->id), 
                            'hasil_ujian_' . $livewire->getOwnerRecord()->id . '.xlsx'
                        );
                    }),
            ])
            ->recordActions([
                \Filament\Tables\Actions\Action::make('unlock')
                    ->label('Buka Kunci (Unlock)')
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
                            ->visible(fn (\Filament\Forms\Get $get) => $get('waktu_opsi') === 'tambah')
                            ->required(fn (\Filament\Forms\Get $get) => $get('waktu_opsi') === 'tambah'),
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
                            'end_time' => null,
                        ];

                        if ($data['waktu_opsi'] === 'reset') {
                            $updateData['start_time'] = now();
                        } elseif ($data['waktu_opsi'] === 'tambah') {
                            $exam = $record->exam;
                            $endTime = $record->start_time->copy()->addMinutes($exam->duration_minutes);
                            
                            if (now()->greaterThan($endTime)) {
                                // Time was up. Give them exactly 'tambahan' minutes from NOW.
                                $updateData['start_time'] = now()->addMinutes($data['tambahan_menit'])->subMinutes($exam->duration_minutes);
                            } else {
                                // Time was not up. Just add to the current remaining by shifting start_time forward
                                $updateData['start_time'] = $record->start_time->copy()->addMinutes($data['tambahan_menit']);
                            }
                        }

                        $record->update($updateData);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Ujian berhasil di-unlock.')
                            ->success()
                            ->send();
                    }),
                \Filament\Tables\Actions\ViewAction::make()->label('Detail'),
                \Filament\Tables\Actions\DeleteAction::make()->label('Reset/Delete Attempt'),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()->label('Reset/Delete Selected'),
                ]),
            ]);
    }
}
