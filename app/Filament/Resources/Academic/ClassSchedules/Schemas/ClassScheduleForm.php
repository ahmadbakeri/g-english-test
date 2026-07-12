<?php

namespace App\Filament\Resources\Academic\ClassSchedules\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class ClassScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('class_id')
                    ->label(__('Class Room'))->translateLabel()
                    ->relationship('classRoom', 'name')
                    ->required(),
                Select::make('tutor_id')
                    ->label(__('Tutor'))->translateLabel()
                    ->relationship('tutor', 'name')
                    ->required(),
                Repeater::make('schedule_items')
                    ->label(__('List of Days & Hours'))
                    ->schema([
                        Select::make('day')
                            ->label(__('Day'))
                            ->options([
                                'Monday' => __('Monday'),
                                'Tuesday' => __('Tuesday'),
                                'Wednesday' => __('Wednesday'),
                                'Thursday' => __('Thursday'),
                                'Friday' => __('Friday'),
                                'Saturday' => __('Saturday'),
                                'Sunday' => __('Sunday'),
                            ])
                            ->required(),
                        TimePicker::make('start_time')
                            ->label(__('Start Time'))->translateLabel()
                            ->required(),
                        TimePicker::make('end_time')
                            ->label(__('End Time'))->translateLabel()
                            ->required(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}


