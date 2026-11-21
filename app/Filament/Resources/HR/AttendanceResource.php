<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';
    public static function getNavigationLabel(): string { return __('Attendances'); }
    public static function getModelLabel(): string { return __('Absensi'); }
    public static function getPluralModelLabel(): string { return __('Attendances'); }
    public static function getNavigationGroup(): ?string
    {
        return __('Academic');
    }
    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['student', 'classRoom', 'tutor', 'user']);
        
        if (auth()->user()->isTutor() && auth()->user()->tutor) {
            $query->whereHas('classRoom', function (Builder $q) {
                $q->where('tutor_id', auth()->user()->tutor->id);
            });
        }
        
        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            \Filament\Schemas\Components\Section::make(__('Attendance Data'))->schema([
                Forms\Components\Select::make('role_type')->label(__('Attendance Type'))
                    ->options([
                        'student' => 'Siswa',
                        'tutor' => 'Tutor / Guru',
                        'employee' => 'Karyawan',
                    ])
                    ->required()
                    ->default('student')
                    ->reactive()
                    ->afterStateUpdated(function ($set) {
                        $set('class_id', null);
                        $set('student_id', null);
                        $set('tutor_id', null);
                        $set('user_id', null);
                    }),
                Forms\Components\Select::make('class_id')->label(__('Class Room'))->translateLabel()
                    ->options(ClassRoom::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->visible(fn ($get) => in_array($get('role_type'), ['student', 'tutor']))
                    ->required(fn ($get) => $get('role_type') === 'student')
                    ->reactive(),
                Forms\Components\Select::make('student_id')->label(__('Student'))
                    ->options(function ($get) {
                        $classId = $get('class_id');
                        if (!$classId) return Student::pluck('name', 'id');
                        return ClassRoom::find($classId)?->students()->pluck('students.name', 'students.id') ?? [];
                    })
                    ->searchable()
                    ->visible(fn ($get) => $get('role_type') === 'student')
                    ->required(fn ($get) => $get('role_type') === 'student'),
                Forms\Components\Select::make('tutor_id')->label(__('Tutor'))->translateLabel()
                    ->options(\App\Models\Tutor::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->visible(fn ($get) => $get('role_type') === 'tutor')
                    ->required(fn ($get) => $get('role_type') === 'tutor'),
                Forms\Components\Select::make('user_id')->label(__('Employee'))
                    ->options(\App\Models\User::pluck('name', 'id'))
                    ->searchable()
                    ->visible(fn ($get) => $get('role_type') === 'employee')
                    ->required(fn ($get) => $get('role_type') === 'employee'),
                Forms\Components\DatePicker::make('date')->label(__('Attendance Date (Date Only)'))->required()->default(now()),
                Forms\Components\TimePicker::make('time_in')->label(__('Time In (Masuk)'))->default(now()),
                Forms\Components\TimePicker::make('time_out')->label(__('Time Out (Pulang)')),
                Forms\Components\Select::make('status')->label(__('Status'))->required()
                    ->options([
                        'present' => 'âœ… Hadir',
                        'absent' => 'âŒ Tidak Hadir',
                        'late' => 'â° Terlambat',
                        'excused' => 'ðŸ“‹ Izin',
                    ])->default('present'),
                Forms\Components\Textarea::make('notes')->label(__('Notes'))->rows(2)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            \Filament\Tables\Columns\Layout\Split::make([
            Tables\Columns\TextColumn::make('date')->label(__('Date'))->translateLabel()->date('d/m/Y')->sortable(),
            Tables\Columns\TextColumn::make('time_in')->label(__('Time In'))->time('H:i')->sortable(),
            Tables\Columns\TextColumn::make('time_out')->label(__('Time Out'))->time('H:i')->sortable(),
            Tables\Columns\TextColumn::make('role_type')->label(__('Type'))->translateLabel()
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'student' => 'info',
                    'tutor' => 'success',
                    'employee' => 'warning',
                    default => 'gray',
                })
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'student' => 'Siswa',
                    'tutor' => 'Tutor',
                    'employee' => 'Karyawan',
                    default => $state,
                }),
            Tables\Columns\TextColumn::make('name')->label(__('Name'))->translateLabel()
                ->getStateUsing(function (Attendance $record) {
                    if ($record->role_type === 'student') return $record->student?->name;
                    if ($record->role_type === 'tutor') return $record->tutor?->name;
                    if ($record->role_type === 'employee') return $record->user?->name;
                    return '-';
                })->searchable(['student.name', 'tutor.name', 'user.name']),
            Tables\Columns\TextColumn::make('classRoom.name')->label(__('Class Room'))->translateLabel()->searchable()->sortable(),
            Tables\Columns\TextColumn::make('status')->label(__('Status'))
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'present' => 'success',
                    'absent' => 'danger',
                    'late' => 'warning',
                    'excused' => 'info',
                })
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'present' => 'Hadir',
                    'absent' => 'Tidak Hadir',
                    'late' => 'Terlambat',
                    'excused' => 'Izin',
                }),
            Tables\Columns\TextColumn::make('notes')->label(__('Notes'))->limit(30)->toggleable(),
            ])->from('md'),
        ])
        ->defaultSort('date', 'desc')
        ->filters([
            Tables\Filters\SelectFilter::make('role_type')->label(__('Attendance Type'))
                ->options([
                    'student' => 'Siswa',
                    'tutor' => 'Tutor',
                    'employee' => 'Karyawan',
                ]),
            Tables\Filters\SelectFilter::make('status')->label(__('Status'))
                ->options([
                    'present' => 'Hadir',
                    'absent' => 'Tidak Hadir',
                    'late' => 'Terlambat',
                    'excused' => 'Izin',
                ]),
            Tables\Filters\Filter::make('date')
                ->form([Forms\Components\DatePicker::make('date')->label(__('Date'))->translateLabel()])
                ->query(fn ($query, array $data) => $query->when($data['date'], fn ($q, $date) => $q->whereDate('date', $date))),
        ])
        ->actions([\Filament\Actions\EditAction::make(), \Filament\Actions\DeleteAction::make()])
        ->bulkActions([\Filament\Actions\BulkActionGroup::make([\Filament\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
        ];
    }
}



