<?php

namespace App\Filament\Resources\Academic\CbtExams\Schemas;

use Filament\Schemas\Schema;

class CbtExamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Exam Details')
                    ->description('Pilih mode pelaksanaan ujian, judul evaluasi, kelas target, dan mata pelajaran.')
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
                            ->relationship('classRoom', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        \Filament\Forms\Components\Select::make('subject_id')
                            ->label('Mata Pelajaran')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload()
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
                        \Filament\Forms\Components\TextInput::make('max_concurrent_users')
                            ->label('Batas Peserta Bersamaan')
                            ->numeric()
                            ->suffix('Siswa')
                            ->placeholder('Biarkan kosong untuk tanpa batas')
                            ->helperText('Batas maksimal siswa mengerjakan bersamaan'),
                        \Filament\Forms\Components\TextInput::make('auto_save_interval')
                            ->label('Interval Auto-Save')
                            ->numeric()
                            ->suffix('Detik')
                            ->default(30)
                            ->visible(fn ($get) => $get('cbt_mode') === 'internal_excel')
                            ->helperText('Interval auto-save dalam detik'),
                        \Filament\Forms\Components\Toggle::make('is_active')
                            ->label('Aktifkan Ujian')
                            ->default(true),
                        \Filament\Forms\Components\Toggle::make('show_result_immediately')
                            ->label('Tampilkan Nilai Langsung')
                            ->default(true)
                            ->helperText('Tampilkan nilai langsung setelah selesai'),
                        \Filament\Forms\Components\Toggle::make('shuffle_questions')
                            ->label('Acak Urutan Soal')
                            ->default(true)
                            ->visible(fn ($get) => $get('cbt_mode') === 'internal_excel')
                            ->helperText('Acak urutan soal untuk setiap siswa'),
                        \Filament\Forms\Components\Toggle::make('shuffle_options')
                            ->label('Acak Pilihan Jawaban')
                            ->default(false)
                            ->visible(fn ($get) => $get('cbt_mode') === 'internal_excel')
                            ->helperText('Acak pilihan jawaban ganda'),
                        \Filament\Forms\Components\Toggle::make('allow_review')
                            ->label('Izinkan Review Jawaban')
                            ->default(false)
                            ->visible(fn ($get) => $get('cbt_mode') === 'internal_excel')
                            ->helperText('Izinkan siswa melihat detail jawaban setelah ujian'),
                    ])->columns(2),
            ]);
    }
}
