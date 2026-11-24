<?php

namespace App\Filament\Resources\HR\StudentResource\Pages;

use App\Filament\Resources\HR\StudentResource;
use App\Models\Student;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected string $view = 'filament.admin.pages.internal-students-accordion';

    public string $search = '';

    /** @var array<int, string> */
    public array $openGroups = [];

    public ?int $editingId = null;

    /** @var array<string, mixed> */
    public array $editForm = [];

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

        $s = Student::find($id);
        if (!$s) {
            return;
        }

        $this->editingId = $s->id;
        $this->editForm = [
            'name' => (string) $s->name,
            'nis' => (string) ($s->nis ?? ''),
            'class_name' => StudentResource::resolveClassLabel($s),
            'gender' => (string) ($s->gender ?: 'L'),
            'phone' => (string) ($s->phone ?? ''),
            'father_name' => (string) ($s->father_name ?? ''),
            'father_phone' => (string) ($s->father_phone ?? ''),
            'mother_name' => (string) ($s->mother_name ?? ''),
            'mother_phone' => (string) ($s->mother_phone ?? ''),
            'is_active' => (bool) $s->is_active,
        ];
    }

    public function cancelInlineEdit(): void
    {
        $this->editingId = null;
        $this->editForm = [];
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

        $s->update([
            'name' => trim((string) ($this->editForm['name'] ?? $s->name)),
            'nis' => trim((string) ($this->editForm['nis'] ?? $s->nis)),
            'class_name' => trim((string) ($this->editForm['class_name'] ?? '')) ?: null,
            'gender' => (string) ($this->editForm['gender'] ?? 'L'),
            'phone' => trim((string) ($this->editForm['phone'] ?? '')) ?: null,
            'father_name' => trim((string) ($this->editForm['father_name'] ?? '')) ?: null,
            'father_phone' => trim((string) ($this->editForm['father_phone'] ?? '')) ?: null,
            'mother_name' => trim((string) ($this->editForm['mother_name'] ?? '')) ?: null,
            'mother_phone' => trim((string) ($this->editForm['mother_phone'] ?? '')) ?: null,
            'parent_name' => trim((string) ($this->editForm['father_name'] ?? '')) ?: trim((string) ($this->editForm['mother_name'] ?? '')) ?: null,
            'parent_phone' => trim((string) ($this->editForm['father_phone'] ?? '')) ?: trim((string) ($this->editForm['mother_phone'] ?? '')) ?: null,
            'is_active' => (bool) ($this->editForm['is_active'] ?? true),
        ]);

        Notification::make()->title('Data siswa internal diperbarui')->success()->send();
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

    public function getGroupedStudentsProperty(): array
    {
        $needle = mb_strtolower(trim($this->search));

        $students = Student::query()
            ->with(['user:id,username', 'classes:id,name'])
            ->where(fn ($q) => $q->where('student_type', 'internal')->orWhereNull('student_type'))
            ->orderBy('class_name')
            ->orderBy('name')
            ->get();

        if ($needle !== '') {
            $students = $students->filter(function (Student $s) use ($needle): bool {
                $hay = implode(' ', [
                    $s->name, $s->nis, $s->phone, $s->father_name, $s->mother_name,
                    StudentResource::resolveClassLabel($s), $s->user?->username,
                ]);
                return str_contains(mb_strtolower($hay), $needle);
            });
        }

        $groups = [];
        foreach ($students as $s) {
            $label = StudentResource::resolveClassLabel($s);
            $groups[$label][] = $s;
        }
        ksort($groups, SORT_NATURAL);

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
                        title: 'Daftar Siswa Internal G-English',
                        subtitle: $this->search ? 'Filter pencarian: "' . $this->search . '"' : 'Seluruh kelas aktif',
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
                    return $data;
                }),
        ];
    }
}
