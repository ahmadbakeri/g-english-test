<?php

namespace App\Filament\Resources\HR\StudentResource\Pages;

use App\Filament\Resources\HR\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ExportAction::make()->exporter(\App\Filament\Exports\StudentExporter::class),
            Actions\CreateAction::make()
                ->label(__('Add Student'))
                ->mutateFormDataUsing(function (array $data): array {
                    $data['student_type'] = $data['student_type'] ?? 'internal';
                    return $data;
                }),
        ];
    }
}
