<?php

namespace App\Livewire;

use App\Models\Admission;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('brain-champion')]
class BrainChampionRegistration extends Component
{
    public const EVENT_TAG = 'G-English Brain Champion';
    public const MAX_QUOTA_PER_GRADE = 35;

    public string $school_level = 'SD'; // 'SD' (Grades 4,5,6) or 'SMP' (Grades 7,8,9)
    public string $school_name = '';
    public string $pic_name = '';
    public string $pic_phone = '';
    public string $school_password = '';

    public string $lastSubmittedSchool = '';
    public string $lastSubmittedPicName = '';
    public string $lastSubmittedPicPhone = '';
    public string $lastSubmittedAt = '';

    /**
     * School login credentials generated/updated on submission.
     *
     * @var array{login_url?: string, username?: string, password?: string, school_name?: string, pic_name?: string}
     */
    public array $schoolCredentials = [];

    /**
     * Participants keyed by grade ('4','5','6','7','8','9') -> slot index (0..1 for SD, 0 for SMP)
     *
     * @var array<int|string, array<int, array{nisn: string, name: string, gender: string, parent_name: string, parent_phone: string, phone: string}>>
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
            'school_password' => 'nullable|string|min:4|max:50',
        ], [
            'school_name.required' => 'Nama sekolah wajib diisi.',
            'pic_name.required' => 'Nama Guru Pendamping wajib diisi.',
            'pic_phone.required' => 'Nomor WhatsApp Guru Pendamping wajib diisi.',
            'school_password.min' => 'Password / PIN sekolah minimal 4 karakter.',
        ]);

        $activeGrades = $this->activeGrades;
        $cleanSchoolName = trim((string) preg_replace('/\s+/', ' ', $this->school_name));
        $normalizedSchool = self::normalizeSchoolName($cleanSchoolName);
        $coordinatorPhone = preg_replace('/\s+/', '', trim($this->pic_phone)) ?: trim($this->pic_phone);

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
                $parentName = trim((string) ($slot['parent_name'] ?? ''));
                $parentPhone = trim((string) ($slot['parent_phone'] ?? ''));
                $phone = trim((string) ($slot['phone'] ?? ''));
                $gender = in_array($slot['gender'] ?? 'L', ['L', 'P'], true) ? $slot['gender'] : 'L';

                // Check if user started filling this slot
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
                    $validationErrors["participants.{$grade}.{$idx}.parent_name"] = "Nama Orang Tua {$slotLabel} wajib diisi.";
                }
                if ($parentPhone === '') {
                    $validationErrors["participants.{$grade}.{$idx}.parent_phone"] = "No. HP Orang Tua {$slotLabel} wajib diisi.";
                }

                $entriesByGrade[$grade][] = [
                    'slot_index' => $idx,
                    'nisn' => $nisn,
                    'name' => $name,
                    'gender' => $gender,
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

        $createdRecords = DB::transaction(function () use ($entriesByGrade, $cleanSchoolName, $normalizedSchool, $coordinatorPhone, $plainPassword) {
            // Lock all Brain Champion admission rows to prevent race conditions under concurrent requests
            $lockedRows = Admission::query()
                ->where('course_level', self::EVENT_TAG)
                ->where('status', '!=', 'rejected')
                ->lockForUpdate()
                ->get();

            // 1. Auto-provision / Find School User account (role = 'school', username = $coordinatorPhone)
            $schoolEmail = preg_replace('/[^a-zA-Z0-9]/', '', $coordinatorPhone) . '@school.g-english.com';
            $schoolUser = User::where('username', $coordinatorPhone)->first();

            if (!$schoolUser) {
                if (User::where('email', $schoolEmail)->exists()) {
                    $schoolEmail = preg_replace('/[^a-zA-Z0-9]/', '', $coordinatorPhone) . '_' . rand(100, 999) . '@school.g-english.com';
                }

                $schoolUser = User::create([
                    'username' => $coordinatorPhone,
                    'name' => $cleanSchoolName,
                    'email' => $schoolEmail,
                    'password' => Hash::make($plainPassword),
                    'role' => 'school',
                ]);
            } else {
                $updates = ['password' => Hash::make($plainPassword)];
                if ($schoolUser->role === 'parent' || $schoolUser->role === 'school') {
                    $updates['name'] = $cleanSchoolName;
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
                        'parent_name' => $entry['parent_name'],
                        'parent_phone' => $entry['parent_phone'],
                        'mother_name' => $entry['parent_name'],
                        'mother_phone' => $entry['parent_phone'],
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
                        'parent_name' => $entry['parent_name'],
                        'parent_phone' => $entry['parent_phone'],
                        'mother_name' => $entry['parent_name'],
                        'mother_phone' => $entry['parent_phone'],
                        'is_active' => true,
                    ]);

                    // Link school user to student so school coordinator can also view registered students
                    $student->parents()->syncWithoutDetaching([$schoolUser->id]);

                    // Add to in-memory locked collection so next iteration in same transaction increments properly
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
        $this->lastSubmittedAt = now()->translatedFormat('d F Y, H:i') . ' WITA';
        $this->schoolCredentials = [
            'login_url' => route('login'),
            'username' => $coordinatorPhone,
            'password' => $plainPassword,
            'school_name' => $cleanSchoolName,
            'pic_name' => trim($this->pic_name),
        ];
        $this->registeredResults = $createdRecords;
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
