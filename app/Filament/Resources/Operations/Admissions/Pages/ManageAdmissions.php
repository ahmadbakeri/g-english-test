<?php

namespace App\Filament\Resources\Operations\Admissions\Pages;

use App\Filament\Resources\Operations\Admissions\AdmissionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ManageAdmissions extends ManageRecords
{
    protected static string $resource = AdmissionResource::class;

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
            CreateAction::make(),
        ];
    }
}
