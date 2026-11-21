<?php

namespace App\Filament\Resources\Evaluation\Pages;

use App\Filament\Resources\Evaluation\CbtAttemptResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCbtAttempts extends ManageRecords
{
    protected static string $resource = CbtAttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
