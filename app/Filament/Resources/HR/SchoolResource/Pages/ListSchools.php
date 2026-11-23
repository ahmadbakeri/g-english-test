<?php

namespace App\Filament\Resources\HR\SchoolResource\Pages;

use App\Filament\Resources\HR\SchoolResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSchools extends ListRecords
{
    protected static string $resource = SchoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Add School'))
                ->slideOver()
                ->modalWidth('2xl')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['role'] = 'school';
                    if (blank($data['username'] ?? null)) {
                        $data['username'] = SchoolResource::generateNextSchoolCode();
                    }

                    return $data;
                }),
        ];
    }
}
