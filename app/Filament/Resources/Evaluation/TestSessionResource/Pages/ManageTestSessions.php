<?php

namespace App\Filament\Resources\Evaluation\TestSessionResource\Pages;

use App\Filament\Resources\Evaluation\TestSessionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageTestSessions extends ManageRecords
{
    protected static string $resource = TestSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label(__('Add Test Session'))->slideOver()->modalWidth('2xl'),
        ];
    }
}
