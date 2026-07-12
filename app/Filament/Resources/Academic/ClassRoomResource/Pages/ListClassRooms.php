<?php
namespace App\Filament\Resources\Academic\ClassRoomResource\Pages;
use App\Filament\Resources\Academic\ClassRoomResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListClassRooms extends ListRecords
{
    protected static string $resource = ClassRoomResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()->label(__('Add Class'))]; }
}
