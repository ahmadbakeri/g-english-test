<?php

namespace App\Filament\Resources\HR\StudentResource\Pages;

use App\Filament\Resources\HR\StudentResource;
use App\Models\ClassRoom;
use App\Models\Student;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class ListStudents extends ListRecords
{
    use WithFileUploads;

    protected static string $resource = StudentResource::class;

    protected string $view = 'filament.admin.pages.internal-students-accordion';

    public const SURVIVAL_CLASSES = [
        'Survival English SD/MI',
        'Survival English SMP/MTs',
        'Survival English SMA/MA',
    ];

    public string $search = '';

    /** @var array<int, string> */
    public array $openGroups = [];

    public ?int $editingId = null;

    /** @var array<string, mixed> */
    public array $editForm = [];

    public $editPhoto = null;

    public function toggleGroup(string $group): void
    {
        if (in_array($group, $this->openGroups, true)) {
            $this->openGroups = array_values(array_diff($this->openGroups, [$group]));
        } else {
            $this->openGroups[] = $group;
        }
    }

    public function startInlineEdit(int $id): void
    {
        if ($this->editingId === $id) {
            $this->cancelInlineEdit();
            return;
        }

        $s = Student::with('classes')->find($id);
        if (!$s) {
            return;
        }

        $this->editPhoto = null;
        $this->editingId = $s->id;
        $this->editForm = [
            'photo' => $s->photo,
            'name' => (string) $s->name,
            'nis' => (string) ($s->nis ?? ''),
            'nisn' => (string) ($s->nisn ?? ''),
            'class_name' => $this->normalizeSurvivalClass(StudentResource::resolveClassLabel($s)),
            'education_level' => (string) ($s->education_level ?: 'SD/MI'),
            'course_level' => 'Survival English',
            'school_name' => (string) ($s->school_name ?? ''),
            'gender' => (string) ($s->gender ?: 'L'),
            'birth_place' => (string) ($s->birth_place ?? ''),
            'birth_date' => $s->birth_date ? $s->birth_date->format('Y-m-d') : '',
            'religion' => (string) ($s->religion ?? ''),
            'phone' => (string) ($s->phone ?? ''),
            'social_media' => (string) ($s->social_media ?? ''),
            'address' => (string) ($s->address ?? ''),
            'father_name' => (string) ($s->father_name ?? ''),
            'father_phone' => (string) ($s->father_phone ?? ''),
            'father_email' => (string) ($s->father_email ?? ''),
            'father_job' => (string) ($s->father_job ?? ''),
            'mother_name' => (string) ($s->mother_name ?? ''),
            'mother_phone' => (string) ($s->mother_phone ?? ''),
            'mother_email' => (string) ($s->mother_email ?? ''),
            'mother_job' => (string) ($s->mother_job ?? ''),
            'is_active' => (bool) $s->is_active,
        ];
    }

    public function removePhoto(): void
    {
        $this->editPhoto = null;
        $this->editForm['photo'] = null;
    }

    public function cancelInlineEdit(): void
    {
        $this->editingId = null;
        $this->editForm = [];
        $this->editPhoto = null;
    }

    public function saveInlineEdit(): void
    {
        if (!$this->editingId) {
            return;
        }
        $s = Student::find($this->editingId);
        if (!$s) {
            $this->cancelInlineEdit();
            return;
        }

        $photoPath = $this->editForm['photo'] ?? $s->photo;
        if ($this->editPhoto) {
            $this->validate([
                'editPhoto' => 'image|max:3072',
            ]);
            $photoPath = $this->editPhoto->store('students', 'public');
        }

        $chosenClass = $this->normalizeSurvivalClass((string) ($this->editForm['class_name'] ?? 'Survival English SD/MI'));
        $eduLevel = match ($chosenClass) {
            'Survival English SMP/MTs' => 'SMP/MTs',
            'Survival English SMA/MA' => 'SMA/MA',
            default => 'SD/MI',
        };

        $fatherName = trim((string) ($this->editForm['father_name'] ?? ''));
        $fatherPhone = trim((string) ($this->editForm['father_phone'] ?? ''));
        $motherName = trim((string) ($this->editForm['mother_name'] ?? ''));
        $motherPhone = trim((string) ($this->editForm['mother_phone'] ?? ''));

        $s->update([
            'photo' => $photoPath ?: null,
            'name' => trim((string) ($this->editForm['name'] ?? $s->name)),
            'nis' => trim((string) ($this->editForm['nis'] ?? $s->nis)),
            'nisn' => trim((string) ($this->editForm['nisn'] ?? '')) ?: null,
            'class_name' => $chosenClass,
            'education_level' => $eduLevel,
            'course_level' => 'Survival English',
            'school_name' => trim((string) ($this->editForm['school_name'] ?? '')) ?: null,
            'gender' => (string) ($this->editForm['gender'] ?? 'L'),
            'birth_place' => trim((string) ($this->editForm['birth_place'] ?? '')) ?: null,
            'birth_date' => ($this->editForm['birth_date'] ?? '') ?: null,
            'religion' => trim((string) ($this->editForm['religion'] ?? '')) ?: null,
            'phone' => trim((string) ($this->editForm['phone'] ?? '')) ?: null,
            'social_media' => trim((string) ($this->editForm['social_media'] ?? '')) ?: null,
            'address' => trim((string) ($this->editForm['address'] ?? '')) ?: null,
            'father_name' => $fatherName ?: null,
            'father_phone' => $fatherPhone ?: null,
            'father_email' => trim((string) ($this->editForm['father_email'] ?? '')) ?: null,
            'father_job' => trim((string) ($this->editForm['father_job'] ?? '')) ?: null,
            'mother_name' => $motherName ?: null,
            'mother_phone' => $motherPhone ?: null,
            'mother_email' => trim((string) ($this->editForm['mother_email'] ?? '')) ?: null,
            'mother_job' => trim((string) ($this->editForm['mother_job'] ?? '')) ?: null,
            'parent_name' => $fatherName ?: ($motherName ?: null),
            'parent_phone' => $fatherPhone ?: ($motherPhone ?: null),
            'is_active' => (bool) ($this->editForm['is_active'] ?? true),
        ]);

        // Sync enrolled ClassRoom to match chosen Survival English level
        $targetClass = ClassRoom::where('name', $chosenClass)->first();
        if ($targetClass) {
            $s->classes()->sync([$targetClass->id => ['enrolled_at' => now()->toDateString()]]);
        }

        if ($s->user) {
            $s->user->update(['name' => $s->name]);
        }

        Notification::make()->title('Data & foto siswa internal diperbarui')->success()->send();
        $this->cancelInlineEdit();
    }

    public function deleteRecord(int $id): void
    {
        $s = Student::find($id);
        if ($s) {
            $s->delete();
            Notification::make()->title('Siswa dihapus')->success()->send();
        }
        if ($this->editingId === $id) {
            $this->cancelInlineEdit();
        }
    }

    protected function normalizeSurvivalClass(string $raw): string
    {
        $u = strtoupper($raw);
        if (str_contains($u, 'SMA') || str_contains($u, 'MA')) {
            return 'Survival English SMA/MA';
        }
        if (str_contains($u, 'SMP') || str_contains($u, 'MTS')) {
            return 'Survival English SMP/MTs';
        }
        return 'Survival English SD/MI';
    }

    public function photoUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        return Storage::disk('public')->url($path);
    }

    public function getGroupedStudentsProperty(): array
    {
        $needle = mb_strtolower(trim($this->search));

        $students = Student::query()
            ->with(['user:id,username', 'classes:id,name'])
            ->where(fn ($q) => $q->where('student_type', 'internal')->orWhereNull('student_type'))
            ->orderBy('name')
            ->get();

        if ($needle !== '') {
            $students = $students->filter(function (Student $s) use ($needle): bool {
                $hay = implode(' ', [
                    $s->name, $s->nis, $s->nisn, $s->phone, $s->school_name,
                    $s->father_name, $s->mother_name,
                    StudentResource::resolveClassLabel($s), $s->user?->username,
                ]);
                return str_contains(mb_strtolower($hay), $needle);
            });
        }

        $groups = [
            'Survival English SD/MI' => [],
            'Survival English SMP/MTs' => [],
            'Survival English SMA/MA' => [],
        ];

        foreach ($students as $s) {
            $label = $this->normalizeSurvivalClass(StudentResource::resolveClassLabel($s));
            $groups[$label][] = $s;
        }

        return [
            'groups' => $groups,
            'total' => $students->count(),
            'active' => $students->where('is_active', true)->count(),
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
                    $data = $this->groupedStudents;
                    $rows = [];
                    $no = 1;
                    foreach ($data['groups'] as $cls => $list) {
                        foreach ($list as $s) {
                            $rows[] = [
                                $no++,
                                $s->nis ?: '-',
                                $s->name,
                                $cls,
                                $s->gender ?: '-',
                                $s->phone ?: '-',
                                ($s->father_name ?: ($s->mother_name ?: '-')) . ' (' . ($s->father_phone ?: ($s->mother_phone ?: '-')) . ')',
                                $s->is_active ? 'Aktif' : 'Nonaktif',
                            ];
                        }
                    }

                    return \App\Support\TablePdfExporter::download(
                        filename: 'daftar-siswa-internal-' . now()->format('Y-m-d') . '.pdf',
                        title: 'Daftar Siswa Internal — Survival English',
                        subtitle: $this->search ? 'Filter pencarian: "' . $this->search . '"' : 'Jenjang SD/MI, SMP/MTs, dan SMA/MA',
                        headers: ['No', 'NIS', 'Nama Siswa', 'Kelas', 'L/P', 'No. HP', 'Orang Tua / WA', 'Status'],
                        rows: $rows,
                        summary: [
                            'Total Siswa' => $data['total'] . ' siswa',
                            'Aktif' => $data['active'] . ' siswa',
                        ],
                    );
                }),
            Actions\ExportAction::make()->exporter(\App\Filament\Exports\StudentExporter::class),
            Actions\CreateAction::make()
                ->label(__('Add Student'))
                ->slideOver()
                ->modalWidth('4xl')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['student_type'] = 'internal';
                    $data['course_level'] = 'Survival English';
                    return $data;
                }),
        ];
    }
}
