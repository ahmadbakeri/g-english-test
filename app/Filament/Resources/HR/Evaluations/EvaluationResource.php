<?php

namespace App\Filament\Resources\HR\Evaluations;

use App\Filament\Resources\HR\Evaluations\Pages\CreateEvaluation;
use App\Filament\Resources\HR\Evaluations\Pages\EditEvaluation;
use App\Filament\Resources\HR\Evaluations\Pages\ListEvaluations;
use App\Filament\Resources\HR\Evaluations\Schemas\EvaluationForm;
use App\Filament\Resources\HR\Evaluations\Tables\EvaluationsTable;
use App\Models\Evaluation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EvaluationResource extends Resource
{
    public static function getModelLabel(): string
    {
        return __('Evaluation');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Evaluations');
    }

    public static function getNavigationLabel(): string
    {
        return __('Evaluations');
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    protected static ?string $model = Evaluation::class;
    public static function getNavigationGroup(): ?string
    {
        return __('Evaluation');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'date';

    public static function form(Schema $schema): Schema
    {
        return EvaluationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EvaluationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvaluations::route('/'),
            'create' => CreateEvaluation::route('/create'),
            'edit' => EditEvaluation::route('/{record}/edit'),
        ];
    }
}
