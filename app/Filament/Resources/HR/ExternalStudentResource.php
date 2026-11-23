<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\ExternalStudentResource\Pages\ListExternalStudents;
use App\Models\Student;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExternalStudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $slug = 'h-r/external-students';

    public static function getNavigationLabel(): string
    {
        return __('External Students');
    }

    public static function getModelLabel(): string
    {
        return __('External Student');
    }

    public static function getPluralModelLabel(): string
    {
        return __('External Students');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Master Data');
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function form(Schema $schema): Schema
    {
        return StudentResource::form($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('school_name')
            ->groups([
                Tables\Grouping\Group::make('school_name')
                    ->label('Sekolah')
                    ->getTitleFromRecordUsing(fn (Student $record): string => trim((string) $record->school_name) ?: 'Tanpa Sekolah')
                    ->collapsible(),
                Tables\Grouping\Group::make('class_name')
                    ->label('Kelas')
                    ->getTitleFromRecordUsing(fn (Student $record): string => StudentResource::resolveClassLabel($record))
                    ->collapsible(),
            ])
            ->collapsedGroupsByDefault()
            ->groupingSettingsInDropdownOnDesktop()
            ->recordAction('edit')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->weight('bold')
                    ->color('primary')
                    ->description(function (Student $record): string {
                        $code = $record->competition_code ?: ($record->nis ?: '-');
                        $nisn = $record->nisn ? "NISN {$record->nisn}" : '-';
                        return "{$code} • {$nisn} • " . ($record->gender ?: '-');
                    })
                    ->searchable(['name', 'nis', 'nisn', 'competition_code', 'school_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('school_name')
                    ->label(__('School Name'))
                    ->placeholder('-')
                    ->limit(28)
                    ->searchable(),
                Tables\Columns\TextColumn::make('class_name')
                    ->label(__('Class'))
                    ->badge()
                    ->color('warning')
                    ->getStateUsing(fn (Student $record): string => StudentResource::resolveClassLabel($record)),
                Tables\Columns\TextColumn::make('parent_phone')
                    ->label('Orang Tua / WA')
                    ->getStateUsing(fn (Student $record): string => $record->parent_name ?: ($record->father_name ?: ($record->mother_name ?: '-')))
                    ->description(fn (Student $record): ?string => $record->parent_phone ?: ($record->phone ?: null))
                    ->limit(22),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class_name')
                    ->label(__('Class'))
                    ->options([
                        'Kelas 4' => 'Kelas 4',
                        'Kelas 5' => 'Kelas 5',
                        'Kelas 6' => 'Kelas 6',
                        'Kelas 7' => 'Kelas 7',
                        'Kelas 8' => 'Kelas 8',
                        'Kelas 9' => 'Kelas 9',
                    ]),
                Tables\Filters\SelectFilter::make('gender')
                    ->label(__('Gender'))
                    ->options([
                        'L' => __('Male'),
                        'P' => __('Female'),
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()
                    ->button()
                    ->size('sm')
                    ->slideOver()
                    ->modalWidth('4xl'),
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\Action::make('brain_champion_card')
                        ->label('Cetak Kartu Peserta')
                        ->icon('heroicon-o-identification')
                        ->color('warning')
                        ->url(fn (Student $record): string => route('report.brain-champion-card', $record->id))
                        ->openUrlInNewTab(),
                    \Filament\Actions\Action::make('resend_school_credentials')
                        ->label('Kirim Ulang Akun Sekolah (Email)')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->form(function (Student $record): array {
                            $schoolUser = $record->school_user_id ? \App\Models\User::find($record->school_user_id) : null;
                            return [
                                Forms\Components\TextInput::make('email')
                                    ->label('Email Tujuan Koordinator Sekolah')
                                    ->email()
                                    ->default($schoolUser?->email)
                                    ->required(),
                                Forms\Components\TextInput::make('new_password')
                                    ->label('Password / PIN Baru')
                                    ->default(\Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6)))
                                    ->required(),
                            ];
                        })
                        ->action(function (Student $record, array $data): void {
                            $schoolUser = $record->school_user_id
                                ? \App\Models\User::find($record->school_user_id)
                                : \App\Models\User::where('role', 'school')->where('name', $record->school_name)->first();

                            if (!$schoolUser) {
                                $schoolUser = \App\Models\User::create([
                                    'username' => $record->parent_phone ?: ('SCH' . rand(10000, 99999)),
                                    'name' => $record->school_name ?: 'Sekolah Peserta',
                                    'email' => strtolower(trim((string) $data['email'])),
                                    'password' => \Illuminate\Support\Facades\Hash::make($data['new_password']),
                                    'role' => 'school',
                                ]);
                                $record->update(['school_user_id' => $schoolUser->id]);
                            } else {
                                $schoolUser->update([
                                    'email' => strtolower(trim((string) $data['email'])),
                                    'password' => \Illuminate\Support\Facades\Hash::make($data['new_password']),
                                ]);
                            }

                            $participants = Student::where('school_user_id', $schoolUser->id)
                                ->where('course_level', 'G-English Brain Champion')
                                ->get()
                                ->map(fn (Student $s) => [
                                    'competition_code' => $s->competition_code ?: $s->nis,
                                    'nisn' => $s->nisn,
                                    'name' => $s->name,
                                    'class_name' => $s->class_name,
                                    'gender' => $s->gender,
                                ])
                                ->toArray();

                            \Illuminate\Support\Facades\Mail::to($schoolUser->email)->queue(
                                new \App\Mail\SchoolAccountCredentialsMail(
                                    schoolName: $schoolUser->name,
                                    coordinatorName: $record->parent_name ?: 'Koordinator Sekolah',
                                    loginUrl: url('/login'),
                                    username: (string) $schoolUser->username,
                                    plainPassword: (string) $data['new_password'],
                                    participants: $participants,
                                )
                            );

                            \Filament\Notifications\Notification::make()
                                ->title("Email akun sekolah berhasil dikirim ke {$schoolUser->email}")
                                ->success()
                                ->send();
                        }),
                    \Filament\Actions\DeleteAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('student_type', 'external')
            ->with(['user', 'schoolUser']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExternalStudents::route('/'),
        ];
    }
}
