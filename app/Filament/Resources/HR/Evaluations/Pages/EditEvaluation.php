<?php

namespace App\Filament\Resources\HR\Evaluations\Pages;

use App\Filament\Resources\HR\Evaluations\EvaluationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEvaluation extends EditRecord
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
