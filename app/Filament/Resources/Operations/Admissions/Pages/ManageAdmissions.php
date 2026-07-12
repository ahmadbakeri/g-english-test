<?php

namespace App\Filament\Resources\Operations\Admissions\Pages;

use App\Filament\Resources\Operations\Admissions\AdmissionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAdmissions extends ManageRecords
{
    protected static string $resource = AdmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
