<?php

namespace App\Filament\Resources\Academic\CbtExams\RelationManagers;

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
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

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
                TextColumn::make('correct_answer')
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
                    ->label('Download Template Soal')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CbtQuestionTemplateExport, 'template_soal_cbt.xlsx');
                    }),
                \Filament\Actions\Action::make('import_excel')
                    ->label('Upload Soal (Excel)')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('warning')
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('excel_file')
                            ->label('Upload File Soal (.xlsx)')
                            ->disk('local')
                            ->directory('temp_uploads')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'])
                            ->required(),
                    ])
                    ->action(function (array $data, RelationManager $livewire) {
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
                                    'cbt_exam_id' => $livewire->getOwnerRecord()->id,
                                    'question_text' => (string)($row[0] ?? ''),
                                    'attachment' => !empty($row[1]) ? (string)$row[1] : null,
                                    'option_a' => (string)($row[2] ?? ''),
                                    'option_b' => (string)($row[3] ?? ''),
                                    'option_c' => (string)($row[4] ?? ''),
                                    'option_d' => (string)($row[5] ?? ''),
                                    'option_e' => isset($row[6]) ? (string)$row[6] : null,
                                    'correct_answer' => strtoupper((string)($row[7] ?? 'A')),
                                    'points' => (int)($row[8] ?? 1),
                                ]);
                                $count++;
                            }
                        }

                        \Filament\Notifications\Notification::make()
                            ->title("Berhasil mengimpor $count soal!")
                            ->success()
                            ->send();
                    }),
                \Filament\Actions\CreateAction::make(),
                \Filament\Actions\AssociateAction::make(),
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
