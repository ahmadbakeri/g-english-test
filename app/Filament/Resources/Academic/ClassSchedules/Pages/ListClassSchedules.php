<?php

namespace App\Filament\Resources\Academic\ClassSchedules\Pages;

use App\Filament\Resources\Academic\ClassSchedules\ClassScheduleResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClassSchedules extends ListRecords
{
    protected static string $resource = ClassScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('qr_code')
                ->label(__('Scan Attendance'))
                ->icon('heroicon-o-qr-code')
                ->url(fn (): string => \App\Filament\Pages\ScanAttendance::getUrl())
                ->openUrlInNewTab(),

            CreateAction::make(),
        ];
    }
}
