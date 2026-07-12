<?php

namespace App\Filament\Tutor\Resources\Evaluations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EvaluationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('student_id')
                    ->label(__('Student'))
                    ->options(function () {
                        $tutor = auth()->user()->tutor;
                        if (!$tutor) return [];
                        return \App\Models\Student::whereHas('classes', function ($q) use ($tutor) {
                            $q->where('tutor_id', $tutor->id);
                        })->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required(),
                \Filament\Forms\Components\Select::make('subject_id')
                    ->label(__('Subject'))
                    ->options(function () {
                        $tutor = auth()->user()->tutor;
                        if (!$tutor) return [];
                        return \App\Models\Subject::whereIn('id', \App\Models\ClassRoom::where('tutor_id', $tutor->id)->pluck('subject_id'))->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required(),
                TextInput::make('score')
                    ->label(__('Score (0-100)'))
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                Textarea::make('notes')
                    ->label(__('Notes/Evaluation'))
                    ->columnSpanFull(),
                DatePicker::make('date')
                    ->label(__('Evaluation Date'))
                    ->default(now())
                    ->required(),
            ]);
    }
}
