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
                RichEditor::make('question_text')
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
                    ->label('Lampiran (Gambar/Audio)')
                    ->acceptedFileTypes(['image/*', 'audio/*'])
                    ->directory('cbt_questions')
                    ->columnSpanFull(),
                Textarea::make('option_a')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('option_b')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('option_c')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('option_d')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('option_e')
                    ->columnSpanFull(),
                Select::make('correct_answer')
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'C' => 'C',
                        'D' => 'D',
                        'E' => 'E',
                    ])
                    ->required(),
                TextInput::make('points')
                    ->required()
                    ->numeric()
                    ->default(1),
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
                TextColumn::make('question_text')
                    ->html()
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('correct_answer')
                    ->badge()
                    ->color('success')
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
