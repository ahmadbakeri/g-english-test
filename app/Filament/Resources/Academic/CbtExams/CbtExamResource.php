<?php

namespace App\Filament\Resources\Academic\CbtExams;

use App\Filament\Resources\Academic\CbtExams\Pages\CreateCbtExam;
use App\Filament\Resources\Academic\CbtExams\Pages\EditCbtExam;
use App\Filament\Resources\Academic\CbtExams\Pages\ListCbtExams;
use App\Filament\Resources\Academic\CbtExams\Pages\ViewCbtExam;
use App\Filament\Resources\Academic\CbtExams\Schemas\CbtExamForm;
use App\Filament\Resources\Academic\CbtExams\Schemas\CbtExamInfolist;
use App\Filament\Resources\Academic\CbtExams\Tables\CbtExamsTable;
use App\Models\CbtExam;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CbtExamResource extends Resource
{
    protected static ?string $model = CbtExam::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string { return __('CBT Exams'); }
    public static function getModelLabel(): string { return __('CBT Exam'); }
    public static function getPluralModelLabel(): string { return __('CBT Exams'); }
    
    public static function getNavigationGroup(): ?string
    {
        return __('Exams & Evaluation');
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
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
            \App\Filament\Resources\Academic\CbtExams\RelationManagers\QuestionsRelationManager::class,
            \App\Filament\Resources\Academic\CbtExams\RelationManagers\AttemptsRelationManager::class,
        ];
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
