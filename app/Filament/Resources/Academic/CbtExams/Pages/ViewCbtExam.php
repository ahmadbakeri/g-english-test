<?php

namespace App\Filament\Resources\Academic\CbtExams\Pages;

use App\Filament\Resources\Academic\CbtExams\CbtExamResource;
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

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\Academic\CbtExams\Widgets\ExamStatsOverview::class,
        ];
    }
}
