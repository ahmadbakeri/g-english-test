<?php

namespace App\Filament\Resources\HR\ExternalStudentResource\Pages;

use App\Filament\Resources\HR\ExternalStudentResource;
use App\Livewire\BrainChampionRegistration;
use App\Models\Student;
use App\Models\User;
use App\Support\FastTablePdfWriter;
use App\Support\SchoolAgencyClassifier;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;

class ListExternalStudents extends ListRecords
{
    protected static string $resource = ExternalStudentResource::class;

    protected string $view = 'filament.admin.pages.external-students-hierarchy';

    public const PER_CLASS_STEP = 25;
    public const PDF_CHUNK_SIZE = 1000;

    public string $search = '';

    /** 'all' | 'brain_champion' | 'non_bc' */
    public string $programFilter = 'all';

    /** @var array<int, string> */
    public array $openLevels = [];

    /** @var array<int, string> */
    public array $openSchools = [];

    /** @var array<int, string> */
    public array $openClasses = [];

    /** @var array<string, int> */
    public array $classLimits = [];

    public ?int $editingStudentId = null;

    /** @var array<string, mixed> */
    public array $editForm = [];

    // Chunked Streaming PDF Export Progress State
    public ?string $pdfJobId = null;
    public string $pdfStatus = 'idle'; // idle | running | done | error
    public int $pdfTotal = 0;
    public int $pdfProcessed = 0;
    public int $pdfPercent = 0;
    public int $pdfPagesDone = 0;
    public int $pdfTotalPages = 0;
    public string $pdfFileSize = '';
    public string $pdfElapsed = '';
    public string $pdfError = '';

    /** Per-request memoization for lazy class queries */
    protected array $schoolClassesMemo = [];

    /** Per-request memoization for lazy student slices */
    protected array $classStudentsMemo = [];

    public function updatedSearch(): void
    {
        $this->openClasses = [];
        $this->classLimits = [];
        $this->editingStudentId = null;
    }

    public function setProgramFilter(string $filter): void
    {
        if (!in_array($filter, ['all', 'brain_champion', 'non_bc'], true)) {
            $filter = 'all';
        }
        $this->programFilter = $filter;
        $this->openClasses = [];
        $this->classLimits = [];
        $this->editingStudentId = null;
    }

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
            unset($this->classLimits[$classKey]);
        } else {
            $this->openClasses[] = $classKey;
            $this->classLimits[$classKey] = self::PER_CLASS_STEP;
        }
    }

    public function loadMoreClass(string $classKey): void
    {
        $current = (int) ($this->classLimits[$classKey] ?? self::PER_CLASS_STEP);
        $this->classLimits[$classKey] = $current + self::PER_CLASS_STEP;
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
            'course_level' => (string) ($student->course_level ?: 'Eksternal Umum'),
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
            'course_level' => trim((string) ($this->editForm['course_level'] ?? '')) ?: null,
            'gender' => (string) ($this->editForm['gender'] ?? 'L'),
            'phone' => trim((string) ($this->editForm['phone'] ?? '')) ?: null,
            'parent_name' => trim((string) ($this->editForm['parent_name'] ?? '')) ?: null,
            'parent_phone' => trim((string) ($this->editForm['parent_phone'] ?? '')) ?: null,
            'is_active' => (bool) ($this->editForm['is_active'] ?? true),
        ]);

        self::flushSummaryCache();

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
            self::flushSummaryCache();
            Notification::make()
                ->title('Siswa dihapus')
                ->success()
                ->send();
        }
        if ($this->editingStudentId === $studentId) {
            $this->cancelInlineEdit();
        }
    }

    public static function flushSummaryCache(): void
    {
        foreach (['all', 'brain_champion', 'non_bc', 'global'] as $k) {
            Cache::forget("ext_students:summary:v1:{$k}");
        }
    }

    /**
     * Scoped SQL query for external students respecting programFilter + search.
     */
    protected function baseQuery(bool $applyProgramFilter = true): Builder
    {
        $query = Student::query()->where('student_type', 'external');

        if ($applyProgramFilter) {
            if ($this->programFilter === 'brain_champion') {
                $query->where('course_level', BrainChampionRegistration::EVENT_TAG);
            } elseif ($this->programFilter === 'non_bc') {
                $query->where(function (Builder $q) {
                    $q->whereNull('course_level')
                        ->orWhere('course_level', '!=', BrainChampionRegistration::EVENT_TAG);
                });
            }
        }

        $needle = trim($this->search);
        if ($needle !== '') {
            $like = '%' . $needle . '%';
            $query->where(function (Builder $q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('nis', 'like', $like)
                    ->orWhere('nisn', 'like', $like)
                    ->orWhere('competition_code', 'like', $like)
                    ->orWhere('school_name', 'like', $like)
                    ->orWhere('class_name', 'like', $like)
                    ->orWhere('course_level', 'like', $like)
                    ->orWhereHas('user', fn (Builder $u) => $u->where('username', 'like', $like))
                    ->orWhereHas('schoolUser', fn (Builder $su) => $su->where('username', 'like', $like));
            });
        }

        return $query;
    }

    /**
     * Apply sargable WHERE filter for a normalized school's raw school_name values.
     */
    protected function applySchoolFilter(Builder $query, array $rawSchools): Builder
    {
        $hasEmpty = in_array('', $rawSchools, true) || in_array('Tanpa Sekolah', $rawSchools, true);
        $named = array_values(array_filter($rawSchools, fn ($s) => $s !== '' && $s !== 'Tanpa Sekolah'));

        return $query->where(function (Builder $q) use ($named, $hasEmpty) {
            if (!empty($named)) {
                $q->whereIn('school_name', $named);
            }
            if ($hasEmpty) {
                $method = empty($named) ? 'where' : 'orWhere';
                $q->{$method}(fn (Builder $sub) => $sub->whereNull('school_name')->orWhere('school_name', ''));
            }
        });
    }

    /**
     * Global tab counts (cached 60s in Redis when search is empty).
     */
    #[Computed]
    public function globalCounts(): array
    {
        $builder = function (): array {
            $row = $this->baseQuery(applyProgramFilter: false)
                ->selectRaw('COUNT(*) AS total_all, SUM(CASE WHEN course_level = ? THEN 1 ELSE 0 END) AS total_bc', [
                    BrainChampionRegistration::EVENT_TAG,
                ])
                ->first();

            $all = (int) ($row->total_all ?? 0);
            $bc = (int) ($row->total_bc ?? 0);

            return [
                'all' => $all,
                'brain_champion' => $bc,
                'non_bc' => max(0, $all - $bc),
            ];
        };

        if (trim($this->search) === '') {
            return Cache::remember('ext_students:summary:v1:global', 60, $builder);
        }

        return $builder();
    }

    /**
     * Level 1 & Level 2 Summary ONLY via SQL GROUP BY (0 student Eloquent rows hydrated!).
     */
    #[Computed]
    public function hierarchy(): array
    {
        $build = function (): array {
            $rows = $this->baseQuery()
                ->selectRaw(
                    "school_name, school_user_id, education_level, COUNT(*) AS student_count, SUM(CASE WHEN course_level = ? THEN 1 ELSE 0 END) AS bc_count",
                    [BrainChampionRegistration::EVENT_TAG]
                )
                ->groupBy('school_name', 'school_user_id', 'education_level')
                ->orderBy('school_name')
                ->get();

            $schoolUserIds = $rows->pluck('school_user_id')->filter()->unique()->values()->all();
            $schoolUsernames = !empty($schoolUserIds)
                ? User::whereIn('id', $schoolUserIds)->pluck('username', 'id')->all()
                : [];

            $levels = [
                'SD' => ['schools' => [], 'schoolCount' => 0, 'studentCount' => 0, 'bcCount' => 0],
                'MI' => ['schools' => [], 'schoolCount' => 0, 'studentCount' => 0, 'bcCount' => 0],
                'SMP' => ['schools' => [], 'schoolCount' => 0, 'studentCount' => 0, 'bcCount' => 0],
                'MTs' => ['schools' => [], 'schoolCount' => 0, 'studentCount' => 0, 'bcCount' => 0],
            ];

            $totalStudents = 0;
            $totalBc = 0;
            $schoolKeysSeen = [];

            foreach ($rows as $row) {
                $rawSchool = trim((string) ($row->school_name ?? ''));
                $displaySchool = $rawSchool !== '' ? $rawSchool : 'Tanpa Sekolah';
                $level = SchoolAgencyClassifier::classifyLevel($displaySchool, $row->education_level);
                $schoolKey = BrainChampionRegistration::normalizeSchoolName($displaySchool) ?: 'tanpa-sekolah';
                $cnt = (int) $row->student_count;
                $bcCnt = (int) $row->bc_count;

                if (!isset($levels[$level]['schools'][$schoolKey])) {
                    $levels[$level]['schools'][$schoolKey] = [
                        'key' => $schoolKey,
                        'name' => $displaySchool,
                        'rawSchools' => [],
                        'username' => null,
                        'studentCount' => 0,
                        'bcCount' => 0,
                    ];
                    $schoolKeysSeen[$level . '::' . $schoolKey] = true;
                }

                $rawKey = $rawSchool !== '' ? $rawSchool : 'Tanpa Sekolah';
                if (!in_array($rawKey, $levels[$level]['schools'][$schoolKey]['rawSchools'], true)) {
                    $levels[$level]['schools'][$schoolKey]['rawSchools'][] = $rawKey;
                }

                if (!$levels[$level]['schools'][$schoolKey]['username'] && $row->school_user_id && isset($schoolUsernames[$row->school_user_id])) {
                    $levels[$level]['schools'][$schoolKey]['username'] = $schoolUsernames[$row->school_user_id];
                }

                $levels[$level]['schools'][$schoolKey]['studentCount'] += $cnt;
                $levels[$level]['schools'][$schoolKey]['bcCount'] += $bcCnt;
                $levels[$level]['studentCount'] += $cnt;
                $levels[$level]['bcCount'] += $bcCnt;
                $totalStudents += $cnt;
                $totalBc += $bcCnt;
            }

            foreach ($levels as $lvl => &$lvlData) {
                usort($lvlData['schools'], fn ($a, $b) => strnatcasecmp($a['name'], $b['name']));
                $lvlData['schoolCount'] = count($lvlData['schools']);
            }
            unset($lvlData);

            return [
                'levels' => $levels,
                'totalStudents' => $totalStudents,
                'totalSchools' => count($schoolKeysSeen),
                'totalBc' => $totalBc,
                'totalNonBc' => max(0, $totalStudents - $totalBc),
            ];
        };

        if (trim($this->search) === '') {
            return Cache::remember("ext_students:summary:v1:{$this->programFilter}", 60, $build);
        }

        return $build();
    }

    /**
     * Lazy Level 3: fetch class breakdown ONLY when a school accordion is expanded.
     *
     * @return array<int, array{name: string, rawClasses: array<int, string>, total: int, bcCount: int}>
     */
    public function getSchoolClasses(string $schoolCompositeKey, array $rawSchools): array
    {
        if (isset($this->schoolClassesMemo[$schoolCompositeKey])) {
            return $this->schoolClassesMemo[$schoolCompositeKey];
        }

        $rows = $this->applySchoolFilter($this->baseQuery(), $rawSchools)
            ->selectRaw(
                "class_name, COUNT(*) AS total, SUM(CASE WHEN course_level = ? THEN 1 ELSE 0 END) AS bc_count",
                [BrainChampionRegistration::EVENT_TAG]
            )
            ->groupBy('class_name')
            ->get();

        $classes = [];
        foreach ($rows as $row) {
            $rawCls = trim((string) ($row->class_name ?? ''));
            $displayCls = $rawCls !== '' ? $rawCls : 'Tanpa Kelas';

            if (!isset($classes[$displayCls])) {
                $classes[$displayCls] = [
                    'name' => $displayCls,
                    'rawClasses' => [],
                    'total' => 0,
                    'bcCount' => 0,
                ];
            }

            $rawKey = $rawCls !== '' ? $rawCls : 'Tanpa Kelas';
            if (!in_array($rawKey, $classes[$displayCls]['rawClasses'], true)) {
                $classes[$displayCls]['rawClasses'][] = $rawKey;
            }

            $classes[$displayCls]['total'] += (int) $row->total;
            $classes[$displayCls]['bcCount'] += (int) $row->bc_count;
        }

        uksort($classes, 'strnatcasecmp');

        return $this->schoolClassesMemo[$schoolCompositeKey] = array_values($classes);
    }

    /**
     * Lazy Level 4: fetch paginated students ONLY when a class accordion is expanded.
     *
     * @return array{items: \Illuminate\Support\Collection<int, Student>, loaded: int, limit: int, hasMore: bool}
     */
    public function getClassStudents(string $classKey, array $rawSchools, array $rawClasses): array
    {
        if (isset($this->classStudentsMemo[$classKey])) {
            return $this->classStudentsMemo[$classKey];
        }

        $limit = max(self::PER_CLASS_STEP, (int) ($this->classLimits[$classKey] ?? self::PER_CLASS_STEP));

        $query = $this->applySchoolFilter($this->baseQuery(), $rawSchools)
            ->select([
                'id',
                'user_id',
                'school_user_id',
                'nis',
                'nisn',
                'competition_code',
                'name',
                'gender',
                'phone',
                'parent_name',
                'parent_phone',
                'father_name',
                'mother_name',
                'course_level',
                'class_name',
                'school_name',
                'is_active',
            ])
            ->with(['user:id,username']);

        $hasEmptyCls = in_array('', $rawClasses, true) || in_array('Tanpa Kelas', $rawClasses, true);
        $namedCls = array_values(array_filter($rawClasses, fn ($c) => $c !== '' && $c !== 'Tanpa Kelas'));

        $query->where(function (Builder $q) use ($namedCls, $hasEmptyCls) {
            if (!empty($namedCls)) {
                $q->whereIn('class_name', $namedCls);
            }
            if ($hasEmptyCls) {
                $method = empty($namedCls) ? 'where' : 'orWhere';
                $q->{$method}(fn (Builder $sub) => $sub->whereNull('class_name')->orWhere('class_name', ''));
            }
        });

        $fetched = $query
            ->orderBy('name')
            ->limit($limit + 1)
            ->get();

        $hasMore = $fetched->count() > $limit;
        $items = $hasMore ? $fetched->slice(0, $limit)->values() : $fetched;

        return $this->classStudentsMemo[$classKey] = [
            'items' => $items,
            'loaded' => $items->count(),
            'limit' => $limit,
            'hasMore' => $hasMore,
        ];
    }

    public function startPdfExport(): void
    {
        $this->cancelPdfExport();

        $total = $this->baseQuery()->count();
        if ($total === 0) {
            Notification::make()
                ->title('Tidak ada data siswa eksternal untuk diexport pada filter ini.')
                ->warning()
                ->send();
            return;
        }

        $bcTotal = (clone $this->baseQuery())
            ->where('course_level', BrainChampionRegistration::EVENT_TAG)
            ->count();
        $nonBcTotal = max(0, $total - $bcTotal);

        $jobId = (string) Str::uuid();
        $jobDir = storage_path("app/pdf-exports/{$jobId}");
        $filename = 'daftar-siswa-eksternal-' . now()->format('Y-m-d-His') . '.pdf';

        $filterLabel = match ($this->programFilter) {
            'brain_champion' => 'Program: Peserta Brain Champion',
            'non_bc' => 'Program: Eksternal Non-Brain Champion',
            default => 'Program: Semua Siswa Eksternal',
        };
        $subtitle = $filterLabel . ($this->search ? ' • Pencarian: "' . $this->search . '"' : ' • Seluruh jenjang SD, MI, SMP, MTs');

        $meta = FastTablePdfWriter::beginJob($jobDir, [
            'title' => 'Daftar Siswa Eksternal',
            'subtitle' => $subtitle,
            'headers' => ['No', 'Jenjang', 'Sekolah', 'Kelas', 'Program', 'Kode / User', 'Nama Siswa', 'Orang Tua / WA'],
            'colWeights' => [0.45, 0.55, 1.9, 0.72, 1.3, 1.05, 1.75, 1.65],
            'summary' => [
                'Total Siswa' => number_format($total, 0, ',', '.') . ' siswa',
                'Brain Champion' => number_format($bcTotal, 0, ',', '.') . ' peserta',
                'Eksternal Non-BC' => number_format($nonBcTotal, 0, ',', '.') . ' siswa',
            ],
            'orientation' => 'landscape',
            'totalRows' => $total,
        ]);

        Cache::put("pdf_export_job:{$jobId}", [
            'user_id' => (int) auth()->id(),
            'status' => 'running',
            'job_dir' => $jobDir,
            'filename' => $filename,
            'file_path' => $jobDir . DIRECTORY_SEPARATOR . $filename,
            'program_filter' => $this->programFilter,
            'search' => $this->search,
            'last_id' => 0,
            'row_no' => 0,
            'total' => $total,
            'processed' => 0,
            'total_pages' => (int) $meta['totalPages'],
            'started_at' => microtime(true),
        ], 1800);

        $this->pdfJobId = $jobId;
        $this->pdfStatus = 'running';
        $this->pdfTotal = $total;
        $this->pdfProcessed = 0;
        $this->pdfPercent = 0;
        $this->pdfPagesDone = 0;
        $this->pdfTotalPages = (int) $meta['totalPages'];
        $this->pdfFileSize = '';
        $this->pdfElapsed = '0,0 dtk';
        $this->pdfError = '';

        // Process the first chunk immediately so small exports finish in 1 click and large exports show live progress right away
        $this->processPdfChunk();
    }

    public function processPdfChunk(): void
    {
        if ($this->pdfStatus !== 'running' || !$this->pdfJobId) {
            return;
        }

        $cacheKey = "pdf_export_job:{$this->pdfJobId}";
        $job = Cache::get($cacheKey);
        if (!is_array($job) || ($job['status'] ?? '') !== 'running') {
            $this->pdfStatus = 'idle';
            return;
        }

        try {
            $query = Student::query()->where('student_type', 'external');

            $pf = (string) ($job['program_filter'] ?? 'all');
            if ($pf === 'brain_champion') {
                $query->where('course_level', BrainChampionRegistration::EVENT_TAG);
            } elseif ($pf === 'non_bc') {
                $query->where(function (Builder $q) {
                    $q->whereNull('course_level')
                        ->orWhere('course_level', '!=', BrainChampionRegistration::EVENT_TAG);
                });
            }

            $needle = trim((string) ($job['search'] ?? ''));
            if ($needle !== '') {
                $like = '%' . $needle . '%';
                $query->where(function (Builder $q) use ($like) {
                    $q->where('name', 'like', $like)
                        ->orWhere('nis', 'like', $like)
                        ->orWhere('nisn', 'like', $like)
                        ->orWhere('competition_code', 'like', $like)
                        ->orWhere('school_name', 'like', $like)
                        ->orWhere('class_name', 'like', $like)
                        ->orWhere('course_level', 'like', $like)
                        ->orWhereHas('user', fn (Builder $u) => $u->where('username', 'like', $like))
                        ->orWhereHas('schoolUser', fn (Builder $su) => $su->where('username', 'like', $like));
                });
            }

            // O(1) keyset pagination on primary key (zero slow OFFSET)
            $students = $query
                ->select([
                    'id', 'user_id', 'nis', 'nisn', 'competition_code', 'name',
                    'school_name', 'education_level', 'class_name', 'course_level',
                    'parent_name', 'father_name', 'parent_phone', 'phone',
                ])
                ->with(['user:id,username'])
                ->where('id', '>', (int) $job['last_id'])
                ->orderBy('id')
                ->limit(self::PDF_CHUNK_SIZE)
                ->get();

            $rows = [];
            $rowNo = (int) $job['row_no'];
            $lastId = (int) $job['last_id'];

            foreach ($students as $s) {
                $rowNo++;
                $lastId = (int) $s->id;
                $lvl = SchoolAgencyClassifier::classifyLevel($s->school_name, $s->education_level, $s->class_name);
                $program = $s->course_level === BrainChampionRegistration::EVENT_TAG
                    ? 'Brain Champion'
                    : ($s->course_level ?: 'Eksternal Umum');

                $rows[] = [
                    $rowNo,
                    $lvl,
                    $s->school_name ?: 'Tanpa Sekolah',
                    $s->class_name ?: '-',
                    $program,
                    $s->user?->username ?: ($s->competition_code ?: ($s->nis ?: '-')),
                    $s->name,
                    ($s->parent_name ?: ($s->father_name ?: '-')) . ' / ' . ($s->parent_phone ?: ($s->phone ?: '-')),
                ];
            }

            $meta = FastTablePdfWriter::appendRows($job['job_dir'], $rows);

            $job['last_id'] = $lastId;
            $job['row_no'] = $rowNo;
            $job['processed'] = $rowNo;

            $elapsedSec = max(0.1, microtime(true) - (float) $job['started_at']);
            $this->pdfProcessed = $rowNo;
            $this->pdfPagesDone = (int) $meta['pagesWritten'];
            $this->pdfTotalPages = max($this->pdfTotalPages, (int) $meta['totalPages']);
            $this->pdfElapsed = number_format($elapsedSec, 1, ',', '.') . ' dtk';

            $isFinished = $students->count() < self::PDF_CHUNK_SIZE || $rowNo >= (int) $job['total'];

            if ($isFinished) {
                $result = FastTablePdfWriter::finalizeJob($job['job_dir'], $job['file_path']);
                $job['status'] = 'done';
                $job['pages'] = $result['pages'];
                $job['bytes'] = $result['bytes'];
                Cache::put($cacheKey, $job, 1800);

                $this->pdfStatus = 'done';
                $this->pdfPercent = 100;
                $this->pdfPagesDone = $result['pages'];
                $this->pdfTotalPages = $result['pages'];
                $this->pdfFileSize = $result['bytes'] >= 1048576
                    ? number_format($result['bytes'] / 1048576, 2, ',', '.') . ' MB'
                    : number_format(max(1, $result['bytes'] / 1024), 0, ',', '.') . ' KB';
                $this->pdfElapsed = number_format(max(0.1, microtime(true) - (float) $job['started_at']), 1, ',', '.') . ' dtk';

                $this->dispatch('gx-pdf-ready', url: route('admin.pdf-export.download', $this->pdfJobId));
            } else {
                $this->pdfPercent = min(99, (int) floor(($rowNo / max(1, (int) $job['total'])) * 100));
                Cache::put($cacheKey, $job, 1800);
            }
        } catch (\Throwable $e) {
            $this->pdfStatus = 'error';
            $this->pdfError = $e->getMessage();
            Cache::forget($cacheKey);
            FastTablePdfWriter::cleanupDir($job['job_dir'] ?? '');
        }
    }

    public function cancelPdfExport(): void
    {
        if ($this->pdfJobId) {
            $cacheKey = "pdf_export_job:{$this->pdfJobId}";
            $job = Cache::get($cacheKey);
            Cache::forget($cacheKey);
            if (is_array($job) && !empty($job['job_dir'])) {
                FastTablePdfWriter::cleanupDir((string) $job['job_dir']);
            }
        }

        $this->pdfJobId = null;
        $this->pdfStatus = 'idle';
        $this->pdfTotal = 0;
        $this->pdfProcessed = 0;
        $this->pdfPercent = 0;
        $this->pdfPagesDone = 0;
        $this->pdfTotalPages = 0;
        $this->pdfFileSize = '';
        $this->pdfElapsed = '';
        $this->pdfError = '';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(fn () => $this->startPdfExport()),
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
                        ->options(fn () => User::where('role', 'school')->pluck('name', 'id')->toArray())
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

                    $schoolUser = !empty($data['school_user_id']) ? User::find($data['school_user_id']) : null;
                    $importer = new \App\Imports\BrainChampionStudentImport(
                        schoolUserId: $schoolUser?->id,
                        schoolName: $schoolUser?->name ?: ($data['school_name'] ?? 'Sekolah Peserta'),
                    );
                    \Maatwebsite\Excel\Facades\Excel::import($importer, $filePath);
                    self::flushSummaryCache();

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
                })
                ->after(fn () => self::flushSummaryCache()),
        ];
    }
}
