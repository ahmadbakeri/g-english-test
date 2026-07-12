<?php

namespace App\Filament\Tutor\Resources\TutorSalaries\Pages;

use App\Filament\Tutor\Resources\TutorSalaries\TutorSalaryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageTutorSalaries extends ManageRecords
{
    protected static string $resource = TutorSalaryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
