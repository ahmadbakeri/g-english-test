<?php

namespace App\Filament\Resources\Academic;

use App\Filament\Resources\Academic\StudyMaterialResource\Pages;
use App\Models\StudyMaterial;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudyMaterialResource extends Resource
{
    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }

    protected static ?string $model = StudyMaterial::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-book-open';
    
    public static function getNavigationLabel(): string { return __('Study Materials'); }
    public static function getModelLabel(): string { return __('Study Material'); }
    public static function getPluralModelLabel(): string { return __('Study Materials'); }
    public static function getNavigationGroup(): ?string
    {
        return __('Academic');
    }
    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            \Filament\Schemas\Components\Section::make(__('Material Info'))->schema([
                Forms\Components\Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label(__('Subject')),
                Forms\Components\Select::make('type')
                    ->label(__('Type'))
                    ->options([
                        'video' => 'Video Pembelajaran',
                        'pdf' => 'Dokumen / PDF',
                        'mindmap' => 'Infografis / Mindmap',
                        'mixed' => 'Campuran (Semua Form)',
                    ])
                    ->default('mixed')
                    ->required()
                    ->live(),
                Forms\Components\TextInput::make('title')->label(__('Title'))->required(),
                Forms\Components\Textarea::make('description')->label(__('Description'))->rows(4),
                Forms\Components\TextInput::make('youtube_url')
                    ->label(__('YouTube Link'))
                    ->url()
                    ->placeholder('https://www.youtube.com/watch?v=...')
                    ->visible(fn ($get) => in_array($get('type'), ['video', 'mixed'])),
                Forms\Components\FileUpload::make('mindmap_image')
                    ->label(__('Mindmap Image'))
                    ->image()
                    ->directory('study-materials/images')
                    ->visible(fn ($get) => in_array($get('type'), ['mindmap', 'mixed'])),
                Forms\Components\FileUpload::make('attachment_file')
                    ->label(__('Attachment File (PDF, etc)'))
                    ->directory('study-materials/files')
                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip', 'application/x-rar-compressed'])
                    ->visible(fn ($get) => in_array($get('type'), ['pdf', 'mixed'])),
                Forms\Components\Toggle::make('is_published')->label(__('Published'))->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordAction('edit')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->weight('bold')
                    ->description(fn (StudyMaterial $record): string => $record->subject?->name ?? '-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('youtube_url')
                    ->label(__('Video'))
                    ->boolean()
                    ->getStateUsing(fn ($record) => !empty($record->youtube_url)),
                Tables\Columns\IconColumn::make('attachment_file')
                    ->label(__('Attachment'))
                    ->boolean()
                    ->getStateUsing(fn ($record) => !empty($record->attachment_file)),
                Tables\Columns\IconColumn::make('is_published')->label(__('Published'))->boolean(),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()->button()->size('sm'),
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudyMaterials::route('/'),
            'create' => Pages\CreateStudyMaterial::route('/create'),
            'edit' => Pages\EditStudyMaterial::route('/{record}/edit'),
        ];
    }
}
