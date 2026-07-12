<?php

namespace App\Filament\Tutor\Resources\Schedules\Pages;

use App\Filament\Tutor\Resources\Schedules\ScheduleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSchedules extends ManageRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
