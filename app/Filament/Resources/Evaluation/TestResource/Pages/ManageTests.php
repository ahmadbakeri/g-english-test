<?php

namespace App\Filament\Resources\Evaluation\TestResource\Pages;

use App\Filament\Resources\Evaluation\TestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageTests extends ManageRecords
{
    protected static string $resource = TestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label(__('Add Test'))->slideOver()->modalWidth('2xl'),
        ];
    }
}
