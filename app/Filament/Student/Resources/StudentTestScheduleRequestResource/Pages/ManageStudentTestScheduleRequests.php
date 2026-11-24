<?php

namespace App\Filament\Student\Resources\StudentTestScheduleRequestResource\Pages;

use App\Filament\Student\Resources\StudentTestScheduleRequestResource;
use App\Models\TestSession;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ManageStudentTestScheduleRequests extends ManageRecords
{
    protected static string $resource = StudentTestScheduleRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Ajukan Jadwal Tes')
                ->slideOver()
                ->modalWidth('xl')
                ->mutateFormDataUsing(function (array $data): array {
                    $session = TestSession::find($data['test_session_id'] ?? null);
                    if (!$session || $session->remainingCapacity() < 1) {
                        throw ValidationException::withMessages([
                            'test_session_id' => 'Kapasitas server sesi ini sudah penuh. Silakan pilih sesi lain.',
                        ]);
                    }
                    $student = Auth::user()?->student;
                    $data['requester_user_id'] = Auth::id();
                    $data['requester_role'] = 'siswa';
                    $data['student_id'] = $student?->id;
                    $data['school_name'] = $student?->school_name ?: $student?->class_name;
                    $data['requested_seats'] = 1;
                    $data['status'] = 'pending';
                    return $data;
                })
                ->after(function (): void {
                    Notification::make()
                        ->title('Request jadwal terkirim')
                        ->body('Menunggu persetujuan Admin / Tutor.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
