<?php

namespace App\Filament\Resources\Academic\CbtExams\Pages;

use App\Filament\Resources\Academic\CbtExams\CbtExamResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCbtExams extends ListRecords
{
    protected static string $resource = CbtExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('download_template')
                ->label('Download Template Soal')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CbtQuestionTemplateExport, 'template_soal_cbt.xlsx');
                }),
            \Filament\Actions\Action::make('quick_upload')
                ->label('Quick Upload (Excel)')
                ->icon('heroicon-o-bolt')
                ->color('warning')
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
                        ->directory('temp_uploads')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'])
                        ->required(),
                ])
                ->action(function (array $data) {
                    $exam = \App\Models\CbtExam::create([
                        'title' => $data['title'],
                        'class_id' => $data['class_id'],
                        'subject_id' => $data['subject_id'],
                        'duration_minutes' => $data['duration_minutes'],
                    ]);

                    $filePath = storage_path('app/' . $data['excel_file']);
                    
                    $rows = \Maatwebsite\Excel\Facades\Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
                        public function array(array $array) {}
                    }, $filePath);
                    
                    $count = 0;
                    if (!empty($rows[0])) {
                        for ($i = 1; $i < count($rows[0]); $i++) {
                            $row = $rows[0][$i];
                            if (empty($row[0])) continue; // skip empty questions
                            
                            \App\Models\CbtQuestion::create([
                                'cbt_exam_id' => $exam->id,
                                'question_text' => (string)($row[0] ?? ''),
                                'option_a' => (string)($row[1] ?? ''),
                                'option_b' => (string)($row[2] ?? ''),
                                'option_c' => (string)($row[3] ?? ''),
                                'option_d' => (string)($row[4] ?? ''),
                                'option_e' => isset($row[5]) ? (string)$row[5] : null,
                                'correct_answer' => strtoupper((string)($row[6] ?? 'A')),
                                'points' => (int)($row[7] ?? 1),
                            ]);
                            $count++;
                        }
                    }

                    \Filament\Notifications\Notification::make()
                        ->title("Ujian berhasil dibuat dengan $count soal!")
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
