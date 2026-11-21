<?php

namespace App\Filament\Parent\Resources\StudentPayments\Pages;

use App\Filament\Parent\Resources\StudentPayments\StudentPaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageStudentPayments extends ManageRecords
{
    protected static string $resource = StudentPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
