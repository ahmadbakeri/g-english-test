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
                    \Filament\Forms\Components\ToggleButtons::make('cbt_mode')
                        ->label('Mode Ujian CBT')
                        ->options([
                            'google_form' => 'Mode A: Google Form (Ringan & Bebas Beban Server)',
                            'internal_excel' => 'Mode B: Real CBT + Upload Excel',
                        ])
                        ->icons([
                            'google_form' => 'heroicon-o-globe-alt',
                            'internal_excel' => 'heroicon-o-table-cells',
                        ])
                        ->colors([
                            'google_form' => 'info',
                            'internal_excel' => 'primary',
                        ])
                        ->inline()
                        ->default('google_form')
                        ->required()
                        ->live()
                        ->columnSpanFull(),
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

                \Filament\Schemas\Components\Section::make('Google Form Configuration')
                    ->description('Konfigurasi Embed URL Google Form & Auto-Sync Nilai via Google Sheets CSV.')
                    ->visible(fn ($get) => ($get('cbt_mode') ?? 'google_form') === 'google_form')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('gform_embed_url')
                            ->label('Google Form Embed / Viewform URL')
                            ->placeholder('https://docs.google.com/forms/d/e/.../viewform?embedded=true')
                            ->url()
                            ->required(fn ($get) => ($get('cbt_mode') ?? 'google_form') === 'google_form')
                            ->columnSpanFull(),
                        \Filament\Forms\Components\TextInput::make('gform_sheet_csv_url')
                            ->label('Published Google Sheet CSV URL (Optional)')
                            ->placeholder('https://docs.google.com/spreadsheets/d/e/.../pub?output=csv')
                            ->url()
                            ->columnSpanFull(),
                        \Filament\Forms\Components\TextInput::make('gform_nis_entry_id')
                            ->label('Pre-fill NIS Entry ID')
                            ->placeholder('entry.123456789')
                            ->maxLength(100),
                        \Filament\Forms\Components\TextInput::make('gform_name_entry_id')
                            ->label('Pre-fill Nama Entry ID')
                            ->placeholder('entry.987654321')
                            ->maxLength(100),
                        \Filament\Forms\Components\TextInput::make('gform_score_column')
                            ->label('Nama Kolom Skor di Sheet')
                            ->default('Skor')
                            ->maxLength(100),
                    ])->columns(3),

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
                        ->visible(fn ($get) => $get('cbt_mode') === 'internal_excel')
                        ->helperText('Interval auto-save dalam detik'),
                    \Filament\Forms\Components\Toggle::make('shuffle_questions')
                        ->default(true)
                        ->visible(fn ($get) => $get('cbt_mode') === 'internal_excel')
                        ->helperText('Acak urutan soal untuk setiap siswa'),
                    \Filament\Forms\Components\Toggle::make('shuffle_options')
                        ->default(false)
                        ->visible(fn ($get) => $get('cbt_mode') === 'internal_excel')
                        ->helperText('Acak pilihan jawaban ganda'),
                    \Filament\Forms\Components\Toggle::make('show_result_immediately')
                        ->default(true)
                        ->helperText('Tampilkan nilai langsung setelah selesai'),
                    \Filament\Forms\Components\Toggle::make('allow_review')
                        ->default(false)
                        ->visible(fn ($get) => $get('cbt_mode') === 'internal_excel')
                        ->helperText('Izinkan siswa melihat detail jawaban setelah ujian'),
                ])->columns(2),
            ]);
    }
}
