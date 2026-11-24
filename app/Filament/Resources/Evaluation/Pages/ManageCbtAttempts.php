<?php

namespace App\Filament\Resources\Evaluation\Pages;

use App\Filament\Resources\Evaluation\CbtAttemptResource;
use App\Filament\Resources\HR\StudentResource;
use App\Models\CbtAttempt;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageCbtAttempts extends ManageRecords
{
    protected static string $resource = CbtAttemptResource::class;

    protected string $view = 'filament.admin.pages.cbt-attempts-hierarchy';

    public string $search = '';
    public ?string $dateFrom = null;
    public ?string $dateUntil = null;

    /** @var array<int, string> */
    public array $openTypes = ['Internal', 'Eksternal'];

    /** @var array<int, string> */
    public array $openSchools = [];

    /** @var array<int, string> */
    public array $openClasses = [];

    public ?int $editingId = null;

    /** @var array<string, mixed> */
    public array $editForm = [];

    public function toggleType(string $type): void
    {
        $this->openTypes = in_array($type, $this->openTypes, true)
            ? array_values(array_diff($this->openTypes, [$type]))
            : [...$this->openTypes, $type];
    }

    public function toggleSchool(string $key): void
    {
        $this->openSchools = in_array($key, $this->openSchools, true)
            ? array_values(array_diff($this->openSchools, [$key]))
            : [...$this->openSchools, $key];
    }

    public function toggleClass(string $key): void
    {
        $this->openClasses = in_array($key, $this->openClasses, true)
            ? array_values(array_diff($this->openClasses, [$key]))
            : [...$this->openClasses, $key];
    }

    public function startInlineEdit(int $id): void
    {
        if ($this->editingId === $id) {
            $this->cancelInlineEdit();
            return;
        }
        $a = CbtAttempt::find($id);
        if (!$a) {
            return;
        }
        $this->editingId = $a->id;
        $this->editForm = [
            'score' => $a->score,
            'status' => (string) $a->status,
            'violation_count' => (int) ($a->violation_count ?? 0),
            'unlock_mode' => 'lanjut',
            'extra_minutes' => 15,
        ];
    }

    public function cancelInlineEdit(): void
    {
        $this->editingId = null;
        $this->editForm = [];
    }

    public function saveInlineEdit(): void
    {
        if (!$this->editingId) return;
        $a = CbtAttempt::find($this->editingId);
        if (!$a) return;

        $a->update([
            'score' => $this->editForm['score'] !== '' && $this->editForm['score'] !== null ? (float) $this->editForm['score'] : null,
            'status' => (string) ($this->editForm['status'] ?? $a->status),
            'violation_count' => max(0, (int) ($this->editForm['violation_count'] ?? 0)),
        ]);

        Notification::make()->title('Data CBT attempt diperbarui')->success()->send();
        $this->cancelInlineEdit();
    }

    public function unlockAttempt(int $id): void
    {
        $a = CbtAttempt::with('exam')->find($id);
        if (!$a) return;

        $mode = (string) ($this->editForm['unlock_mode'] ?? 'lanjut');
        $extra = max(1, (int) ($this->editForm['extra_minutes'] ?? 15));

        $update = [
            'is_completed' => false,
            'is_force_submitted' => false,
            'status' => 'in_progress',
            'violation_count' => 0,
            'score' => null,
        ];

        if ($mode === 'reset') {
            $update['start_time'] = now();
            $update['end_time'] = now()->addMinutes($a->exam?->duration_minutes ?? 60);
        } elseif ($mode === 'tambah') {
            $base = ($a->end_time && now()->lessThan($a->end_time)) ? $a->end_time->copy() : now();
            $update['end_time'] = $base->addMinutes($extra);
        }

        $a->update($update);
        Notification::make()->title('Ujian siswa berhasil dibuka kuncinya')->success()->send();
        $this->cancelInlineEdit();
    }

    public function deleteRecord(int $id): void
    {
        $a = CbtAttempt::find($id);
        if ($a) {
            $a->delete();
            Notification::make()->title('Attempt dihapus / direset')->success()->send();
        }
        if ($this->editingId === $id) {
            $this->cancelInlineEdit();
        }
    }

    public function getHierarchyProperty(): array
    {
        $needle = mb_strtolower(trim($this->search));
        $query = CbtAttempt::query()
            ->with(['exam:id,title', 'student.classes:id,name'])
            ->orderBy('created_at', 'desc');

        if ($this->dateFrom) {
            $query->whereDate('start_time', '>=', $this->dateFrom);
        }
        if ($this->dateUntil) {
            $query->whereDate('start_time', '<=', $this->dateUntil);
        }

        $attempts = $query->get();
        if ($needle !== '') {
            $attempts = $attempts->filter(function (CbtAttempt $a) use ($needle): bool {
                $s = $a->student;
                $hay = implode(' ', [
                    $a->exam?->title, $s?->name, $s?->nis, $s?->school_name,
                    $s ? StudentResource::resolveClassLabel($s) : '', $a->status,
                ]);
                return str_contains(mb_strtolower($hay), $needle);
            });
        }

        $tree = [
            'Internal' => ['schools' => [], 'count' => 0],
            'Eksternal' => ['schools' => [], 'count' => 0],
        ];

        foreach ($attempts as $a) {
            $s = $a->student;
            $type = ($s?->student_type === 'external') ? 'Eksternal' : 'Internal';
            $school = $type === 'Eksternal'
                ? (trim((string) ($s?->school_name ?? '')) ?: 'Tanpa Sekolah')
                : 'G-English Internal';
            $class = $s ? StudentResource::resolveClassLabel($s) : 'Tanpa Kelas';

            $tree[$type]['schools'][$school][$class][] = $a;
            $tree[$type]['count']++;
        }

        foreach ($tree as &$tData) {
            ksort($tData['schools'], SORT_NATURAL);
            foreach ($tData['schools'] as &$classes) {
                ksort($classes, SORT_NATURAL);
            }
        }

        return [
            'types' => $tree,
            'total' => $attempts->count(),
            'locked' => $attempts->where('is_force_submitted', true)->count(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $h = $this->hierarchy;
                    $rows = [];
                    $no = 1;
                    foreach ($h['types'] as $type => $tData) {
                        foreach ($tData['schools'] as $school => $classes) {
                            foreach ($classes as $cls => $list) {
                                foreach ($list as $a) {
                                    $rows[] = [
                                        $no++,
                                        $type,
                                        $school,
                                        $cls,
                                        $a->student?->name ?? '-',
                                        $a->exam?->title ?? '-',
                                        strtoupper($a->status),
                                        ($a->violation_count ?? 0) . '/' . ($a->max_violations ?? 3),
                                        $a->score ?? '-',
                                    ];
                                }
                            }
                        }
                    }

                    return \App\Support\TablePdfExporter::download(
                        filename: 'exam-monitoring-' . now()->format('Y-m-d') . '.pdf',
                        title: 'Laporan Monitoring & Hasil Ujian CBT',
                        subtitle: \App\Support\TablePdfExporter::periodSubtitle($this->dateFrom, $this->dateUntil),
                        headers: ['No', 'Tipe', 'Sekolah', 'Kelas', 'Siswa', 'Ujian', 'Status', 'Pelanggaran', 'Skor'],
                        rows: $rows,
                        summary: [
                            'Total Peserta' => $h['total'],
                            'Terkunci / Force Submit' => $h['locked'],
                        ],
                    );
                }),
            CreateAction::make()->slideOver()->modalWidth('2xl'),
        ];
    }
}
