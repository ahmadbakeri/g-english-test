<?php

namespace App\Filament\Resources\Academic\ClassSchedules\Pages;

use App\Filament\Resources\Academic\ClassSchedules\ClassScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClassSchedule extends EditRecord
{
    protected static string $resource = ClassScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
