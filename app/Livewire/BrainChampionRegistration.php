<?php

namespace App\Livewire;

use App\Mail\SchoolAccountCredentialsMail;
use App\Models\Admission;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

#[Layout('brain-champion')]
class BrainChampionRegistration extends Component
{
    use WithFileUploads;

    public const EVENT_TAG = 'G-English Brain Champion';
    public const MAX_QUOTA_PER_GRADE = 35;

    public string $school_level = 'SD'; // 'SD' (Grades 4,5,6) or 'SMP' (Grades 7,8,9)
    public string $school_name = '';
    public string $pic_name = '';
    public string $pic_phone = '';
    public string $school_email = '';
    public string $school_password = '';

    public $excelFile = null;
    public ?string $excelNotice = null;
    public string $excelNoticeType = 'success';

    public string $lastSubmittedSchool = '';
    public string $lastSubmittedPicName = '';
    public string $lastSubmittedPicPhone = '';
    public string $lastSubmittedEmail = '';
    public string $lastSubmittedAt = '';

    /**
     * School login credentials generated/updated on submission.
     *
     * @var array{login_url?: string, username?: string, password?: string, email?: string, school_name?: string, pic_name?: string}
     */
    public array $schoolCredentials = [];

    /**
     * Participants keyed by grade ('4','5','6','7','8','9') -> slot index (0..1 for SD, 0 for SMP)
     *
     * @var array<int|string, array<int, array{nisn: string, name: string, gender: string, father_name: string, father_phone: string, mother_name: string, mother_phone: string, parent_name: string, parent_phone: string, phone: string}>>
     */
    public array $participants = [];

    /**
     * Newly registered participants summary from current session submission
     *
     * @var array<int, array{registration_number: string, competition_code: string, nisn: string, name: string, gender: string, parent_name: string, parent_phone: string, phone: string, grade: int, class_name: string, school_name: string, username: string|null}>
     */
    public array $registeredResults = [];

    public function mount(): void
    {
        $this->initializeParticipantSlots();
    }

    public function updatedSchoolLevel(): void
    {
        $this->resetValidation();
        $this->excelNotice = null;
        $this->initializeParticipantSlots();
    }

    public function updatedSchoolName(): void
    {
        $this->resetValidation();
    }

    protected function emptySlot(array $existing = []): array
    {
        return [
            'nisn' => (string) ($existing['nisn'] ?? ''),
            'name' => (string) ($existing['name'] ?? ''),
            'gender' => (string) ($existing['gender'] ?? 'L'),
            'father_name' => (string) ($existing['father_name'] ?? ''),
            'father_phone' => (string) ($existing['father_phone'] ?? ''),
            'mother_name' => (string) ($existing['mother_name'] ?? ''),
            'mother_phone' => (string) ($existing['mother_phone'] ?? ''),
            'parent_name' => (string) ($existing['parent_name'] ?? ''),
            'parent_phone' => (string) ($existing['parent_phone'] ?? ''),
            'phone' => (string) ($existing['phone'] ?? ''),
        ];
    }

    protected function initializeParticipantSlots(): void
    {
        $slots = [];
        foreach ([4, 5, 6] as $grade) {
            $slots[$grade] = [
                0 => $this->emptySlot($this->participants[$grade][0] ?? []),
                1 => $this->emptySlot($this->participants[$grade][1] ?? []),
            ];
        }
        foreach ([7, 8, 9] as $grade) {
            $slots[$grade] = [
                0 => $this->emptySlot($this->participants[$grade][0] ?? []),
            ];
        }
        $this->participants = $slots;
    }

    public function downloadExcelTemplate()
    {
        $isSmp = $this->school_level === 'SMP';
        $rows = $isSmp
            ? [
                ['Kelas 7', '', '', 'L', '', '', '', '', ''],
                ['Kelas 8', '', '', 'P', '', '', '', '', ''],
                ['Kelas 9', '', '', 'L', '', '', '', '', ''],
            ]
            : [
                ['Kelas 4', '', '', 'L', '', '', '', '', ''],
                ['Kelas 4', '', '', 'P', '', '', '', '', ''],
                ['Kelas 5', '', '', 'L', '', '', '', '', ''],
                ['Kelas 5', '', '', 'P', '', '', '', '', ''],
                ['Kelas 6', '', '', 'L', '', '', '', '', ''],
                ['Kelas 6', '', '', 'P', '', '', '', '', ''],
            ];

        $export = new class($rows) implements FromArray, WithHeadings, WithStyles, WithColumnWidths
        {
            public function __construct(protected array $rows) {}

            public function headings(): array
            {
                return [
                    'kelas',
                    'nisn',
                    'nama_siswa',
                    'jenis_kelamin_L_P',
                    'nama_ayah',
                    'no_hp_ayah',
                    'nama_ibu',
                    'no_hp_ibu',
                    'no_hp_siswa',
                ];
            }

            public function array(): array
            {
                return $this->rows;
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['argb' => 'FF0A4282'],
                        ],
                    ],
                ];
            }

            public function columnWidths(): array
            {
                return [
                    'A' => 14,
                    'B' => 16,
                    'C' => 28,
                    'D' => 18,
                    'E' => 24,
                    'F' => 18,
                    'G' => 24,
                    'H' => 18,
                    'I' => 18,
                ];
            }
        };

        $levelSlug = strtolower($this->school_level);
        return Excel::download($export, "template_brain_champion_{$levelSlug}.xlsx");
    }

    public function updatedExcelFile(): void
    {
        $this->excelNotice = null;

        $this->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'excelFile.mimes' => 'Format file harus berupa Excel (.xlsx, .xls) atau .csv.',
        ]);

        try {
            $sheets = Excel::toArray(
                new class implements ToArray, WithHeadingRow
                {
                    public function array(array $array): array
                    {
                        return $array;
                    }
                },
                $this->excelFile->getRealPath()
            );
        } catch (\Throwable $e) {
            $this->excelNoticeType = 'error';
            $this->excelNotice = 'Gagal membaca file Excel: pastikan menggunakan template resmi (.xlsx).';
            return;
        }

        $rows = $sheets[0] ?? [];
        $activeGrades = $this->activeGrades;
        $quotaSummary = $this->quotaSummary;

        // Clear current active grade inputs before auto-filling
        foreach ($activeGrades as $g) {
            $maxSlots = self::maxPerSchoolForGrade($g);
            for ($i = 0; $i < $maxSlots; $i++) {
                $this->participants[$g][$i] = $this->emptySlot();
            }
        }

        $nextSlotByGrade = array_fill_keys($activeGrades, 0);
        $filledCount = 0;
        $skippedReasons = [];

        foreach ($rows as $idx => $row) {
            $rowNum = $idx + 2;
            $nisn = preg_replace('/\s+/', '', trim((string) ($row['nisn'] ?? ''))) ?? '';
            $name = trim((string) ($row['nama_siswa'] ?? ''));

            if ($nisn === '' && $name === '') {
                continue;
            }

            $rawKelas = trim((string) ($row['kelas'] ?? ''));
            $grade = preg_match('/(\d+)/', $rawKelas, $m) ? (int) $m[1] : 0;

            if (!in_array($grade, $activeGrades, true)) {
                $skippedReasons[] = "Baris {$rowNum}: Kelas '{$rawKelas}' tidak sesuai jenjang {$this->school_level}";
                continue;
            }

            $q = $quotaSummary[$grade] ?? null;
            if (!$q || $q['is_full']) {
                $skippedReasons[] = "Baris {$rowNum} ({$name}): Kuota Kelas {$grade} sudah penuh (35/35)";
                continue;
            }

            $slotIdx = $nextSlotByGrade[$grade] ?? 0;
            if ($slotIdx >= $q['available_slots']) {
                $skippedReasons[] = "Baris {$rowNum} ({$name}): Melebihi sisa slot Kelas {$grade} ({$q['available_slots']} slot)";
                continue;
            }

            $rawGender = strtoupper(trim((string) ($row['jenis_kelamin_l_p'] ?? $row['jenis_kelamin_L_P'] ?? 'L')));
            $gender = in_array($rawGender, ['L', 'P'], true) ? $rawGender : 'L';
            $fatherName = trim((string) ($row['nama_ayah'] ?? ''));
            $fatherPhone = trim((string) ($row['no_hp_ayah'] ?? ''));
            $motherName = trim((string) ($row['nama_ibu'] ?? ''));
            $motherPhone = trim((string) ($row['no_hp_ibu'] ?? ''));
            $studentPhone = trim((string) ($row['no_hp_siswa'] ?? ''));

            $parentName = $motherName !== '' ? $motherName : $fatherName;
            $parentPhone = $motherPhone !== '' ? $motherPhone : $fatherPhone;

            $this->participants[$grade][$slotIdx] = [
                'nisn' => $nisn,
                'name' => $name,
                'gender' => $gender,
                'father_name' => $fatherName,
                'father_phone' => $fatherPhone,
                'mother_name' => $motherName,
                'mother_phone' => $motherPhone,
                'parent_name' => $parentName,
                'parent_phone' => $parentPhone,
                'phone' => $studentPhone,
            ];

            $nextSlotByGrade[$grade] = $slotIdx + 1;
            $filledCount++;
        }

        if ($filledCount > 0) {
            $this->excelNoticeType = 'success';
            $msg = "Berhasil mengisi otomatis {$filledCount} data peserta dari Excel! Silakan periksa kembali lalu klik tombol Daftar di bawah.";
            if (!empty($skippedReasons)) {
                $msg .= ' (Dilewati: ' . implode('; ', array_slice($skippedReasons, 0, 3)) . ')';
            }
            $this->excelNotice = $msg;
        } else {
            $this->excelNoticeType = 'warning';
            $this->excelNotice = !empty($skippedReasons)
                ? 'Tidak ada baris yang dapat diisi: ' . implode('; ', array_slice($skippedReasons, 0, 3))
                : 'File Excel masih kosong. Isi kolom nisn, nama_siswa, dan data orang tua terlebih dahulu.';
        }
    }

    /**
     * Return active grades for the currently selected school level.
     *
     * @return array<int>
     */
    public function getActiveGradesProperty(): array
    {
        return $this->school_level === 'SMP' ? [7, 8, 9] : [4, 5, 6];
    }

    public static function maxPerSchoolForGrade(int $grade): int
    {
        return in_array($grade, [4, 5, 6], true) ? 2 : 1;
    }

    public static function normalizeSchoolName(string $schoolName): string
    {
        return preg_replace('/\s+/', ' ', mb_strtoupper(trim($schoolName))) ?? '';
    }

    /**
     * Compute real-time quota status for all grades (4..9) and current school.
     *
     * @return array<int, array{grade: int, class_label: string, max_global: int, global_used: int, global_remaining: int, is_full: bool, max_per_school: int, school_used: int, school_remaining: int, available_slots: int}>
     */
    public function getQuotaSummaryProperty(): array
    {
        $normalizedSchool = self::normalizeSchoolName($this->school_name);

        $admissions = Admission::query()
            ->select(['class_name', 'school_name'])
            ->where('course_level', self::EVENT_TAG)
            ->where('status', '!=', 'rejected')
            ->get();

        $summary = [];
        foreach ([4, 5, 6, 7, 8, 9] as $grade) {
            $classLabel = "Kelas {$grade}";
            $maxPerSchool = self::maxPerSchoolForGrade($grade);

            $gradeRows = $admissions->filter(fn ($row) => trim((string) $row->class_name) === $classLabel);
            $globalUsed = $gradeRows->count();
            $globalRemaining = max(0, self::MAX_QUOTA_PER_GRADE - $globalUsed);

            $schoolUsed = 0;
            if ($normalizedSchool !== '') {
                $schoolUsed = $gradeRows->filter(
                    fn ($row) => self::normalizeSchoolName((string) $row->school_name) === $normalizedSchool
                )->count();
            }

            $schoolRemaining = max(0, $maxPerSchool - $schoolUsed);
            $availableSlots = min($globalRemaining, $schoolRemaining);

            $summary[$grade] = [
                'grade' => $grade,
                'class_label' => $classLabel,
                'max_global' => self::MAX_QUOTA_PER_GRADE,
                'global_used' => $globalUsed,
                'global_remaining' => $globalRemaining,
                'is_full' => $globalRemaining === 0,
                'max_per_school' => $maxPerSchool,
                'school_used' => $schoolUsed,
                'school_remaining' => $schoolRemaining,
                'available_slots' => $availableSlots,
            ];
        }

        return $summary;
    }

    public function submit(): void
    {
        $this->validate([
            'school_level' => 'required|in:SD,SMP',
            'school_name' => 'required|string|min:3|max:255',
            'pic_name' => 'required|string|min:2|max:255',
            'pic_phone' => 'required|string|min:8|max:25',
            'school_email' => 'required|email|max:255',
            'school_password' => 'nullable|string|min:4|max:50',
        ], [
            'school_name.required' => 'Nama sekolah wajib diisi.',
            'pic_name.required' => 'Nama Guru Pendamping wajib diisi.',
            'pic_phone.required' => 'Nomor WhatsApp Guru Pendamping wajib diisi.',
            'school_email.required' => 'Alamat Email Sekolah / Pendamping wajib diisi untuk pengiriman akun login.',
            'school_email.email' => 'Format alamat email tidak valid.',
            'school_password.min' => 'Password / PIN sekolah minimal 4 karakter.',
        ]);

        $activeGrades = $this->activeGrades;
        $cleanSchoolName = trim((string) preg_replace('/\s+/', ' ', $this->school_name));
        $normalizedSchool = self::normalizeSchoolName($cleanSchoolName);
        $coordinatorPhone = preg_replace('/\s+/', '', trim($this->pic_phone)) ?: trim($this->pic_phone);
        $cleanSchoolEmail = strtolower(trim($this->school_email));

        // Collect & validate non-empty participant entries from active grades
        $entriesByGrade = [];
        $validationErrors = [];
        $totalSubmitted = 0;

        foreach ($activeGrades as $grade) {
            $maxSlots = self::maxPerSchoolForGrade($grade);
            $gradeSlots = $this->participants[$grade] ?? [];

            for ($idx = 0; $idx < $maxSlots; $idx++) {
                $slot = $gradeSlots[$idx] ?? [];
                $nisn = preg_replace('/\s+/', '', trim((string) ($slot['nisn'] ?? ''))) ?? '';
                $name = trim((string) ($slot['name'] ?? ''));
                $fatherName = trim((string) ($slot['father_name'] ?? ''));
                $fatherPhone = trim((string) ($slot['father_phone'] ?? ''));
                $motherName = trim((string) ($slot['mother_name'] ?? ''));
                $motherPhone = trim((string) ($slot['mother_phone'] ?? ''));
                $parentName = trim((string) ($slot['parent_name'] ?? '')) ?: ($motherName !== '' ? $motherName : $fatherName);
                $parentPhone = trim((string) ($slot['parent_phone'] ?? '')) ?: ($motherPhone !== '' ? $motherPhone : $fatherPhone);
                $phone = trim((string) ($slot['phone'] ?? ''));
                $gender = in_array($slot['gender'] ?? 'L', ['L', 'P'], true) ? $slot['gender'] : 'L';

                $hasAnyField = ($name !== '' || $nisn !== '' || $parentName !== '' || $parentPhone !== '');
                if (!$hasAnyField) {
                    continue;
                }

                $slotLabel = "Kelas {$grade} (Peserta " . ($idx + 1) . ')';

                if ($nisn === '' || !preg_match('/^\d{10}$/', $nisn)) {
                    $validationErrors["participants.{$grade}.{$idx}.nisn"] = "NISN {$slotLabel} wajib diisi 10 digit angka.";
                }
                if ($name === '') {
                    $validationErrors["participants.{$grade}.{$idx}.name"] = "Nama Lengkap Siswa {$slotLabel} wajib diisi.";
                }
                if ($parentName === '') {
                    $validationErrors["participants.{$grade}.{$idx}.parent_name"] = "Nama Orang Tua (Ibu / Ayah) {$slotLabel} wajib diisi.";
                }
                if ($parentPhone === '') {
                    $validationErrors["participants.{$grade}.{$idx}.parent_phone"] = "No. HP Orang Tua (Ibu / Ayah) {$slotLabel} wajib diisi.";
                }

                $entriesByGrade[$grade][] = [
                    'slot_index' => $idx,
                    'nisn' => $nisn,
                    'name' => $name,
                    'gender' => $gender,
                    'father_name' => $fatherName ?: null,
                    'father_phone' => $fatherPhone ?: null,
                    'mother_name' => $motherName ?: $parentName,
                    'mother_phone' => $motherPhone ?: $parentPhone,
                    'parent_name' => $parentName,
                    'parent_phone' => $parentPhone,
                    'phone' => $phone !== '' ? $phone : $parentPhone,
                ];
                $totalSubmitted++;
            }
        }

        if (!empty($validationErrors)) {
            throw ValidationException::withMessages($validationErrors);
        }

        if ($totalSubmitted === 0) {
            throw ValidationException::withMessages([
                'participants' => 'Minimal isi 1 peserta lengkap (NISN 10 digit, Nama Siswa, Nama & No. HP Orang Tua) pada kelas yang masih tersedia.',
            ]);
        }

        $plainPassword = trim($this->school_password) !== ''
            ? trim($this->school_password)
            : Str::upper(Str::random(6));

        $createdRecords = DB::transaction(function () use ($entriesByGrade, $cleanSchoolName, $normalizedSchool, $coordinatorPhone, $cleanSchoolEmail, $plainPassword) {
            $lockedRows = Admission::query()
                ->where('course_level', self::EVENT_TAG)
                ->where('status', '!=', 'rejected')
                ->lockForUpdate()
                ->get();

            $resolvedEmail = $cleanSchoolEmail;

            $schoolUser = User::where('role', 'school')
                ->get()
                ->first(fn (User $u): bool => self::normalizeSchoolName((string) $u->name) === $normalizedSchool);

            if (!$schoolUser && User::where('email', $resolvedEmail)->exists()) {
                $schoolUser = User::where('email', $resolvedEmail)->first();
            }

            if (!$schoolUser) {
                $maxSeq = User::where('role', 'school')
                    ->whereNotNull('username')
                    ->pluck('username')
                    ->map(function ($u): int {
                        return preg_match('/^48(\d{3})$/', (string) $u, $m) ? (int) $m[1] : 0;
                    })
                    ->max() ?? 0;

                $nextSeq = $maxSeq + 1;
                do {
                    $schoolUsername = '48' . str_pad((string) $nextSeq, 3, '0', STR_PAD_LEFT);
                    $nextSeq++;
                } while (User::where('username', $schoolUsername)->exists());

                $schoolUser = User::create([
                    'username' => $schoolUsername,
                    'name' => $cleanSchoolName,
                    'email' => $resolvedEmail,
                    'password' => Hash::make($plainPassword),
                    'role' => 'school',
                ]);
            } else {
                $updates = ['password' => Hash::make($plainPassword)];
                if (in_array($schoolUser->role, ['parent', 'school'], true)) {
                    $updates['name'] = $cleanSchoolName;
                    $updates['role'] = 'school';
                }
                if (!User::where('email', $resolvedEmail)->where('id', '!=', $schoolUser->id)->exists()) {
                    $updates['email'] = $resolvedEmail;
                }
                $schoolUser->update($updates);
            }

            $results = [];

            foreach ($entriesByGrade as $grade => $gradeEntries) {
                $classLabel = "Kelas {$grade}";
                $maxPerSchool = self::maxPerSchoolForGrade((int) $grade);
                $requestedCount = count($gradeEntries);

                $gradeRows = $lockedRows->filter(fn ($row) => trim((string) $row->class_name) === $classLabel);
                $globalUsed = $gradeRows->count();
                $globalRemaining = max(0, self::MAX_QUOTA_PER_GRADE - $globalUsed);

                if ($requestedCount > $globalRemaining) {
                    throw ValidationException::withMessages([
                        "participants.{$grade}" => "Kuota {$classLabel} tersisa {$globalRemaining} kursi (Maksimal 35 siswa per kelas).",
                    ]);
                }

                $schoolUsed = $gradeRows->filter(
                    fn ($row) => self::normalizeSchoolName((string) $row->school_name) === $normalizedSchool
                )->count();

                $schoolRemaining = max(0, $maxPerSchool - $schoolUsed);

                if ($requestedCount > $schoolRemaining) {
                    throw ValidationException::withMessages([
                        "participants.{$grade}" => "Sekolah {$cleanSchoolName} hanya dapat mendaftarkan maksimal {$maxPerSchool} siswa untuk {$classLabel} (Sisa kuota sekolah Anda: {$schoolRemaining}).",
                    ]);
                }

                foreach ($gradeEntries as $entry) {
                    $regCode = $this->generateUniqueBrainChampionCode((int) $grade);

                    $admission = Admission::create([
                        'registration_number' => $regCode,
                        'nisn' => $entry['nisn'],
                        'competition_code' => $regCode,
                        'student_type' => 'external',
                        'school_user_id' => $schoolUser->id,
                        'name' => $entry['name'],
                        'gender' => $entry['gender'],
                        'phone' => $entry['phone'],
                        'education_level' => $this->school_level,
                        'course_level' => self::EVENT_TAG,
                        'class_name' => $classLabel,
                        'school_name' => $cleanSchoolName,
                        'previous_school' => $cleanSchoolName,
                        'father_name' => $entry['father_name'],
                        'father_phone' => $entry['father_phone'],
                        'mother_name' => $entry['mother_name'],
                        'mother_phone' => $entry['mother_phone'],
                        'parent_name' => $entry['parent_name'],
                        'parent_phone' => $entry['parent_phone'],
                        'status' => 'approved',
                        'tuition_fee' => 0,
                        'registration_fee' => 0,
                        'has_discount' => false,
                        'total_fee' => 0,
                        'amount_paid' => 0,
                        'notes' => self::EVENT_TAG . " (One Mind, One Champion) — {$classLabel} — NISN: {$entry['nisn']} — Sekolah: {$cleanSchoolName} — Pendamping: {$this->pic_name} ({$coordinatorPhone})",
                    ]);

                    $student = Student::create([
                        'school_user_id' => $schoolUser->id,
                        'nis' => $regCode,
                        'nisn' => $entry['nisn'],
                        'competition_code' => $regCode,
                        'student_type' => 'external',
                        'name' => $entry['name'],
                        'gender' => $entry['gender'],
                        'phone' => $entry['phone'],
                        'education_level' => $this->school_level,
                        'course_level' => self::EVENT_TAG,
                        'class_name' => $classLabel,
                        'school_name' => $cleanSchoolName,
                        'father_name' => $entry['father_name'],
                        'father_phone' => $entry['father_phone'],
                        'mother_name' => $entry['mother_name'],
                        'mother_phone' => $entry['mother_phone'],
                        'parent_name' => $entry['parent_name'],
                        'parent_phone' => $entry['parent_phone'],
                        'is_active' => true,
                    ]);

                    $student->parents()->syncWithoutDetaching([$schoolUser->id]);
                    $lockedRows->push($admission);

                    $results[] = [
                        'registration_number' => $regCode,
                        'competition_code' => $regCode,
                        'nisn' => $entry['nisn'],
                        'name' => $student->name,
                        'gender' => $entry['gender'],
                        'parent_name' => $entry['parent_name'],
                        'parent_phone' => $entry['parent_phone'],
                        'phone' => $entry['phone'],
                        'grade' => (int) $grade,
                        'class_name' => $classLabel,
                        'school_name' => $cleanSchoolName,
                        'username' => $student->user?->username,
                    ];
                }
            }

            return $results;
        });

        $this->lastSubmittedSchool = $cleanSchoolName;
        $this->lastSubmittedPicName = trim($this->pic_name);
        $this->lastSubmittedPicPhone = $coordinatorPhone;
        $this->lastSubmittedEmail = $cleanSchoolEmail;
        $this->lastSubmittedAt = now()->translatedFormat('d F Y, H:i') . ' WITA';
        $this->schoolCredentials = [
            'login_url' => route('login'),
            'username' => $coordinatorPhone,
            'password' => $plainPassword,
            'email' => $cleanSchoolEmail,
            'school_name' => $cleanSchoolName,
            'pic_name' => trim($this->pic_name),
        ];
        $this->registeredResults = $createdRecords;

        if ($cleanSchoolEmail !== '') {
            Mail::to($cleanSchoolEmail)->queue(new SchoolAccountCredentialsMail(
                schoolName: $cleanSchoolName,
                coordinatorName: trim($this->pic_name),
                loginUrl: url('/login'),
                username: $coordinatorPhone,
                plainPassword: $plainPassword,
                participants: $createdRecords,
            ));
        }

        $this->excelNotice = null;
        $this->participants = [];
        $this->initializeParticipantSlots();
    }

    protected function generateUniqueBrainChampionCode(int $grade): string
    {
        $prefix = "BC-K{$grade}-";

        $existingCodes = Admission::query()
            ->where('registration_number', 'like', "{$prefix}%")
            ->pluck('registration_number')
            ->merge(
                Student::withTrashed()
                    ->where('nis', 'like', "{$prefix}%")
                    ->pluck('nis')
            );

        $maxSeq = 0;
        foreach ($existingCodes as $code) {
            if (preg_match('/^BC-K' . $grade . '-(\d+)$/', (string) $code, $m)) {
                $num = (int) $m[1];
                if ($num > $maxSeq) {
                    $maxSeq = $num;
                }
            }
        }

        $nextSeq = $maxSeq + 1;
        do {
            $candidate = $prefix . str_pad((string) $nextSeq, 3, '0', STR_PAD_LEFT);
            $exists = Admission::where('registration_number', $candidate)->exists()
                || Student::withTrashed()->where('nis', $candidate)->exists();
            $nextSeq++;
        } while ($exists);

        return $candidate;
    }

    public function resetRegistrationSuccess(): void
    {
        $this->registeredResults = [];
        $this->schoolCredentials = [];
    }

    public function render()
    {
        return view('livewire.brain-champion-registration', [
            'quotaSummary' => $this->quotaSummary,
            'activeGrades' => $this->activeGrades,
        ]);
    }
}
