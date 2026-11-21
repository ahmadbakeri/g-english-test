<?php

namespace App\Filament\Resources\Academic;

use App\Filament\Resources\Academic\ClassRoomResource\Pages;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Tutor;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClassRoomResource extends Resource
{
    protected static ?string $model = ClassRoom::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-library';
    public static function getNavigationLabel(): string { return __('Class Rooms'); }
    public static function getModelLabel(): string { return __('Class Rooms'); }
    public static function getPluralModelLabel(): string { return __('Class Rooms'); }
    public static function getNavigationGroup(): ?string
    {
        return __('Master Data');
    }
    public static function getNavigationSort(): ?int
    {
        return 3;
    }
    protected static ?string $slug = 'class-rooms';

    public static function canCreate(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['academicYear', 'subject', 'tutor', 'schedules']);

        if (auth()->user()->isTutor() && auth()->user()->tutor) {
            $query->where('tutor_id', auth()->user()->tutor->id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            \Filament\Schemas\Components\Section::make(__('Class Room Data'))
                ->description('Konfigurasi nama rombongan belajar, tahun akademik, mata pelajaran, wali kelas, dan kuota kursi.')
                ->icon('heroicon-o-building-library')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('Class Name'))
                        ->required(),
                    Forms\Components\Select::make('academic_year_id')
                        ->label(__('Academic Year'))
                        ->options(AcademicYear::pluck('name', 'id'))
                        ->searchable(),
                    Forms\Components\Select::make('subject_id')
                        ->label(__('Subject'))
                        ->translateLabel()
                        ->options(Subject::pluck('name', 'id'))
                        ->searchable(),
                    Forms\Components\Select::make('tutor_id')
                        ->label(__('Homeroom Teacher (Optional)'))
                        ->options(Tutor::where('is_active', true)->pluck('name', 'id'))
                        ->searchable(),
                    Forms\Components\TextInput::make('capacity')
                        ->label(__('Capacity'))
                        ->numeric()
                        ->suffix('Siswa')
                        ->default(20),
                    Forms\Components\Toggle::make('is_active')
                        ->label(__('Active'))
                        ->default(true),
                ])->columns(3),

            \Filament\Schemas\Components\Section::make(__('Class Schedule'))
                ->description('Atur hari, jam mulai/selesai, dan tutor pengajar untuk setiap sesi mingguan.')
                ->icon('heroicon-o-calendar-days')
                ->schema([
                    Forms\Components\Repeater::make('schedules')
                        ->label('')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('day')
                                ->label(__('Day'))
                                ->options([
                                    'Monday' => __('Monday'),
                                    'Tuesday' => __('Tuesday'),
                                    'Wednesday' => __('Wednesday'),
                                    'Thursday' => __('Thursday'),
                                    'Friday' => __('Friday'),
                                    'Saturday' => __('Saturday'),
                                    'Sunday' => __('Sunday'),
                                ])->required(),
                            Forms\Components\TimePicker::make('start_time')
                                ->label(__('Start Time'))
                                ->translateLabel()
                                ->required(),
                            Forms\Components\TimePicker::make('end_time')
                                ->label(__('End Time'))
                                ->translateLabel()
                                ->required(),
                            Forms\Components\Select::make('tutor_id')
                                ->label(__('Teaching Tutor'))
                                ->options(Tutor::where('is_active', true)->pluck('name', 'id'))
                                ->searchable()
                                ->nullable(),
                        ])->columns(4)
                        ->addActionLabel('Tambah Jadwal')
                        ->defaultItems(1),
                ]),

            \Filament\Schemas\Components\Section::make(__('Student List'))
                ->description('Pilih siswa yang terdaftar di dalam kelas ini.')
                ->icon('heroicon-o-user-group')
                ->schema([
                    Forms\Components\Select::make('students')
                        ->label(__('Student'))
                        ->multiple()
                        ->relationship('students', 'name')
                        ->preload()
                        ->searchable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            \Filament\Tables\Columns\Layout\Split::make([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Class Name'))
                    ->weight('bold')
                    ->description(fn (ClassRoom $record): string => $record->academicYear?->name ?? 'Tanpa Tahun Ajaran')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label(__('Academic Year'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label(__('Subject'))
                    ->translateLabel()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('tutor.name')
                    ->label(__('Homeroom Teacher'))
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('schedules.day')
                    ->label(__('Schedule'))
                    ->badge()
                    ->color('gray')
                    ->separator(', '),
                Tables\Columns\TextColumn::make('students_count')
                    ->label(__('Student'))
                    ->counts('students')
                    ->badge()
                    ->color('success')
                    ->suffix(' Siswa'),
                Tables\Columns\TextColumn::make('capacity')
                    ->label(__('Capacity'))
                    ->numeric()
                    ->suffix(' Kursi')
                    ->alignEnd(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean(),
            ])->from('md'),
        ])
        ->actions([
            \Filament\Actions\ActionGroup::make([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray')
                ->tooltip('Aksi'),
        ])
        ->bulkActions([
            \Filament\Actions\BulkActionGroup::make([
                \Filament\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassRooms::route('/'),
        ];
    }
}
