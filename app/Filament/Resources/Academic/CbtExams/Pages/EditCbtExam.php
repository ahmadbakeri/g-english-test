<?php

namespace App\Filament\Resources\Academic\CbtExams\Pages;

use App\Filament\Resources\Academic\CbtExams\CbtExamResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCbtExam extends EditRecord
{
    protected static string $resource = CbtExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
