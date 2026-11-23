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
    public static function getNavigationLabel(): string { return __('Internal Students'); }
    public static function getModelLabel(): string { return __('Internal Student'); }
    public static function getPluralModelLabel(): string { return __('Internal Students'); }
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
            \Filament\Schemas\Components\Tabs::make('StudentTabs')
                ->tabs([
                    \Filament\Schemas\Components\Tabs\Tab::make('Identitas & Akademik')
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
                                    'MI' => 'MI',
                                    'SMP' => 'SMP',
                                    'MTs' => 'MTs',
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
                        ])->columns(2),

                    \Filament\Schemas\Components\Tabs\Tab::make('Data Orang Tua / Wali')
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

                    \Filament\Schemas\Components\Tabs\Tab::make('Foto & Status')
                        ->icon('heroicon-o-shield-check')
                        ->schema([
                            Forms\Components\FileUpload::make('photo')
                                ->label(__('Photo'))
                                ->avatar()
                                ->alignCenter()
                                ->columnSpanFull()
                                ->extraAttributes(['class' => 'flex justify-center text-center'])
                                ->imageEditor()
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
                ]),
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
            ->recordAction('edit')
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('')
                    ->circular()
                    ->imageSize(36)
                    ->disk('public')
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=0a4282&background=dbeafe'),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->weight('bold')
                    ->color('primary')
                    ->description(function (Student $record): string {
                        $code = $record->user?->username ?: ($record->nis ?: '-');
                        $gender = $record->gender ?: '-';
                        $school = trim((string) $record->school_name);
                        return $school !== '' ? "{$code} • {$gender} • {$school}" : "{$code} • {$gender}";
                    })
                    ->searchable(['name', 'nis', 'nisn', 'school_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('class_name')
                    ->label(__('Class'))
                    ->badge()
                    ->color('info')
                    ->getStateUsing(fn (Student $record): string => self::resolveClassLabel($record))
                    ->description(fn (Student $record): string => $record->student_type === 'external' ? __('External') : __('Internal'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->placeholder('-')
                    ->description(function (Student $record): ?string {
                        $parent = $record->father_name ?: ($record->mother_name ?: $record->parent_name);
                        return $parent ? 'Wali: ' . \Illuminate\Support\Str::limit($parent, 22) : null;
                    })
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('student_type')
                    ->label(__('Type'))
                    ->badge()
                    ->color(fn (?string $state): string => $state === 'external' ? 'warning' : 'info')
                    ->formatStateUsing(fn (?string $state): string => $state === 'external' ? __('External') : __('Internal'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gender')
                    ->label('L/P')
                    ->badge()
                    ->color(fn (?string $state): string => $state === 'L' ? 'info' : 'danger')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Registered'))
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                \Filament\Actions\EditAction::make()
                    ->button()
                    ->size('sm')
                    ->slideOver()
                    ->modalWidth('4xl'),
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\ViewAction::make()->slideOver()->modalWidth('4xl'),
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
        return parent::getEloquentQuery()
            ->where(fn (Builder $q) => $q->where('student_type', 'internal')->orWhereNull('student_type'))
            ->with(['user', 'classes', 'parents']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
        ];
    }
}
