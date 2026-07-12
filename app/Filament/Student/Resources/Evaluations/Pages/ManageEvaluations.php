<?php

namespace App\Filament\Student\Resources\Evaluations\Pages;

use App\Filament\Student\Resources\Evaluations\EvaluationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEvaluations extends ManageRecords
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
