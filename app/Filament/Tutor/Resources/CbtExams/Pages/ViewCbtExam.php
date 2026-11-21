<?php

namespace App\Filament\Tutor\Resources\CbtExams\Pages;

use App\Filament\Tutor\Resources\CbtExams\CbtExamResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCbtExam extends ViewRecord
{
    protected static string $resource = CbtExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
