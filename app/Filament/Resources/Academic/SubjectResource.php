<?php

namespace App\Filament\Resources\Academic;

use App\Filament\Resources\Academic\SubjectResource\Pages;
use App\Models\Subject;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubjectResource extends Resource
{
    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }

    protected static ?string $model = Subject::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bookmark';
    public static function getNavigationLabel(): string { return __('Subjects'); }
    public static function getModelLabel(): string { return __('Subjects'); }
    public static function getPluralModelLabel(): string { return __('Subjects'); }
    public static function getNavigationGroup(): ?string
    {
        return __('Master Data');
    }
    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            \Filament\Schemas\Components\Section::make(__('Subject Data'))->schema([
                Forms\Components\TextInput::make('code')->label(__('Code'))->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('name')->label(__('Name'))->translateLabel()->required(),
                Forms\Components\Select::make('level')->label(__('Level'))->options([
                    'Beginner' => 'Beginner',
                    'Intermediate' => 'Intermediate',
                    'Advanced' => 'Advanced',
                    'All Levels' => 'All Levels',
                ]),
                Forms\Components\Textarea::make('description')->label(__('Description'))->rows(3),
                Forms\Components\Toggle::make('is_active')->label(__('Active'))->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            \Filament\Tables\Columns\Layout\Split::make([
            Tables\Columns\TextColumn::make('code')->label(__('Code'))->searchable()->sortable(),
            Tables\Columns\TextColumn::make('name')->label(__('Name'))->translateLabel()->searchable()->sortable(),
            Tables\Columns\TextColumn::make('level')->label(__('Level'))->badge()->color('info'),
            Tables\Columns\TextColumn::make('description')->label(__('Description'))->limit(40)->toggleable(),
            Tables\Columns\IconColumn::make('is_active')->label(__('Active'))->boolean(),
            ])->from('md'),
        ])
        ->actions([\Filament\Actions\EditAction::make(), \Filament\Actions\DeleteAction::make()])
        ->bulkActions([\Filament\Actions\BulkActionGroup::make([\Filament\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjects::route('/'),
        ];
    }
}


