<?php

namespace App\Filament\Tutor\Resources;

use App\Filament\Resources\Evaluation\TestScheduleRequestResource as AdminRequestResource;
use App\Filament\Tutor\Resources\TestScheduleRequestResource\Pages;
use App\Models\TestScheduleRequest;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TestScheduleRequestResource extends Resource
{
    protected static ?string $model = TestScheduleRequest::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?string $slug = 'test-schedule-requests';

    public static function getNavigationLabel(): string { return __('Test Schedule Requests'); }
    public static function getModelLabel(): string { return __('Test Schedule Request'); }
    public static function getPluralModelLabel(): string { return __('Test Schedule Requests'); }
    public static function getNavigationSort(): ?int { return 6; }

    public static function getNavigationBadge(): ?string
    {
        $pending = TestScheduleRequest::where('status', 'pending')->count();
        return $pending > 0 ? (string) $pending : null;
    }

    public static function form(Schema $schema): Schema
    {
        return AdminRequestResource::form($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminRequestResource::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTestScheduleRequests::route('/'),
        ];
    }
}
