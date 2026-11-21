<?php

namespace App\Filament\Resources\Academic\StudyMaterialResource\Pages;

use App\Filament\Resources\Academic\StudyMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudyMaterial extends EditRecord
{
    protected static string $resource = StudyMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
