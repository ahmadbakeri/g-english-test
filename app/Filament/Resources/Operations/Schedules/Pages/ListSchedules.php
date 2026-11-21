<?php

namespace App\Filament\Resources\Operations\Schedules\Pages;

use App\Filament\Resources\Operations\Schedules\ScheduleResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use App\Models\ClassSchedule;
use App\Models\Schedule;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('qr_code')
                ->label(__('Scan Attendance'))
                ->icon('heroicon-o-qr-code')
                ->url(fn (): string => \App\Filament\Pages\ScanAttendance::getUrl())
                ->openUrlInNewTab(),

            Action::make('generate')
                ->label(__('Generate Daily Schedule'))
                ->color('success')
                ->icon('heroicon-o-arrow-path')
                ->form([
                    DatePicker::make('date')
                        ->label(__('Date'))->translateLabel()
                        ->required()
                        ->default(now()),
                ])
                ->action(function (array $data) {
                    $date = Carbon::parse($data['date']);
                    
                    $dayOfWeek = $date->format('l');

                    if (!$dayOfWeek) return;
                    
                    $classSchedules = ClassSchedule::all();
                    $count = 0;
                    
                    foreach ($classSchedules as $classSchedule) {
                        if ($classSchedule->schedule_items) {
                            foreach ($classSchedule->schedule_items as $item) {
                                if (isset($item['day']) && $item['day'] === $dayOfWeek) {
                                    $tutorId = null;
                                    if ($classSchedule->tutor_id) {
                                        $tutor = \App\Models\Tutor::find($classSchedule->tutor_id);
                                        if ($tutor) {
                                            $tutorId = $tutor->user_id;
                                        }
                                    }
                                    
                                    if ($tutorId) {
                                        Schedule::firstOrCreate([
                                            'class_room_id' => $classSchedule->class_id,
                                            'date' => $date->format('Y-m-d'),
                                            'start_time' => $item['start_time'] ?? '00:00',
                                        ], [
                                            'user_id' => $tutorId,
                                            'end_time' => $item['end_time'] ?? '00:00',
                                        ]);
                                        $count++;
                                    }
                                }
                            }
                        }
                    }
                    
                    Notification::make()
                        ->title(__('Schedule Successfully Created'))
                        ->body("Berhasil meng-generate $count jadwal harian untuk tanggal " . $date->format('d/m/Y'))
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}


