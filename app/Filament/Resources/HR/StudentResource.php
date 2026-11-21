<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\StudentResource\Pages;
use App\Models\Student;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentResource extends Resource
{
    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }

    protected static ?string $model = Student::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';
    public static function getNavigationLabel(): string { return __('Student Management'); }
    public static function getModelLabel(): string { return __('Student'); }
    public static function getPluralModelLabel(): string { return __('Student'); }
    public static function getNavigationGroup(): ?string
    {
        return __('Academic');
    }
    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function resolveClassLabel(Student $record): string
    {
        $enrolledClasses = $record->classes
            ->pluck('name')
            ->map(fn ($n) => trim((string) $n))
            ->filter()
            ->sort()
            ->join(', ');

        if ($enrolledClasses !== '') {
            return $enrolledClasses;
        }

        $rawClass = trim((string) $record->class_name);
        return $rawClass !== '' ? $rawClass : 'Tanpa Kelas';
    }

    public static function resolveSchoolClassLabel(Student $record): string
    {
        $rawSchool = trim((string) $record->school_name);
        $school = $rawSchool !== '' ? $rawSchool : 'Tanpa Sekolah';
        $class = self::resolveClassLabel($record);

        return "{$school} — {$class}";
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            \Filament\Schemas\Components\Section::make(__('Student Data'))
                ->description('Identitas utama siswa, kategori program (Internal/Eksternal), serta informasi sekolah dan kelas.')
                ->icon('heroicon-o-identification')
                ->schema([
                    Forms\Components\TextInput::make('nis')
                        ->label(__('No. Siswa (NIS)'))
                        ->prefixIcon('heroicon-o-hashtag')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(20),
                    Forms\Components\TextInput::make('nisn')
                        ->label('NISN (10 Digit)')
                        ->maxLength(20),
                    Forms\Components\Select::make('student_type')
                        ->label(__('Student Type'))
                        ->options([
                            'internal' => 'Siswa Internal',
                            'external' => 'Siswa Eksternal',
                        ])
                        ->default('internal')
                        ->required(),
                    Forms\Components\TextInput::make('name')
                        ->label(__('Full Name'))
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('gender')
                        ->label(__('Gender'))
                        ->options([
                            'L' => __('Male'),
                            'P' => __('Female'),
                        ]),
                    Forms\Components\TextInput::make('religion')
                        ->label('Agama (Religion)')
                        ->maxLength(100),
                    Forms\Components\TextInput::make('birth_place')
                        ->label(__('Place of Birth')),
                    Forms\Components\DatePicker::make('birth_date')
                        ->label(__('Date of Birth')),
                    Forms\Components\TextInput::make('phone')
                        ->label(__('Phone No.'))
                        ->prefixIcon('heroicon-o-phone')
                        ->tel(),
                    Forms\Components\Select::make('education_level')
                        ->label(__('Education Level'))
                        ->options([
                            'SD' => 'SD',
                            'SMP' => 'SMP',
                            'SMA' => 'SMA',
                            'Umum' => 'Umum',
                        ]),
                    Forms\Components\TextInput::make('course_level')
                        ->label(__('Course Level')),
                    Forms\Components\TextInput::make('school_name')
                        ->label('Sekolah Asal (School Name)'),
                    Forms\Components\TextInput::make('class_name')
                        ->label('Kelas (Class)'),
                    Forms\Components\Textarea::make('address')
                        ->label(__('Address'))
                        ->rows(2)
                        ->columnSpanFull(),
                ])->columns(3),

            \Filament\Schemas\Components\Section::make('Data Orang Tua / Wali')
                ->description('Data Ayah, Ibu, dan Wali disinkronkan otomatis ke akun Portal Orang Tua (student_parent).')
                ->icon('heroicon-o-users')
                ->schema([
                    \Filament\Schemas\Components\Fieldset::make('Data Ayah (Father)')->schema([
                        Forms\Components\TextInput::make('father_name')
                            ->label(__('Father\'s Name')),
                        Forms\Components\TextInput::make('father_phone')
                            ->label(__('Father\'s Phone'))
                            ->tel(),
                        Forms\Components\TextInput::make('father_email')
                            ->label(__('Father\'s Email'))
                            ->email(),
                        Forms\Components\TextInput::make('father_job')
                            ->label(__('Father\'s Occupation')),
                    ])->columns(2),

                    \Filament\Schemas\Components\Fieldset::make('Data Ibu (Mother)')->schema([
                        Forms\Components\TextInput::make('mother_name')
                            ->label(__('Mother\'s Name')),
                        Forms\Components\TextInput::make('mother_phone')
                            ->label(__('Mother\'s Phone'))
                            ->tel(),
                        Forms\Components\TextInput::make('mother_email')
                            ->label(__('Mother\'s Email'))
                            ->email(),
                        Forms\Components\TextInput::make('mother_job')
                            ->label(__('Mother\'s Occupation')),
                    ])->columns(2),

                    \Filament\Schemas\Components\Fieldset::make('Data Wali (Guardian)')->schema([
                        Forms\Components\TextInput::make('parent_name')
                            ->label('Nama Wali (Guardian Name)'),
                        Forms\Components\TextInput::make('parent_phone')
                            ->label('No. HP Wali (Guardian Phone)')
                            ->tel(),
                    ])->columns(2),
                ]),

            \Filament\Schemas\Components\Section::make(__('Status'))
                ->description('Foto profil siswa dan status keaktifan akademik.')
                ->icon('heroicon-o-shield-check')
                ->schema([
                    Forms\Components\FileUpload::make('photo')
                        ->label(__('Photo'))
                        ->avatar()
                        ->alignCenter()
                        ->columnSpanFull()
                        ->extraAttributes(['class' => 'flex justify-center text-center hide-edit-btn'])
                        ->circleCropper()
                        ->imageCropAspectRatio('1:1')
                        ->automaticallyOpenImageEditorForAspectRatio()
                        ->disk('public')
                        ->directory('students')
                        ->maxSize(2048)
                        ->hintAction(
                            \Filament\Actions\Action::make('remove_photo')
                                ->label(__('Remove (Reset)'))
                                ->icon('heroicon-o-trash')
                                ->color('danger')
                                ->action(function ($record, callable $set) {
                                    $set('photo', null);
                                    if ($record) {
                                        $record->photo = null;
                                        $record->save();
                                    }
                                })
                        ),
                    Forms\Components\Toggle::make('is_active')
                        ->label(__('Active'))
                        ->default(true),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        $classSortSql = "COALESCE(
            NULLIF(TRIM(LOWER((
                SELECT GROUP_CONCAT(TRIM(c.name) ORDER BY c.name SEPARATOR ', ')
                FROM classes c
                INNER JOIN class_student cs ON cs.class_id = c.id
                WHERE cs.student_id = students.id AND c.deleted_at IS NULL
            ))), ''),
            NULLIF(TRIM(LOWER(students.class_name)), ''),
            'tanpa kelas'
        )";

        return $table
            ->groups([
                Group::make('class_name')
                    ->label('Kelas')
                    ->collapsible()
                    ->getKeyFromRecordUsing(fn (Student $record): string => mb_strtolower(trim(self::resolveClassLabel($record))))
                    ->getTitleFromRecordUsing(fn (Student $record): string => trim(self::resolveClassLabel($record)))
                    ->orderQueryUsing(function (Builder $query, string $direction) use ($classSortSql): Builder {
                        $dir = strtolower($direction) === 'desc' ? 'DESC' : 'ASC';
                        return $query
                            ->reorder()
                            ->orderByRaw("{$classSortSql} {$dir}")
                            ->orderBy('students.name', 'asc');
                    }),
                Group::make('school_name')
                    ->label('Sekolah — Kelas')
                    ->collapsible()
                    ->getKeyFromRecordUsing(fn (Student $record): string => mb_strtolower(trim(self::resolveSchoolClassLabel($record))))
                    ->getTitleFromRecordUsing(fn (Student $record): string => trim(self::resolveSchoolClassLabel($record)))
                    ->orderQueryUsing(function (Builder $query, string $direction) use ($classSortSql): Builder {
                        $dir = strtolower($direction) === 'desc' ? 'DESC' : 'ASC';
                        return $query
                            ->reorder()
                            ->orderByRaw("COALESCE(NULLIF(TRIM(LOWER(students.school_name)), ''), 'tanpa sekolah') {$dir}")
                            ->orderByRaw("{$classSortSql} {$dir}")
                            ->orderBy('students.name', 'asc');
                    }),
            ])
            ->defaultGroup(function ($livewire): ?string {
                $activeTab = $livewire->activeTab ?? 'internal';
                return match ($activeTab) {
                    'internal' => 'class_name',
                    'external' => 'school_name',
                    default => 'class_name',
                };
            })
            ->collapsedGroupsByDefault()
            ->groupingSettingsInDropdownOnDesktop()
            ->columns([
                \Filament\Tables\Columns\Layout\Split::make([
                    Tables\Columns\ImageColumn::make('photo')
                        ->label(__('Photo'))
                        ->circular()
                        ->disk('public')
                        ->grow(false)
                        ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=0a4282&background=dbeafe'),
                    Tables\Columns\TextColumn::make('user.username')
                        ->label(__('No. Siswa'))
                        ->badge()
                        ->color('gray')
                        ->grow(false)
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('name')
                        ->label(__('Name'))
                        ->translateLabel()
                        ->weight('bold')
                        ->description(fn (Student $record): string => 'NIS: ' . ($record->nis ?: '-'))
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('student_type')
                        ->label(__('Type'))
                        ->badge()
                        ->grow(false)
                        ->color(fn (?string $state): string => $state === 'external' ? 'warning' : 'info')
                        ->formatStateUsing(fn (?string $state): string => $state === 'external' ? __('External') : __('Internal')),
                    Tables\Columns\TextColumn::make('school_name')
                        ->label('Sekolah')
                        ->placeholder('-')
                        ->searchable()
                        ->toggleable(),
                    Tables\Columns\TextColumn::make('class_name')
                        ->label('Kelas')
                        ->badge()
                        ->color('info')
                        ->grow(false)
                        ->getStateUsing(fn (Student $record): string => self::resolveClassLabel($record))
                        ->searchable()
                        ->toggleable(),
                    Tables\Columns\TextColumn::make('gender')
                        ->label(__('Gender'))
                        ->badge()
                        ->grow(false)
                        ->color(fn (?string $state): string => $state === 'L' ? 'info' : 'danger'),
                    Tables\Columns\TextColumn::make('phone')
                        ->label(__('Phone'))
                        ->placeholder('-')
                        ->grow(false)
                        ->searchable(),
                    Tables\Columns\TextColumn::make('parent_summary')
                        ->label('Orang Tua / Wali')
                        ->getStateUsing(fn (Student $record): string => $record->father_name ?: ($record->mother_name ?: ($record->parent_name ?: '-')))
                        ->toggleable(),
                    Tables\Columns\IconColumn::make('is_active')
                        ->label(__('Active'))
                        ->grow(false)
                        ->boolean(),
                    Tables\Columns\TextColumn::make('created_at')
                        ->label(__('Registered'))
                        ->dateTime('d/m/Y')
                        ->grow(false)
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ])->from('md'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('student_type')
                    ->label(__('Student Type'))
                    ->options([
                        'internal' => __('Internal'),
                        'external' => __('External'),
                    ]),
                Tables\Filters\SelectFilter::make('gender')
                    ->label(__('Gender'))
                    ->options([
                        'L' => __('Male'),
                        'P' => __('Female'),
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('Active Status')),
            ])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\ViewAction::make(),
                    \Filament\Actions\EditAction::make(),
                    \Filament\Actions\Action::make('brain_champion_card')
                        ->label('Cetak Kartu Peserta')
                        ->icon('heroicon-o-identification')
                        ->color('warning')
                        ->visible(fn (Student $record): bool => $record->course_level === 'G-English Brain Champion' || !empty($record->competition_code))
                        ->url(fn (Student $record): string => route('report.brain-champion-card', $record->id))
                        ->openUrlInNewTab(),
                    \Filament\Actions\Action::make('resend_school_credentials')
                        ->label('Kirim Ulang Akun Sekolah (Email)')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->visible(fn (Student $record): bool => !empty($record->school_user_id) || $record->course_level === 'G-English Brain Champion')
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
                                    ->required()
                                    ->helperText('Password akun sekolah akan direset ke PIN ini dan dikirimkan via email.'),
                            ];
                        })
                        ->action(function (Student $record, array $data): void {
                            $schoolUser = $record->school_user_id
                                ? \App\Models\User::find($record->school_user_id)
                                : \App\Models\User::where('role', 'school')->where('name', $record->school_name)->first();

                            if (!$schoolUser) {
                                $username = $record->parent_phone ?: ('SCH' . rand(10000, 99999));
                                $schoolUser = \App\Models\User::create([
                                    'username' => $username,
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
                    \Filament\Actions\Action::make('certificate')
                        ->label(__('Certificate'))
                        ->icon('heroicon-o-academic-cap')
                        ->color('success')
                        ->url(fn ($record) => route('report.certificate', $record->id))
                        ->openUrlInNewTab(),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'classes', 'parents']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
        ];
    }
}
