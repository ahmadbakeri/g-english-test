<?php

namespace App\Filament\Resources\Operations\Schedules;

use App\Filament\Resources\Operations\Schedules\Pages\CreateSchedule;
use App\Filament\Resources\Operations\Schedules\Pages\EditSchedule;
use App\Filament\Resources\Operations\Schedules\Pages\ListSchedules;
use App\Filament\Resources\Operations\Schedules\Schemas\ScheduleForm;
use App\Filament\Resources\Operations\Schedules\Tables\SchedulesTable;
use App\Models\Schedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ScheduleResource extends Resource
{
public static function getNavigationSort(): ?int
    {
        return 3;
    }

    protected static ?string $model = Schedule::class;
    public static function getNavigationGroup(): ?string
    {
        return __('Academic');
    }
    
    public static function getPluralModelLabel(): string { return __('Daily Schedules'); }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'day';

    public static function form(Schema $schema): Schema
    {
        return ScheduleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchedulesTable::configure($table);
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
            'index' => ListSchedules::route('/'),
            'create' => CreateSchedule::route('/create'),
            'edit' => EditSchedule::route('/{record}/edit'),
        ];
    }
}

