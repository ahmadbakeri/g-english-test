<?php

namespace App\Filament\Resources\Academic\ClassSchedules;

use App\Filament\Resources\Academic\ClassSchedules\Pages\CreateClassSchedule;
use App\Filament\Resources\Academic\ClassSchedules\Pages\EditClassSchedule;
use App\Filament\Resources\Academic\ClassSchedules\Pages\ListClassSchedules;
use App\Filament\Resources\Academic\ClassSchedules\Schemas\ClassScheduleForm;
use App\Filament\Resources\Academic\ClassSchedules\Tables\ClassSchedulesTable;
use App\Models\ClassSchedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClassScheduleResource extends Resource
{
public static function getNavigationLabel(): string
    {
        return __('Master Schedules');
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    protected static ?string $model = ClassSchedule::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Master Data');
    }
    
    public static function getPluralModelLabel(): string { return __('Jadwal'); }
    
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Schema $schema): Schema
    {
        return ClassScheduleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassSchedulesTable::configure($table);
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
            'index' => ListClassSchedules::route('/'),
            'create' => CreateClassSchedule::route('/create'),
            'edit' => EditClassSchedule::route('/{record}/edit'),
        ];
    }
}
