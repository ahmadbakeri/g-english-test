<?php

namespace App\Filament\Resources\HR\UserResource\Pages;

use App\Filament\Resources\HR\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label(__('Add Employee')),
        ];
    }
}
