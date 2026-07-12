<?php
namespace App\Filament\Resources\HR\TutorResource\Pages;
use App\Filament\Resources\HR\TutorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListTutors extends ListRecords
{
    protected static string $resource = TutorResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()->label(__('Add Tutor'))]; }
}
