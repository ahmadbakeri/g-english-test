<?php

namespace App\Filament\Resources\Academic\StudyMaterialResource\Pages;

use App\Filament\Resources\Academic\StudyMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudyMaterials extends ListRecords
{
    protected static string $resource = StudyMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
