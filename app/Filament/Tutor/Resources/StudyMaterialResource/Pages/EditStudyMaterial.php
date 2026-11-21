<?php

namespace App\Filament\Tutor\Resources\StudyMaterialResource\Pages;

use App\Filament\Tutor\Resources\StudyMaterialResource;
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
