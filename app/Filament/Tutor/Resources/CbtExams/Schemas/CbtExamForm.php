<?php

namespace App\Filament\Tutor\Resources\CbtExams\Schemas;

use Filament\Schemas\Schema;

class CbtExamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Exam Details')->schema([
                    \Filament\Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    \Filament\Forms\Components\Textarea::make('description')
                        ->maxLength(65535)
                        ->columnSpanFull(),
                    \Filament\Forms\Components\Select::make('class_id')
                        ->relationship('classRoom', 'name', function (\Illuminate\Database\Eloquent\Builder $query) {
                            $tutorId = auth()->user()->tutor?->id;
                            if ($tutorId) {
                                $query->whereHas('schedules', function ($q) use ($tutorId) {
                                    $q->where('tutor_id', $tutorId);
                                });
                            }
                        })
                        ->required(),
                    \Filament\Forms\Components\Select::make('subject_id')
                        ->relationship('subject', 'name')
                        ->required(),
                ])->columns(2),
                \Filament\Schemas\Components\Section::make('Settings')->schema([
                    \Filament\Forms\Components\TextInput::make('duration_minutes')
                        ->required()
                        ->numeric()
                        ->default(120),
                    \Filament\Forms\Components\TextInput::make('max_attempts')
                        ->required()
                        ->numeric()
                        ->default(3),
                    \Filament\Forms\Components\DateTimePicker::make('start_time'),
                    \Filament\Forms\Components\DateTimePicker::make('end_time'),
                    \Filament\Forms\Components\Toggle::make('is_active')
                        ->default(true)
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }
}
