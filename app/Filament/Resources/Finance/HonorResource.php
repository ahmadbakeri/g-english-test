<?php

namespace App\Filament\Resources\Finance;

use App\Filament\Resources\Finance\HonorResource\Pages;
use App\Models\Honor;
use App\Models\Student;
use App\Models\Journal;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HonorResource extends Resource
{
    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }

    protected static ?string $model = Honor::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-trophy';
    public static function getNavigationLabel(): string { return __('Honors'); }
    public static function getModelLabel(): string { return __('Honors'); }
    public static function getPluralModelLabel(): string { return __('Honors'); }
    public static function getNavigationGroup(): ?string
    {
        return __('Evaluation');
    }
    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            \Filament\Schemas\Components\Section::make(__('Honor Data'))->schema([
                Forms\Components\Select::make('student_id')->label(__('Student'))
                    ->options(Student::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()->required(),
                Forms\Components\Select::make('journal_id')->label(__('Related Journal'))
                    ->options(Journal::where('status', 'validated')->pluck('topic', 'id'))
                    ->searchable()->nullable(),
                Forms\Components\Select::make('type')->label(__('Type'))->translateLabel()
                    ->options(['achievement' => '🏆 Achievement', 'certificate' => '📜 Certificate', 'award' => '🥇 Award'])
                    ->required()->default('achievement'),
                Forms\Components\TextInput::make('title')->label(__('Title'))->required(),
                Forms\Components\Textarea::make('description')->label(__('Description'))->rows(3)->columnSpanFull(),
                Forms\Components\DatePicker::make('date')->label(__('Date'))->translateLabel()->required()->default(now()),
                Forms\Components\TextInput::make('certificate_number')->label(__('Certificate No.')),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->recordAction('edit')
        ->columns([
            Tables\Columns\TextColumn::make('date')->label(__('Date'))->translateLabel()->date('d/m/Y')->sortable(),
            Tables\Columns\TextColumn::make('student.name')->label(__('Student'))->weight('bold')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('title')
                ->label(__('Title'))
                ->description(fn (Honor $record): ?string => $record->certificate_number ? 'No: ' . $record->certificate_number : null)
                ->searchable(),
            Tables\Columns\TextColumn::make('type')->label(__('Type'))->translateLabel()->badge()
                ->color(fn (string $state): string => match ($state) {
                    'achievement' => 'success', 'certificate' => 'info', 'award' => 'warning', default => 'gray',
                }),
            Tables\Columns\TextColumn::make('certificate_number')->label(__('Certificate No.'))->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('journal.topic')->label(__('Journal'))->limit(25)->toggleable(isToggledHiddenByDefault: true),
        ])
        ->defaultSort('date', 'desc')
        ->actions([
            \Filament\Actions\EditAction::make()->button()->size('sm'),
            \Filament\Actions\ActionGroup::make([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ]),
        ])
        ->bulkActions([\Filament\Actions\BulkActionGroup::make([\Filament\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHonors::route('/'),
        ];
    }
}

