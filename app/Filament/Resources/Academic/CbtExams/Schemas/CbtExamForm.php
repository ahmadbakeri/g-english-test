<?php

namespace App\Filament\Resources\Academic\CbtExams\Schemas;

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
                        ->relationship('classRoom', 'name')
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
                        ->default(true),
                    \Filament\Forms\Components\TextInput::make('max_concurrent_users')
                        ->numeric()
                        ->placeholder('Biarkan kosong untuk tanpa batas')
                        ->helperText('Batas maksimal siswa mengerjakan bersamaan'),
                    \Filament\Forms\Components\TextInput::make('auto_save_interval')
                        ->numeric()
                        ->default(30)
                        ->helperText('Interval auto-save dalam detik'),
                    \Filament\Forms\Components\Toggle::make('shuffle_questions')
                        ->default(true)
                        ->helperText('Acak urutan soal untuk setiap siswa'),
                    \Filament\Forms\Components\Toggle::make('shuffle_options')
                        ->default(false)
                        ->helperText('Acak pilihan jawaban ganda'),
                    \Filament\Forms\Components\Toggle::make('show_result_immediately')
                        ->default(true)
                        ->helperText('Tampilkan nilai langsung setelah selesai'),
                    \Filament\Forms\Components\Toggle::make('allow_review')
                        ->default(false)
                        ->helperText('Izinkan siswa melihat detail jawaban setelah ujian'),
                ])->columns(2),
            ]);
    }
}
