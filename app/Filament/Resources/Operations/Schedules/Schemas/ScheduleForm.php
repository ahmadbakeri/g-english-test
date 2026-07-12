<?php

namespace App\Filament\Resources\Operations\Schedules\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Schemas\Schema;
use App\Models\ClassSchedule;
use Carbon\Carbon;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->label(__('Date'))->translateLabel()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateDefaults($get, $set);
                    }),
                Select::make('class_room_id')
                    ->label(__('Class Room'))->translateLabel()
                    ->relationship('classRoom', 'name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateDefaults($get, $set);
                    }),
                Select::make('user_id')
                    ->label(__('Tutor'))->translateLabel()
                    ->relationship('user', 'name', fn ($query) => $query->where('role', 'tutor'))
                    ->required(),
                TimePicker::make('start_time')
                    ->label(__('Start Time'))->translateLabel()
                    ->required(),
                TimePicker::make('end_time')
                    ->label(__('End Time'))->translateLabel()
                    ->required(),
            ]);
    }

    protected static function updateDefaults(Get $get, Set $set): void
    {
        $date = $get('date');
        $classId = $get('class_room_id');

        if ($date && $classId) {
            $parsedDate = Carbon::parse($date);
            $dayMap = [
                'Monday' => __('Monday'),
                'Tuesday' => __('Tuesday'),
                'Wednesday' => __('Wednesday'),
                'Thursday' => __('Thursday'),
                'Friday' => __('Friday'),
                'Saturday' => __('Saturday'),
                'Sunday' => __('Sunday'),
            ];
            $dayOfWeek = $dayMap[$parsedDate->format('l')] ?? null;

            if ($dayOfWeek) {
                $schedules = ClassSchedule::where('class_id', $classId)->get();

                foreach ($schedules as $schedule) {
                    if ($schedule && $schedule->schedule_items) {
                        $foundDay = false;
                        foreach ($schedule->schedule_items as $item) {
                            if (isset($item['day']) && $item['day'] === $dayOfWeek) {
                                if ($schedule->tutor_id) {
                                    $tutor = \App\Models\Tutor::find($schedule->tutor_id);
                                    if ($tutor && $tutor->user_id) {
                                        $set('user_id', $tutor->user_id);
                                    }
                                }
                                if (isset($item['start_time'])) {
                                    $set('start_time', $item['start_time']);
                                }
                                if (isset($item['end_time'])) {
                                    $set('end_time', $item['end_time']);
                                }
                                $foundDay = true;
                                break;
                            }
                        }
                        if ($foundDay) break;
                    }
                }
            }
        }
    }
}


