<?php

namespace App\Filament\Agency\Resources;

use App\Filament\Agency\Resources\AgencyParticipantResource\Pages\ManageAgencyParticipants;
use App\Models\Student;
use App\Support\SchoolAgencyClassifier;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AgencyParticipantResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $slug = 'participants';
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string { return __('Registered Participants'); }
    public static function getModelLabel(): string { return __('Brain Champion Participant'); }
    public static function getPluralModelLabel(): string { return __('Registered Participants'); }

    public static function canViewAny(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['kemenag', 'kemendikdasmen'], true);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $agency = $user?->role === SchoolAgencyClassifier::AGENCY_KEMENAG
            ? SchoolAgencyClassifier::AGENCY_KEMENAG
            : SchoolAgencyClassifier::AGENCY_KEMENDIKDASMEN;

        $ids = SchoolAgencyClassifier::studentIdsForAgency($agency);

        return parent::getEloquentQuery()
            ->with('schoolUser')
            ->whereIn('id', $ids ?: [0]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('competition_code')
                    ->label(__('Code'))
                    ->badge()
                    ->color('primary')
                    ->description(fn (Student $record): string => 'NISN: ' . ($record->nisn ?: '-'))
                    ->searchable(['competition_code', 'nisn'])
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->weight('bold')
                    ->description(fn (Student $record): string => ($record->school_name ?: '-') . ' • ' . ($record->gender === 'P' ? 'P' : 'L'))
                    ->searchable(['name', 'school_name'])
                    ->sortable(),
                TextColumn::make('class_name')
                    ->label(__('Class (Kelas)'))
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('schoolUser.name')
                    ->label(__('Coordinator'))
                    ->description(fn (Student $record): ?string => $record->schoolUser?->username)
                    ->placeholder('-'),
                TextColumn::make('school_name')
                    ->label(__('School Name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('gender')
                    ->label(__('Gender'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === 'P' ? 'P' : 'L')
                    ->color(fn (?string $state): string => $state === 'P' ? 'danger' : 'info')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('class_name')
                    ->label(__('Class (Kelas)'))
                    ->options([
                        'Kelas 4' => 'Kelas 4',
                        'Kelas 5' => 'Kelas 5',
                        'Kelas 6' => 'Kelas 6',
                        'Kelas 7' => 'Kelas 7',
                        'Kelas 8' => 'Kelas 8',
                        'Kelas 9' => 'Kelas 9',
                    ]),
            ])
            ->defaultSort('school_name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAgencyParticipants::route('/'),
        ];
    }
}
