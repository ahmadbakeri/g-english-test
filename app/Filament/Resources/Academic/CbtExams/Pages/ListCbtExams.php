<?php

namespace App\Filament\Resources\Academic\CbtExams\Pages;

use App\Filament\Resources\Academic\CbtExams\CbtExamResource;
use App\Imports\CbtQuestionsExcelImport;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class ListCbtExams extends ListRecords
{
    protected static string $resource = CbtExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('download_template')
                ->label('Download Template Excel (.xlsx)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return Excel::download(new \App\Exports\CbtQuestionTemplateExport, 'template_soal_cbt.xlsx');
                }),
            \Filament\Actions\Action::make('quick_upload')
                ->label('Import Soal Excel (.xlsx)')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->form([
                    \Filament\Forms\Components\TextInput::make('title')->required(),
                    \Filament\Forms\Components\Select::make('class_id')
                        ->relationship('classRoom', 'name')->required(),
                    \Filament\Forms\Components\Select::make('subject_id')
                        ->relationship('subject', 'name')->required(),
                    \Filament\Forms\Components\TextInput::make('duration_minutes')->numeric()->default(120)->required(),
                    \Filament\Forms\Components\FileUpload::make('excel_file')
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
                    $exam = \App\Models\CbtExam::create([
                        'title' => $data['title'],
                        'class_id' => $data['class_id'],
                        'subject_id' => $data['subject_id'],
                        'cbt_mode' => 'internal_excel',
                        'duration_minutes' => $data['duration_minutes'],
                        'imported_excel_path' => $data['excel_file'],
                    ]);

                    $filePath = storage_path('app/' . $data['excel_file']);
                    if (!file_exists($filePath)) {
                        $filePath = \Illuminate\Support\Facades\Storage::disk('local')->path($data['excel_file']);
                    }

                    $importer = new CbtQuestionsExcelImport($exam->id);
                    Excel::import($importer, $filePath);

                    Cache::forget("exam_q_{$exam->id}");

                    \Filament\Notifications\Notification::make()
                        ->title("Ujian berhasil dibuat dengan {$importer->importedCount} soal!")
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
