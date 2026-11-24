<?php

namespace App\Filament\Resources\Academic\CbtExams\RelationManagers;

use App\Imports\CbtQuestionsExcelImport;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return ($ownerRecord->cbt_mode ?? 'google_form') === 'internal_excel';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('question_type')
                    ->label('Tipe Soal')
                    ->options([
                        'mcq' => 'Pilihan Ganda (A–E)',
                        'complex_tf' => 'PG Kompleks (Tabel Benar/Salah)',
                        'short_answer' => 'Isian Singkat (Numerik/Teks)',
                    ])
                    ->default('mcq')
                    ->required()
                    ->live(),
                TextInput::make('order_number')
                    ->label('Nomor Urut')
                    ->numeric()
                    ->default(0),
                RichEditor::make('question_text')
                    ->label('Isi Soal (Mendukung HTML & LaTeX \(...\))')
                    ->required()
                    ->toolbarButtons([
                        'attachFiles',
                        'blockquote',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'h2',
                        'h3',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                    ])
                    ->columnSpanFull(),
                FileUpload::make('attachment')
                    ->label('Lampiran Tambahan (Gambar/Audio)')
                    ->acceptedFileTypes(['image/*', 'audio/*'])
                    ->directory('cbt_questions')
                    ->columnSpanFull(),
                Textarea::make('option_a')
                    ->label('Opsi A')
                    ->visible(fn ($get) => ($get('question_type') ?? 'mcq') === 'mcq')
                    ->required(fn ($get) => ($get('question_type') ?? 'mcq') === 'mcq')
                    ->columnSpanFull(),
                Textarea::make('option_b')
                    ->label('Opsi B')
                    ->visible(fn ($get) => ($get('question_type') ?? 'mcq') === 'mcq')
                    ->required(fn ($get) => ($get('question_type') ?? 'mcq') === 'mcq')
                    ->columnSpanFull(),
                Textarea::make('option_c')
                    ->label('Opsi C')
                    ->visible(fn ($get) => ($get('question_type') ?? 'mcq') === 'mcq')
                    ->columnSpanFull(),
                Textarea::make('option_d')
                    ->label('Opsi D')
                    ->visible(fn ($get) => ($get('question_type') ?? 'mcq') === 'mcq')
                    ->columnSpanFull(),
                Textarea::make('option_e')
                    ->label('Opsi E')
                    ->visible(fn ($get) => ($get('question_type') ?? 'mcq') === 'mcq')
                    ->columnSpanFull(),
                \Filament\Forms\Components\Repeater::make('statements')
                    ->label('Daftar Pernyataan (Tabel Benar / Salah)')
                    ->visible(fn ($get) => $get('question_type') === 'complex_tf')
                    ->schema([
                        TextInput::make('no')->label('No')->numeric()->default(1)->required(),
                        Textarea::make('text')->label('Pernyataan')->rows(2)->required()->columnSpan(2),
                        Select::make('answer')
                            ->label('Kunci (Opsional)')
                            ->options(['B' => 'Benar', 'S' => 'Salah'])
                            ->placeholder('Kosongkan jika proses Admin/IRT'),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
                TextInput::make('correct_answer')
                    ->label('Kunci Jawaban (Opsional — kosongkan jika penilaian IRT/Admin)')
                    ->placeholder('A/B/C/D/E atau angka untuk Isian Singkat')
                    ->maxLength(255),
                TextInput::make('points')
                    ->label('Poin')
                    ->required()
                    ->numeric()
                    ->default(1),
                Textarea::make('explanation')
                    ->label('Pembahasan (Opsional)')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('question_text')
                    ->columnSpanFull(),
                TextEntry::make('attachment')
                    ->label('Status Lampiran')
                    ->formatStateUsing(fn ($state) => $state ? 'Ada Lampiran' : 'Tidak Ada')
                    ->columnSpanFull(),
                TextEntry::make('option_a')
                    ->columnSpanFull(),
                TextEntry::make('option_b')
                    ->columnSpanFull(),
                TextEntry::make('option_c')
                    ->columnSpanFull(),
                TextEntry::make('option_d')
                    ->columnSpanFull(),
                TextEntry::make('option_e')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('correct_answer'),
                TextEntry::make('points')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question_text')
            ->columns([
                TextColumn::make('order_number')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('question_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'complex_tf' => 'warning',
                        'short_answer' => 'info',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'complex_tf' => 'Benar/Salah',
                        'short_answer' => 'Isian',
                        default => 'PG A–E',
                    }),
                TextColumn::make('question_text')
                    ->label('Soal')
                    ->html()
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('correct_answer')
                    ->label('Kunci')
                    ->badge()
                    ->placeholder('Proses Admin')
                    ->color(fn (?string $state): string => filled($state) ? 'success' : 'gray')
                    ->searchable(),
                TextColumn::make('points')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\Action::make('download_docx_template')
                    ->label('Template Word (.docx)')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(function () {
                        $path = (new \App\Services\CbtDocx\DocxTemplateGenerator())->generateTempFile();

                        return response()->download($path, 'template_soal_utbk_cbt.docx')->deleteFileAfterSend(true);
                    }),
                \Filament\Actions\Action::make('import_docx')
                    ->label('Import Word (.docx) [Staging]')
                    ->icon('heroicon-o-document-plus')
                    ->color('warning')
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('docx_file')
                            ->label('Upload File Soal Word (.docx)')
                            ->helperText('Masuk ke Staging & Visual Preview dulu agar rumus KaTeX, tabel, dan gambar bisa dicek sebelum publish.')
                            ->disk('local')
                            ->directory('cbt_docx_imports')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ])
                            ->maxSize(20480)
                            ->required(),
                    ])
                    ->action(function (array $data, RelationManager $livewire) {
                        $exam = $livewire->getOwnerRecord();
                        $stored = $data['docx_file'];

                        $batch = (new \App\Services\CbtDocx\DocxImportService())->import(
                            storedLocalPath: $stored,
                            originalFilename: basename($stored),
                            cbtExamId: $exam->id,
                            uploadedBy: auth()->id(),
                        );

                        \Filament\Notifications\Notification::make()
                            ->title("Draft berhasil diparse: {$batch->total_questions} soal")
                            ->body("Valid: {$batch->valid_count}, Warning: {$batch->warning_count}, Error: {$batch->error_count}.")
                            ->success()
                            ->send();

                        return redirect(\App\Filament\Resources\Academic\CbtImportBatches\CbtImportBatchResource::getUrl('review', ['record' => $batch]));
                    }),
                \Filament\Actions\Action::make('download_template')
                    ->label('Download Template Excel (.xlsx)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        return Excel::download(new \App\Exports\CbtQuestionTemplateExport, 'template_soal_cbt.xlsx');
                    }),
                \Filament\Actions\Action::make('import_excel')
                    ->label('Import Soal Excel (.xlsx)')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('primary')
                    ->form([
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
                    ->action(function (array $data, RelationManager $livewire) {
                        $exam = $livewire->getOwnerRecord();
                        $filePath = storage_path('app/' . $data['excel_file']);
                        if (!file_exists($filePath)) {
                            $filePath = \Illuminate\Support\Facades\Storage::disk('local')->path($data['excel_file']);
                        }

                        $importer = new CbtQuestionsExcelImport($exam->id);
                        Excel::import($importer, $filePath);

                        $exam->update(['imported_excel_path' => $data['excel_file']]);
                        Cache::forget("exam_q_{$exam->id}");
                        Cache::forget("exam_{$exam->id}_question_ids");

                        \Filament\Notifications\Notification::make()
                            ->title("Berhasil mengimpor {$importer->importedCount} soal!")
                            ->success()
                            ->send();
                    }),
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
