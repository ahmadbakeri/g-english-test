<?php

namespace App\Filament\Tutor\Resources\TestScheduleRequestResource\Pages;

use App\Filament\Tutor\Resources\TestScheduleRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;

class ManageTestScheduleRequests extends ManageRecords
{
    protected static string $resource = TestScheduleRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Request Jadwal Tes')
                ->slideOver()
                ->modalWidth('2xl')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['requester_user_id'] = Auth::id();
                    $data['requester_role'] = 'tutor';
                    return $data;
                }),
        ];
    }
}
