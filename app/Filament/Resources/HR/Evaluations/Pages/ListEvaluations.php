<?php

namespace App\Filament\Resources\HR\Evaluations\Pages;

use App\Filament\Resources\HR\Evaluations\EvaluationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEvaluations extends ListRecords
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\ExportAction::make()->exporter(\App\Filament\Exports\EvaluationExporter::class),
            CreateAction::make(),
        ];
    }
}
