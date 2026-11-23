<?php

namespace App\Filament\Resources\HR\ExternalStudentResource\Pages;

use App\Filament\Resources\HR\ExternalStudentResource;
use App\Livewire\BrainChampionRegistration;
use App\Models\Student;
use App\Support\SchoolAgencyClassifier;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListExternalStudents extends ListRecords
{
    protected static string $resource = ExternalStudentResource::class;

    protected string $view = 'filament.admin.pages.external-students-hierarchy';

    public string $search = '';

    /** @var array<int, string> */
    public array $openLevels = [];

    /** @var array<int, string> */
    public array $openSchools = [];

    /** @var array<int, string> */
    public array $openClasses = [];

    public ?int $editingStudentId = null;

    /** @var array<string, mixed> */
    public array $editForm = [];

    public function toggleLevel(string $level): void
    {
        if (in_array($level, $this->openLevels, true)) {
            $this->openLevels = array_values(array_diff($this->openLevels, [$level]));
        } else {
            $this->openLevels[] = $level;
        }
    }

    public function toggleSchool(string $schoolKey): void
    {
        if (in_array($schoolKey, $this->openSchools, true)) {
            $this->openSchools = array_values(array_diff($this->openSchools, [$schoolKey]));
        } else {
            $this->openSchools[] = $schoolKey;
        }
    }

    public function toggleClass(string $classKey): void
    {
        if (in_array($classKey, $this->openClasses, true)) {
            $this->openClasses = array_values(array_diff($this->openClasses, [$classKey]));
        } else {
            $this->openClasses[] = $classKey;
        }
    }

    public function startInlineEdit(int $studentId): void
    {
        if ($this->editingStudentId === $studentId) {
            $this->cancelInlineEdit();
            return;
        }

        $student = Student::where('student_type', 'external')->find($studentId);
        if (!$student) {
            return;
        }

        $this->editingStudentId = $student->id;
        $this->editForm = [
            'name' => (string) $student->name,
            'nisn' => (string) ($student->nisn ?? ''),
            'class_name' => (string) ($student->class_name ?: 'Kelas 4'),
            'school_name' => (string) ($student->school_name ?? ''),
            'gender' => (string) ($student->gender ?: 'L'),
            'phone' => (string) ($student->phone ?? ''),
            'parent_name' => (string) ($student->parent_name ?? ''),
            'parent_phone' => (string) ($student->parent_phone ?? ''),
            'is_active' => (bool) $student->is_active,
        ];
    }

    public function cancelInlineEdit(): void
    {
        $this->editingStudentId = null;
        $this->editForm = [];
    }

    public function saveInlineEdit(): void
    {
        if (!$this->editingStudentId) {
            return;
        }

        $student = Student::where('student_type', 'external')->find($this->editingStudentId);
        if (!$student) {
            $this->cancelInlineEdit();
            return;
        }

        $student->update([
            'name' => trim((string) ($this->editForm['name'] ?? $student->name)),
            'nisn' => trim((string) ($this->editForm['nisn'] ?? '')) ?: null,
            'class_name' => trim((string) ($this->editForm['class_name'] ?? $student->class_name)),
            'school_name' => trim((string) ($this->editForm['school_name'] ?? '')) ?: null,
            'gender' => (string) ($this->editForm['gender'] ?? 'L'),
            'phone' => trim((string) ($this->editForm['phone'] ?? '')) ?: null,
            'parent_name' => trim((string) ($this->editForm['parent_name'] ?? '')) ?: null,
            'parent_phone' => trim((string) ($this->editForm['parent_phone'] ?? '')) ?: null,
            'is_active' => (bool) ($this->editForm['is_active'] ?? true),
        ]);

        Notification::make()
            ->title('Data siswa diperbarui')
            ->success()
            ->send();

        $this->cancelInlineEdit();
    }

    public function deleteStudent(int $studentId): void
    {
        $student = Student::where('student_type', 'external')->find($studentId);
        if ($student) {
            $student->delete();
            Notification::make()
                ->title('Siswa dihapus')
                ->success()
                ->send();
        }
        if ($this->editingStudentId === $studentId) {
            $this->cancelInlineEdit();
        }
    }

    public function getHierarchyProperty(): array
    {
        $needle = mb_strtolower(trim($this->search));

        $students = Student::query()
            ->with(['user:id,username', 'schoolUser:id,username,name'])
            ->where('student_type', 'external')
            ->orderBy('school_name')
            ->orderBy('class_name')
            ->orderBy('name')
            ->get();

        if ($needle !== '') {
            $students = $students->filter(function (Student $s) use ($needle): bool {
                $haystack = implode(' ', [
                    $s->name,
                    $s->nis,
                    $s->nisn,
                    $s->competition_code,
                    $s->school_name,
                    $s->class_name,
                    $s->user?->username,
                    $s->schoolUser?->username,
                ]);
                return str_contains(mb_strtolower($haystack), $needle);
            });
        }

        $levels = [
            'SD' => ['schools' => [], 'schoolCount' => 0, 'studentCount' => 0],
            'MI' => ['schools' => [], 'schoolCount' => 0, 'studentCount' => 0],
            'SMP' => ['schools' => [], 'schoolCount' => 0, 'studentCount' => 0],
            'MTs' => ['schools' => [], 'schoolCount' => 0, 'studentCount' => 0],
        ];

        $schoolKeysSeen = [];

        foreach ($students as $student) {
            $level = SchoolAgencyClassifier::classifyLevel($student->school_name, $student->education_level, $student->class_name);
            $schoolName = trim((string) $student->school_name) ?: 'Tanpa Sekolah';
            $schoolKey = BrainChampionRegistration::normalizeSchoolName($schoolName) ?: 'tanpa-sekolah';
            $className = trim((string) $student->class_name) ?: 'Tanpa Kelas';

            if (!isset($levels[$level]['schools'][$schoolKey])) {
                $levels[$level]['schools'][$schoolKey] = [
                    'key' => $schoolKey,
                    'name' => $schoolName,
                    'username' => $student->schoolUser?->username,
                    'studentCount' => 0,
                    'classes' => [],
                ];
                $schoolKeysSeen[$level . '::' . $schoolKey] = true;
            }

            if (!$levels[$level]['schools'][$schoolKey]['username'] && $student->schoolUser?->username) {
                $levels[$level]['schools'][$schoolKey]['username'] = $student->schoolUser->username;
            }

            $levels[$level]['schools'][$schoolKey]['classes'][$className][] = $student;
            $levels[$level]['schools'][$schoolKey]['studentCount']++;
            $levels[$level]['studentCount']++;
        }

        foreach ($levels as $lvl => &$lvlData) {
            foreach ($lvlData['schools'] as &$sch) {
                ksort($sch['classes'], SORT_NATURAL);
            }
            $lvlData['schools'] = array_values($lvlData['schools']);
            $lvlData['schoolCount'] = count($lvlData['schools']);
        }

        return [
            'levels' => $levels,
            'totalStudents' => $students->count(),
            'totalSchools' => count($schoolKeysSeen),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $hierarchy = $this->hierarchy;
                    $rows = [];
                    $no = 1;
                    foreach (['SD', 'MI', 'SMP', 'MTs'] as $lvl) {
                        foreach ($hierarchy['levels'][$lvl]['schools'] ?? [] as $school) {
                            foreach ($school['classes'] as $className => $students) {
                                foreach ($students as $s) {
                                    $rows[] = [
                                        $no++,
                                        $lvl,
                                        $school['name'],
                                        $className,
                                        $s->user?->username ?: ($s->competition_code ?: ($s->nis ?: '-')),
                                        $s->nisn ?: '-',
                                        $s->name,
                                        ($s->parent_name ?: ($s->father_name ?: '-')) . ' / ' . ($s->parent_phone ?: ($s->phone ?: '-')),
                                    ];
                                }
                            }
                        }
                    }

                    return \App\Support\TablePdfExporter::download(
                        filename: 'daftar-siswa-eksternal-' . now()->format('Y-m-d') . '.pdf',
                        title: 'Daftar Siswa Eksternal (Brain Champion)',
                        subtitle: $this->search ? 'Filter pencarian: "' . $this->search . '"' : 'Seluruh jenjang SD, MI, SMP, dan MTs',
                        headers: ['No', 'Jenjang', 'Sekolah', 'Kelas', 'Kode / User', 'NISN', 'Nama Siswa', 'Orang Tua / WA'],
                        rows: $rows,
                        summary: [
                            'Total Peserta' => $hierarchy['totalStudents'] . ' siswa',
                            'Total Sekolah' => $hierarchy['totalSchools'] . ' sekolah',
                        ],
                    );
                }),
            Actions\Action::make('download_brain_champion_template')
                ->label('Download Template Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\BrainChampionStudentTemplateExport(null),
                        'template_peserta_brain_champion.xlsx'
                    );
                }),
            Actions\Action::make('import_brain_champion_excel')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->form([
                    \Filament\Forms\Components\Select::make('school_user_id')
                        ->label('Pilih Akun Sekolah (Opsional)')
                        ->options(fn () => \App\Models\User::where('role', 'school')->pluck('name', 'id')->toArray())
                        ->searchable(),
                    \Filament\Forms\Components\TextInput::make('school_name')
                        ->label('Nama Sekolah (Jika akun sekolah belum dipilih)')
                        ->placeholder('Contoh: SDN 1 Tanjung'),
                    \Filament\Forms\Components\FileUpload::make('excel_file')
                        ->label('Upload File Excel Peserta (.xlsx)')
                        ->disk('local')
                        ->directory('brain_champion_imports')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv',
                        ])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $filePath = storage_path('app/' . $data['excel_file']);
                    if (!file_exists($filePath)) {
                        $filePath = \Illuminate\Support\Facades\Storage::disk('local')->path($data['excel_file']);
                    }

                    $schoolUser = !empty($data['school_user_id']) ? \App\Models\User::find($data['school_user_id']) : null;
                    $importer = new \App\Imports\BrainChampionStudentImport(
                        schoolUserId: $schoolUser?->id,
                        schoolName: $schoolUser?->name ?: ($data['school_name'] ?? 'Sekolah Peserta'),
                    );
                    \Maatwebsite\Excel\Facades\Excel::import($importer, $filePath);

                    $summary = "Ditambahkan: {$importer->createdCount} peserta, Diperbarui: {$importer->updatedCount} peserta.";
                    if ($importer->skippedCount > 0) {
                        $summary .= " Dilewati: {$importer->skippedCount} baris.";
                    }

                    Notification::make()
                        ->title('Import Excel Brain Champion Selesai')
                        ->body($summary)
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make()
                ->label('Tambah Siswa Eksternal')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['student_type'] = 'external';
                    return $data;
                }),
        ];
    }
}
