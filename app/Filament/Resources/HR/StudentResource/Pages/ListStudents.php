<?php
namespace App\Filament\Resources\HR\StudentResource\Pages;
use App\Filament\Resources\HR\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('All')),
            'internal' => Tab::make(__('Internal'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('student_type', 'internal')),
            'external' => Tab::make(__('External'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('student_type', 'external')),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ExportAction::make()->exporter(\App\Filament\Exports\StudentExporter::class),
            Actions\CreateAction::make()->label(__('Add Student'))
        ];
    }
}
