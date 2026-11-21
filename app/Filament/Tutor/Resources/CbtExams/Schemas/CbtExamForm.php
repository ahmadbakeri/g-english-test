<?php

namespace App\Filament\Tutor\Resources\CbtExams\Schemas;

use Filament\Schemas\Schema;

class CbtExamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Exam Details')
                    ->description('Pilih mode pelaksanaan ujian, judul evaluasi, kelas, dan mata pelajaran.')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
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
                            ->label('Judul Ujian')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        \Filament\Forms\Components\Select::make('class_id')
                            ->label('Kelas')
                            ->relationship('classRoom', 'name', function (\Illuminate\Database\Eloquent\Builder $query) {
                                $tutorId = auth()->user()->tutor?->id;
                                if ($tutorId) {
                                    $query->where(function ($q) use ($tutorId) {
                                        $q->where('tutor_id', $tutorId)
                                            ->orWhereHas('schedules', function ($sq) use ($tutorId) {
                                                $sq->where('tutor_id', $tutorId);
                                            });
                                    });
                                }
                            })
                            ->required(),
                        \Filament\Forms\Components\Select::make('subject_id')
                            ->label('Mata Pelajaran')
                            ->relationship('subject', 'name')
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('description')
                            ->label('Deskripsi / Petunjuk Ujian')
                            ->rows(2)
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Google Form Configuration')
                    ->description('Konfigurasi Embed URL Google Form & Auto-Sync Nilai via Google Sheets CSV.')
                    ->icon('heroicon-o-globe-alt')
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

                \Filament\Schemas\Components\Section::make('Settings')
                    ->description('Pengaturan durasi waktu pengerjaan, batas percobaan, dan jadwal aktif ujian.')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('duration_minutes')
                            ->label('Durasi Ujian')
                            ->required()
                            ->numeric()
                            ->suffix('Menit')
                            ->default(120),
                        \Filament\Forms\Components\TextInput::make('max_attempts')
                            ->label('Batas Percobaan')
                            ->required()
                            ->numeric()
                            ->suffix('Kali')
                            ->default(3),
                        \Filament\Forms\Components\DateTimePicker::make('start_time')
                            ->label('Waktu Mulai'),
                        \Filament\Forms\Components\DateTimePicker::make('end_time')
                            ->label('Waktu Selesai'),
                        \Filament\Forms\Components\Toggle::make('is_active')
                            ->label('Aktifkan Ujian')
                            ->default(true)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
