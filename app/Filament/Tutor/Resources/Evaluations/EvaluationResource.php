<?php

namespace App\Filament\Tutor\Resources\Evaluations;

use App\Filament\Tutor\Resources\Evaluations\Pages\CreateEvaluation;
use App\Filament\Tutor\Resources\Evaluations\Pages\EditEvaluation;
use App\Filament\Tutor\Resources\Evaluations\Pages\ListEvaluations;
use App\Filament\Tutor\Resources\Evaluations\Schemas\EvaluationForm;
use App\Filament\Tutor\Resources\Evaluations\Tables\EvaluationsTable;
use App\Models\Evaluation;
use App\Models\ClassRoom;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EvaluationResource extends Resource
{
    public static function getNavigationLabel(): string
    {
        return __('Evaluations');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Evaluation');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Evaluations');
    }

    protected static ?string $model = Evaluation::class;

    public static function getEloquentQuery(): Builder
    {
        $tutor = auth()->user()->tutor;
        if (!$tutor) {
            return parent::getEloquentQuery()->whereRaw('1=0');
        }

        return parent::getEloquentQuery()
            ->with(['student', 'subject'])
            ->whereIn('subject_id', ClassRoom::where('tutor_id', $tutor->id)->pluck('subject_id'))
            ->whereHas('student', function (Builder $query) use ($tutor) {
                $query->whereHas('classes', function (Builder $query) use ($tutor) {
                    $query->where('tutor_id', $tutor->id);
                });
            });
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

    
    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return true;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return true;
    }
}
