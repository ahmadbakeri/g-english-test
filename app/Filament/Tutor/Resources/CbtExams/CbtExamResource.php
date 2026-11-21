<?php

namespace App\Filament\Tutor\Resources\CbtExams;

use App\Filament\Tutor\Resources\CbtExams\Pages\CreateCbtExam;
use App\Filament\Tutor\Resources\CbtExams\Pages\EditCbtExam;
use App\Filament\Tutor\Resources\CbtExams\Pages\ListCbtExams;
use App\Filament\Tutor\Resources\CbtExams\Pages\ViewCbtExam;
use App\Filament\Tutor\Resources\CbtExams\Schemas\CbtExamForm;
use App\Filament\Tutor\Resources\CbtExams\Schemas\CbtExamInfolist;
use App\Filament\Tutor\Resources\CbtExams\Tables\CbtExamsTable;
use App\Models\CbtExam;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CbtExamResource extends Resource
{
    protected static ?string $model = CbtExam::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string { return __('Exam'); }
    public static function getModelLabel(): string { return __('Exam'); }
    public static function getPluralModelLabel(): string { return __('Exams'); }
    
    public static function getNavigationGroup(): ?string
    {
        return __('Evaluation');
    }

    public static function form(Schema $schema): Schema
    {
        return CbtExamForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CbtExamInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CbtExamsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Tutor\Resources\CbtExams\RelationManagers\QuestionsRelationManager::class,
            \App\Filament\Tutor\Resources\CbtExams\RelationManagers\AttemptsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        
        $tutorId = auth()->user()->tutor?->id;
        if ($tutorId) {
            $query->whereHas('classRoom.schedules', function ($q) use ($tutorId) {
                $q->where('tutor_id', $tutorId);
            });
        }
        
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCbtExams::route('/'),
            'create' => CreateCbtExam::route('/create'),
            'view' => ViewCbtExam::route('/{record}'),
            'edit' => EditCbtExam::route('/{record}/edit'),
        ];
    }
}
