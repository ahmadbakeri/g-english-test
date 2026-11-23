<?php

namespace App\Filament\Resources\Academic\CbtExams\Tables;

use App\Imports\CbtQuestionsExcelImport;
use App\Models\CbtExam;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class CbtExamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordAction('edit')
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('title')
                    ->label('Judul Ujian')
                    ->weight('bold')
                    ->color('primary')
                    ->description(fn (CbtExam $record): string => ($record->classRoom?->name ?? '-') . ' • ' . ($record->subject?->name ?? '-'))
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('cbt_mode')
                    ->label('Mode')
                    ->badge()
                    ->color(fn (?string $state): string => $state === 'internal_excel' ? 'primary' : 'info')
                    ->formatStateUsing(fn (?string $state): string => $state === 'internal_excel' ? 'Real CBT' : 'Google Form'),
                \Filament\Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Durasi')
                    ->numeric()
                    ->suffix(' Min')
                    ->alignEnd()
                    ->sortable(),
                \Filament\Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
                \Filament\Tables\Columns\TextColumn::make('classRoom.name')
                    ->label('Kelas')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('last_synced_at')
                    ->label('Last Synced')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('cbt_mode')
                    ->label('Mode CBT')
                    ->options([
                        'google_form' => 'Google Form',
                        'internal_excel' => 'Real CBT (Excel)',
                    ]),
            ])
            ->recordActions([
                EditAction::make()->button()->size('sm'),
                ActionGroup::make([
                    ViewAction::make(),
                    Action::make('sync_gform')
                        ->label('Sync Nilai GForm')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->visible(fn (CbtExam $record): bool => ($record->cbt_mode ?? 'google_form') === 'google_form')
                        ->action(function (CbtExam $record) {
                            Artisan::call('cbt:sync-gform', ['--exam_id' => $record->id]);
                            Notification::make()
                                ->title('Sinkronisasi nilai Google Form selesai.')
                                ->body(trim(Artisan::output()))
                                ->success()
                                ->send();
                        }),
                    Action::make('download_template')
                        ->label('Download Template Excel (.xlsx)')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->visible(fn (CbtExam $record): bool => $record->cbt_mode === 'internal_excel')
                        ->action(function () {
                            return Excel::download(new \App\Exports\CbtQuestionTemplateExport, 'template_soal_cbt.xlsx');
                        }),
                    Action::make('import_excel')
                        ->label('Import Soal Excel (.xlsx)')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->color('primary')
                        ->visible(fn (CbtExam $record): bool => $record->cbt_mode === 'internal_excel')
                        ->form([
                            FileUpload::make('excel_file')
                                ->label('Upload File Soal (.xlsx)')
                                ->disk('local')
                                ->directory('cbt_imports')
                                ->acceptedFileTypes([
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    'application/vnd.ms-excel',
                                    'text/csv',
                                ])
                                ->required(),
                        ])
                        ->action(function (array $data, CbtExam $record) {
                            $filePath = storage_path('app/' . $data['excel_file']);
                            if (!file_exists($filePath)) {
                                $filePath = \Illuminate\Support\Facades\Storage::disk('local')->path($data['excel_file']);
                            }

                            $importer = new CbtQuestionsExcelImport($record->id);
                            Excel::import($importer, $filePath);

                            $record->update(['imported_excel_path' => $data['excel_file']]);
                            Cache::forget("exam_q_{$record->id}");
                            Cache::forget("exam_{$record->id}_question_ids");

                            Notification::make()
                                ->title("Berhasil mengimpor {$importer->importedCount} soal!")
                                ->success()
                                ->send();
                        }),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray')
                    ->tooltip('Aksi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
