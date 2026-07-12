<?php
namespace App\Filament\Resources\Finance\HonorResource\Pages;
use App\Filament\Resources\Finance\HonorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListHonors extends ListRecords
{
    protected static string $resource = HonorResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()->label(__('Add Honor'))]; }
}
