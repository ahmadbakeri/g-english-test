<?php

namespace App\Filament\Resources\HR\Evaluations\Pages;

use App\Filament\Resources\HR\Evaluations\EvaluationResource;
use App\Filament\Resources\HR\StudentResource;
use App\Models\Evaluation;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListEvaluations extends ListRecords
{
    protected static string $resource = EvaluationResource::class;

    protected string $view = 'filament.admin.pages.evaluations-hierarchy';

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
        $ev = Evaluation::find($id);
        if (!$ev) return;

        $this->editingId = $ev->id;
        $this->editForm = [
            'score' => $ev->score,
            'date' => $ev->date ? \Carbon\Carbon::parse($ev->date)->format('Y-m-d') : '',
            'notes' => (string) ($ev->notes ?? ''),
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
        $ev = Evaluation::find($this->editingId);
        if (!$ev) return;

        $ev->update([
            'score' => (int) ($this->editForm['score'] ?? $ev->score),
            'date' => $this->editForm['date'] ?: $ev->date,
            'notes' => trim((string) ($this->editForm['notes'] ?? '')) ?: null,
        ]);

        Notification::make()->title('Nilai evaluasi diperbarui')->success()->send();
        $this->cancelInlineEdit();
    }

    public function deleteRecord(int $id): void
    {
        $ev = Evaluation::find($id);
        if ($ev) {
            $ev->delete();
            Notification::make()->title('Data evaluasi dihapus')->success()->send();
        }
        if ($this->editingId === $id) {
            $this->cancelInlineEdit();
        }
    }

    public function getHierarchyProperty(): array
    {
        $needle = mb_strtolower(trim($this->search));
        $query = Evaluation::query()
            ->with(['student.classes:id,name', 'subject:id,name'])
            ->orderBy('date', 'desc');

        if ($this->dateFrom) {
            $query->whereDate('date', '>=', $this->dateFrom);
        }
        if ($this->dateUntil) {
            $query->whereDate('date', '<=', $this->dateUntil);
        }

        $items = $query->get();
        if ($needle !== '') {
            $items = $items->filter(function (Evaluation $ev) use ($needle): bool {
                $s = $ev->student;
                $hay = implode(' ', [
                    $s?->name, $s?->nis, $s?->school_name,
                    $s ? StudentResource::resolveClassLabel($s) : '',
                    $ev->subject?->name, $ev->notes,
                ]);
                return str_contains(mb_strtolower($hay), $needle);
            });
        }

        $tree = [
            'Internal' => ['schools' => [], 'count' => 0],
            'Eksternal' => ['schools' => [], 'count' => 0],
        ];

        foreach ($items as $ev) {
            $s = $ev->student;
            $type = ($s?->student_type === 'external') ? 'Eksternal' : 'Internal';
            $school = $type === 'Eksternal'
                ? (trim((string) ($s?->school_name ?? '')) ?: 'Tanpa Sekolah')
                : 'G-English Internal';
            $class = $s ? StudentResource::resolveClassLabel($s) : 'Tanpa Kelas';

            $tree[$type]['schools'][$school][$class][] = $ev;
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
            'total' => $items->count(),
            'avg' => $items->count() ? round((float) $items->avg('score'), 1) : 0,
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
                                foreach ($list as $ev) {
                                    $rows[] = [
                                        $no++,
                                        $type,
                                        $school,
                                        $cls,
                                        $ev->student?->name ?? '-',
                                        $ev->subject?->name ?? '-',
                                        $ev->date ? \Carbon\Carbon::parse($ev->date)->format('d/m/Y') : '-',
                                        $ev->score,
                                        $ev->notes ?: '-',
                                    ];
                                }
                            }
                        }
                    }

                    return \App\Support\TablePdfExporter::download(
                        filename: 'rekap-evaluasi-nilai-' . now()->format('Y-m-d') . '.pdf',
                        title: 'Rekap Nilai & Evaluasi Siswa',
                        subtitle: \App\Support\TablePdfExporter::periodSubtitle($this->dateFrom, $this->dateUntil),
                        headers: ['No', 'Tipe', 'Sekolah', 'Kelas', 'Siswa', 'Mata Pelajaran', 'Tanggal', 'Skor', 'Catatan'],
                        rows: $rows,
                        summary: [
                            'Total Evaluasi' => $h['total'],
                            'Rata-rata Skor' => $h['avg'],
                        ],
                    );
                }),
            \Filament\Actions\ExportAction::make()->exporter(\App\Filament\Exports\EvaluationExporter::class),
            CreateAction::make()->slideOver()->modalWidth('2xl'),
        ];
    }
}
