<?php

namespace App\Filament\Tutor\Resources\CbtExams\Pages;

use App\Filament\Tutor\Resources\CbtExams\CbtExamResource;
use App\Imports\CbtQuestionsExcelImport;
use App\Models\CbtExam;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class EditCbtExam extends EditRecord
{
    protected static string $resource = CbtExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync_gform')
                ->label('Sync Nilai GForm')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->visible(fn (): bool => ($this->record->cbt_mode ?? 'google_form') === 'google_form')
                ->action(function () {
                    Artisan::call('cbt:sync-gform', ['--exam_id' => $this->record->id]);
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
                ->visible(fn (): bool => $this->record->cbt_mode === 'internal_excel')
                ->action(function () {
                    return Excel::download(new \App\Exports\CbtQuestionTemplateExport, 'template_soal_cbt.xlsx');
                }),
            Action::make('import_excel')
                ->label('Import Soal Excel (.xlsx)')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->visible(fn (): bool => $this->record->cbt_mode === 'internal_excel')
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
                ->action(function (array $data) {
                    /** @var CbtExam $record */
                    $record = $this->record;
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
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
