<?php

namespace App\Filament\Resources\HR\StudentResource\Pages;

use App\Filament\Resources\HR\StudentResource;
use App\Models\Student;
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
            'all' => Tab::make('Semua Siswa')
                ->icon('heroicon-o-user-group')
                ->badge(fn (): int => Student::count()),
            'internal' => Tab::make('Siswa Internal')
                ->icon('heroicon-o-Academic-cap')
                ->icon('heroicon-o-building-library')
                ->badge(fn (): int => Student::where('student_type', 'internal')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('student_type', 'internal')),
            'external' => Tab::make('Siswa Eksternal')
                ->icon('heroicon-o-globe-alt')
                ->badge(fn (): int => Student::where('student_type', 'external')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('student_type', 'external')),
        ];
    }

    public function updatedActiveTab(): void
    {
        parent::updatedActiveTab();

        if ($this->activeTab === 'internal') {
            $this->tableGrouping = 'class_name';
        } elseif ($this->activeTab === 'external') {
            $this->tableGrouping = 'school_name';
        } else {
            $this->tableGrouping = null;
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ExportAction::make()->exporter(\App\Filament\Exports\StudentExporter::class),
            Actions\CreateAction::make()->label(__('Add Student')),
        ];
    }
}
