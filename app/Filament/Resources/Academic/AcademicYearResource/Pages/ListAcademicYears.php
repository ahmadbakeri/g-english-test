<?php
namespace App\Filament\Resources\Academic\AcademicYearResource\Pages;
use App\Filament\Resources\Academic\AcademicYearResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListAcademicYears extends ListRecords
{
    protected static string $resource = AcademicYearResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()->label(__('Add Academic Year'))]; }
}
