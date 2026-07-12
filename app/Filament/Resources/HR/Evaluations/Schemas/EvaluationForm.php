<?php

namespace App\Filament\Resources\HR\Evaluations\Schemas;

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
                    ->relationship('student', 'name')
                    ->required(),
                \Filament\Forms\Components\Select::make('subject_id')
                    ->label(__('Subject'))->translateLabel()
                    ->relationship('subject', 'name')
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

