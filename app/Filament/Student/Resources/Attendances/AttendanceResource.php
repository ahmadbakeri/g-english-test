<?php

namespace App\Filament\Student\Resources\Attendances;

use App\Filament\Student\Resources\Attendances\Pages\ManageAttendances;
use App\Models\Attendance;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttendanceResource extends Resource
{
    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Academic');
    }

    protected static ?string $model = Attendance::class;

    public static function getNavigationLabel(): string { return __('Attendance'); }
    public static function getPluralModelLabel(): string { return __('Attendance'); }
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return true;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('student_id', auth()->user()->student->id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('class_id')
                    ->numeric(),
                Select::make('student_id')
                    ->relationship('student', 'name'),
                DatePicker::make('date')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('present'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                TextInput::make('role_type')
                    ->required()
                    ->default('student'),
                Select::make('tutor_id')
                    ->relationship('tutor', 'name'),
                Select::make('user_id')
                    ->relationship('user', 'name'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('class_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('student.name')
                    ->label(__('Student'))
                    ->placeholder('-'),
                TextEntry::make('date')
                    ->date(),
                TextEntry::make('status'),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('time_in')
                    ->time()
                    ->placeholder('-'),
                TextEntry::make('time_out')
                    ->time()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('role_type'),
                TextEntry::make('tutor.name')
                    ->label(__('Tutor'))
                    ->placeholder('-'),
                TextEntry::make('user.name')
                    ->label(__('User'))
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('date')
                    ->label(__('Date'))
                    ->date('d M Y')
                    ->description(fn (Attendance $record): string => ($record->time_in ? substr((string) $record->time_in, 0, 5) : '--:--') . ' - ' . ($record->time_out ? substr((string) $record->time_out, 0, 5) : '--:--'))
                    ->sortable(),
                TextColumn::make('classRoom.name')
                    ->label(__('Class Room'))
                    ->weight('bold')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('tutor.name')
                    ->label(__('Tutor'))
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success',
                        'absent' => 'danger',
                        'late' => 'warning',
                        'excused' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'present' => 'Hadir',
                        'absent' => 'Tidak Hadir',
                        'late' => 'Terlambat',
                        'excused' => 'Izin',
                        default => $state,
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->button()->size('sm'),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAttendances::route('/'),
        ];
    }
}
