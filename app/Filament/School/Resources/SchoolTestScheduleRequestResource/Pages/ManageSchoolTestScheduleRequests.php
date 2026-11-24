<?php

namespace App\Filament\School\Resources\SchoolTestScheduleRequestResource\Pages;

use App\Filament\School\Resources\SchoolTestScheduleRequestResource;
use App\Models\TestSession;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ManageSchoolTestScheduleRequests extends ManageRecords
{
    protected static string $resource = SchoolTestScheduleRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajukan Jadwal Tes')
                ->slideOver()
                ->modalWidth('2xl')
                ->mutateFormDataUsing(function (array $data): array {
                    $session = TestSession::find($data['test_session_id'] ?? null);
                    $seats = (int) ($data['requested_seats'] ?? 1);
                    if (!$session || $seats > $session->remainingCapacity()) {
                        $rem = $session?->remainingCapacity() ?? 0;
                        throw ValidationException::withMessages([
                            'requested_seats' => "Sisa kapasitas server sesi ini hanya {$rem} kursi. Silakan pilih sesi lain atau bagi ke dua sesi.",
                        ]);
                    }
                    $data['requester_user_id'] = Auth::id();
                    $data['requester_role'] = 'school';
                    $data['school_name'] = Auth::user()?->name;
                    $data['status'] = 'pending';
                    return $data;
                })
                ->after(function (): void {
                    Notification::make()
                        ->title('Request jadwal berhasil dikirim')
                        ->body('Menunggu persetujuan Admin / Tutor.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
