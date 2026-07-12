<?php
namespace App\Filament\Resources\Operations\JournalResource\Pages;
use App\Filament\Resources\Operations\JournalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListJournals extends ListRecords
{
    protected static string $resource = JournalResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()->label(__('Add Journal'))]; }
}
