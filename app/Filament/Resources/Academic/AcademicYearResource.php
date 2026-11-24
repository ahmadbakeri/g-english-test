<?php

namespace App\Filament\Resources\Academic;

use App\Filament\Resources\Academic\AcademicYearResource\Pages;
use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AcademicYearResource extends Resource
{
    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }

    protected static ?string $model = AcademicYear::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar';
    public static function getNavigationLabel(): string { return __('Academic Years'); }
    public static function getModelLabel(): string { return __('Academic Years'); }
    public static function getPluralModelLabel(): string { return __('Academic Years'); }
    public static function getNavigationGroup(): ?string
    {
        return __('Master Data');
    }
    public static function getNavigationSort(): ?int
    {
        return 6;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            \Filament\Schemas\Components\Section::make(__('Academic Year Data'))->schema([
                Forms\Components\TextInput::make('name')->label(__('Name'))->translateLabel()->required()->unique(ignoreRecord: true),
                Forms\Components\Toggle::make('is_active')->label(__('Active Academic Year'))->default(false),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordAction('edit')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('Academic Year'))->weight('bold')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label(__('Active'))->boolean(),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()->button()->size('sm')->slideOver(),
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([\Filament\Actions\BulkActionGroup::make([\Filament\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcademicYears::route('/'),
        ];
    }
}


