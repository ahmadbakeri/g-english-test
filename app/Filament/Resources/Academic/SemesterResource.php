<?php

namespace App\Filament\Resources\Academic;

use App\Filament\Resources\Academic\SemesterResource\Pages;
use App\Models\Semester;
use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SemesterResource extends Resource
{
    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }

    protected static ?string $model = Semester::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';
    public static function getNavigationLabel(): string { return __('Semesters'); }
    public static function getModelLabel(): string { return __('Semesters'); }
    public static function getPluralModelLabel(): string { return __('Semesters'); }
    public static function getNavigationGroup(): ?string
    {
        return __('Master Data');
    }
    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            \Filament\Schemas\Components\Section::make(__('Semester Data'))->schema([
                Forms\Components\Select::make('academic_year_id')->label(__('Academic Year'))
                    ->options(AcademicYear::pluck('name', 'id'))
                    ->searchable()->required(),
                Forms\Components\TextInput::make('name')->label(__('Semester Name (e.g., Semester 1)'))
                    ->required(),
                Forms\Components\DatePicker::make('start_date')->label(__('Start Date'))->required(),
                Forms\Components\DatePicker::make('end_date')->label(__('End Date'))->required(),
                Forms\Components\Toggle::make('is_active')->label(__('Active Semester'))->default(false),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordAction('edit')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Semester'))
                    ->weight('bold')
                    ->description(fn (Semester $record): string => $record->academicYear?->name ?? '-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('Period'))
                    ->formatStateUsing(fn (Semester $record): string => ($record->start_date?->format('d/m/Y') ?? '-') . ' - ' . ($record->end_date?->format('d/m/Y') ?? '-'))
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label(__('Active'))->boolean(),
                Tables\Columns\TextColumn::make('academicYear.name')->label(__('Academic Year'))->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('start_date', 'desc')
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
            'index' => Pages\ListSemesters::route('/'),
        ];
    }
}

