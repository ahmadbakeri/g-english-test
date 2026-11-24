<?php

namespace App\Filament\Resources\Academic\CbtImportBatches\Pages;

use App\Filament\Resources\Academic\CbtImportBatches\CbtImportBatchResource;
use App\Models\CbtExam;
use App\Services\CbtDocx\DocxImportService;
use App\Services\CbtDocx\DocxTemplateGenerator;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListCbtImportBatches extends ListRecords
{
    protected static string $resource = CbtImportBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_docx_template')
                ->label('Download Template Word (.docx)')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->action(function () {
                    $path = (new DocxTemplateGenerator())->generateTempFile();

                    return response()->download($path, 'template_soal_utbk_cbt.docx')->deleteFileAfterSend(true);
                }),
            Action::make('upload_docx')
                ->label('Upload File Word (.docx) Baru')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->modalHeading('Upload Soal Word (.docx) ke Staging Preview')
                ->modalDescription('Mendukung format penomoran otomatis Word Genza (1., 2., ... + A.–E., tabel Benar/Salah, rumus Equation) maupun format tag [SOAL]/[A]..[E]. Soal masuk ke Staging dulu untuk dicek visual sebelum dipublish.')
                ->form([
                    Select::make('cbt_exam_id')
                        ->label('Target Ujian CBT (Opsional — bisa dipilih nanti saat publish)')
                        ->options(fn () => CbtExam::where('cbt_mode', 'internal_excel')->orderByDesc('id')->pluck('title', 'id'))
                        ->searchable()
                        ->preload()
                        ->placeholder('Pilih ujian CBT target...'),
                    TextInput::make('title')
                        ->label('Judul Paket / Catatan Batch (Opsional)')
                        ->placeholder('Otomatis diambil dari judul dokumen Word jika dikosongkan')
                        ->maxLength(220),
                    FileUpload::make('docx_file')
                        ->label('File Microsoft Word (.docx)')
                        ->disk('local')
                        ->directory('cbt_docx_imports')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ])
                        ->maxSize(20480)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $stored = $data['docx_file'];
                    $batch = (new DocxImportService())->import(
                        storedLocalPath: $stored,
                        originalFilename: basename($stored),
                        cbtExamId: !empty($data['cbt_exam_id']) ? (int) $data['cbt_exam_id'] : null,
                        uploadedBy: auth()->id(),
                        customTitle: filled($data['title'] ?? null) ? trim($data['title']) : null,
                    );

                    Notification::make()
                        ->title("Berhasil memparse {$batch->total_questions} soal ke Staging!")
                        ->body("Siap: {$batch->valid_count} | Perlu Cek: {$batch->warning_count} | Error: {$batch->error_count}")
                        ->success()
                        ->send();

                    return redirect(CbtImportBatchResource::getUrl('review', ['record' => $batch]));
                }),
        ];
    }
}
