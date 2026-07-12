<?php

namespace App\Filament\Student\Resources\StudentPayments\Pages;

use App\Filament\Student\Resources\StudentPayments\StudentPaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageStudentPayments extends ManageRecords
{
    protected static string $resource = StudentPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
