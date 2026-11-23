<?php

namespace App\Filament\School\Resources;

use App\Filament\School\Resources\SchoolParticipantResource\Pages\ManageSchoolParticipants;
use App\Filament\School\Widgets\SchoolQuotaOverviewWidget;
use App\Livewire\BrainChampionRegistration;
use App\Models\Admission;
use App\Models\Student;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SchoolParticipantResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $slug = 'school-participants';
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string { return __('Participant Delegation'); }
    public static function getModelLabel(): string { return __('Brain Champion Participant'); }
    public static function getPluralModelLabel(): string { return __('Brain Champion Participant Delegations'); }

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->role === 'school';
    }

    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()->role === 'school';
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->check() && (int) $record->school_user_id === (int) auth()->id();
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('school_user_id', auth()->id())
            ->where('course_level', BrainChampionRegistration::EVENT_TAG);
    }

    public static function getAllowedClassOptions(): array
    {
        $user = auth()->user();
        $level = SchoolQuotaOverviewWidget::resolveSchoolLevelForUser($user);
        $grades = $level === 'SMP' ? [7, 8, 9] : [4, 5, 6];

        $options = [];
        foreach ($grades as $g) {
            $options["Kelas {$g}"] = "Kelas {$g}";
        }
        return $options;
    }

    public static function isClassOptionDisabled(string $classLabel, ?Student $record = null): bool
    {
        if ($record && trim((string) $record->class_name) === $classLabel) {
            return false;
        }

        if (!preg_match('/Kelas\s*(\d+)/i', $classLabel, $m)) {
            return true;
        }
        $grade = (int) $m[1];
        $maxPerSchool = BrainChampionRegistration::maxPerSchoolForGrade($grade);
        $user = auth()->user();
        $normalizedSchool = BrainChampionRegistration::normalizeSchoolName((string) ($user?->name ?? ''));

        $globalUsed = Admission::where('course_level', BrainChampionRegistration::EVENT_TAG)
            ->where('status', '!=', 'rejected')
            ->where('class_name', $classLabel)
            ->count();

        if ($globalUsed >= BrainChampionRegistration::MAX_QUOTA_PER_GRADE) {
            return true;
        }

        $schoolUsed = Admission::where('course_level', BrainChampionRegistration::EVENT_TAG)
            ->where('status', '!=', 'rejected')
            ->where('class_name', $classLabel)
            ->where(function ($q) use ($user, $normalizedSchool) {
                $q->where('school_user_id', $user?->id);
                if ($normalizedSchool !== '') {
                    $q->orWhereRaw('TRIM(UPPER(school_name)) = ?', [$normalizedSchool]);
                }
            })
            ->count();

        return $schoolUsed >= $maxPerSchool;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Siswa')
                    ->description('Identitas lengkap peserta delegasi G-English Brain Champion.')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Select::make('class_name')
                            ->label('Kategori Kelas')
                            ->options(fn () => self::getAllowedClassOptions())
                            ->disableOptionWhen(fn (string $value, ?Student $record): bool => self::isClassOptionDisabled($value, $record))
                            ->required()
                            ->helperText('Kelas yang sudah mencapai kuota global 35/35 atau batas utusan sekolah otomatis dikunci.'),
                        TextInput::make('school_name')
                            ->label('Asal Sekolah')
                            ->default(fn () => auth()->user()?->name)
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('nisn')
                            ->label('NISN (10 Digit)')
                            ->required()
                            ->maxLength(10)
                            ->minLength(10)
                            ->rule('regex:/^\d{10}$/'),
                        TextInput::make('name')
                            ->label('Nama Lengkap Siswa')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('birth_place')
                            ->label('Tempat Lahir')
                            ->maxLength(255),
                        \Filament\Forms\Components\DatePicker::make('birth_date')
                            ->label('Tanggal Lahir'),
                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki (L)',
                                'P' => 'Perempuan (P)',
                            ])
                            ->default('L')
                            ->required(),
                        TextInput::make('religion')
                            ->label('Agama')
                            ->maxLength(100),
                        TextInput::make('phone')
                            ->label('No. HP / WA Siswa')
                            ->tel()
                            ->maxLength(25),
                        \Filament\Forms\Components\Textarea::make('address')
                            ->label('Alamat Lengkap Siswa')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(3),

                Section::make('Data Orang Tua')
                    ->description('Informasi lengkap Ayah, Ibu, dan Kontak Orang Tua / Wali peserta.')
                    ->icon('heroicon-o-users')
                    ->schema([
                        TextInput::make('father_name')
                            ->label('Nama Ayah')
                            ->maxLength(255),
                        TextInput::make('father_phone')
                            ->label('No. HP / WA Ayah')
                            ->tel()
                            ->maxLength(25),
                        TextInput::make('father_job')
                            ->label('Pekerjaan Ayah')
                            ->maxLength(255),
                        TextInput::make('mother_name')
                            ->label('Nama Ibu')
                            ->maxLength(255),
                        TextInput::make('mother_phone')
                            ->label('No. HP / WA Ibu')
                            ->tel()
                            ->maxLength(25),
                        TextInput::make('mother_job')
                            ->label('Pekerjaan Ibu')
                            ->maxLength(255),
                        TextInput::make('parent_name')
                            ->label('Nama Kontak Utama (Orang Tua / Wali)')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('parent_phone')
                            ->label('No. HP / WA Kontak Utama')
                            ->tel()
                            ->required()
                            ->maxLength(25),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordAction('edit')
            ->columns([
                TextColumn::make('competition_code')
                    ->label('Kode / NISN')
                    ->getStateUsing(fn (Student $record): string => $record->competition_code ?: ($record->nis ?: '-'))
                    ->description(fn (Student $record): string => 'NISN: ' . ($record->nisn ?: '-'))
                    ->badge()
                    ->color('primary')
                    ->searchable(['competition_code', 'nis', 'nisn'])
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Siswa')
                    ->weight('bold')
                    ->description(fn (Student $record): string => ($record->gender ?: '-') . ' • ' . ($record->school_name ?: (auth()->user()->name ?? '-')))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('class_name')
                    ->label('Kelas')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('parent_name')
                    ->label('Orang Tua / WA')
                    ->description(fn (Student $record): string => 'WA: ' . ($record->parent_phone ?: '-'))
                    ->searchable(),
                TextColumn::make('nisn')
                    ->label('NISN')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('No. HP Siswa')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->button()
                    ->size('sm')
                    ->slideOver()
                    ->modalWidth('4xl')
                    ->using(function (Student $record, array $data): Student {
                        return DB::transaction(function () use ($record, $data) {
                            $newClass = trim((string) ($data['class_name'] ?? $record->class_name));
                            if ($newClass !== trim((string) $record->class_name)) {
                                if (self::isClassOptionDisabled($newClass, $record)) {
                                    throw ValidationException::withMessages([
                                        'class_name' => "Kuota {$newClass} sudah penuh (35/35) atau batas delegasi sekolah Anda pada kelas tersebut sudah terpenuhi.",
                                    ]);
                                }
                            }

                            $record->update([
                                'class_name' => $newClass,
                                'nisn' => trim((string) $data['nisn']),
                                'name' => trim((string) $data['name']),
                                'birth_place' => $data['birth_place'] ?? null,
                                'birth_date' => $data['birth_date'] ?? null,
                                'gender' => $data['gender'] ?? 'L',
                                'religion' => $data['religion'] ?? null,
                                'address' => $data['address'] ?? null,
                                'father_name' => $data['father_name'] ?? null,
                                'father_phone' => $data['father_phone'] ?? null,
                                'father_job' => $data['father_job'] ?? null,
                                'mother_name' => ($data['mother_name'] ?? null) ?: trim((string) $data['parent_name']),
                                'mother_phone' => ($data['mother_phone'] ?? null) ?: trim((string) $data['parent_phone']),
                                'mother_job' => $data['mother_job'] ?? null,
                                'parent_name' => trim((string) $data['parent_name']),
                                'parent_phone' => trim((string) $data['parent_phone']),
                                'phone' => trim((string) ($data['phone'] ?? '')) ?: trim((string) $data['parent_phone']),
                            ]);

                            $code = $record->competition_code ?: $record->nis;
                            if ($code) {
                                Admission::where('registration_number', $code)->update([
                                    'class_name' => $newClass,
                                    'nisn' => $record->nisn,
                                    'name' => $record->name,
                                    'birth_place' => $record->birth_place,
                                    'birth_date' => $record->birth_date,
                                    'gender' => $record->gender,
                                    'religion' => $record->religion,
                                    'address' => $record->address,
                                    'father_name' => $record->father_name,
                                    'father_phone' => $record->father_phone,
                                    'father_job' => $record->father_job,
                                    'mother_name' => $record->mother_name,
                                    'mother_phone' => $record->mother_phone,
                                    'mother_job' => $record->mother_job,
                                    'parent_name' => $record->parent_name,
                                    'parent_phone' => $record->parent_phone,
                                    'phone' => $record->phone,
                                ]);
                            }

                            Notification::make()
                                ->title('Biodata peserta berhasil diperbarui.')
                                ->success()
                                ->send();

                            return $record;
                        });
                    }),
                \Filament\Actions\Action::make('print_card')
                    ->label('Kartu')
                    ->icon('heroicon-o-identification')
                    ->color('success')
                    ->button()
                    ->size('sm')
                    ->url(fn (Student $record): string => route('report.brain-champion-card', $record->id))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSchoolParticipants::route('/'),
        ];
    }
}
