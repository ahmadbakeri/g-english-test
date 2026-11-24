<?php

namespace App\Imports;

use App\Livewire\BrainChampionRegistration;
use App\Models\Admission;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BrainChampionStudentImport implements ToCollection, WithHeadingRow
{
    public int $createdCount = 0;
    public int $updatedCount = 0;
    public int $skippedCount = 0;

    /** @var array<int, string> */
    public array $skipReasons = [];

    public function __construct(
        protected ?int $schoolUserId = null,
        protected ?string $schoolName = null,
    ) {}

    public function collection(Collection $rows): void
    {
        $schoolUser = $this->schoolUserId ? User::find($this->schoolUserId) : null;
        $resolvedSchoolName = trim((string) ($this->schoolName ?: ($schoolUser?->name ?? 'Sekolah Peserta')));
        $normalizedSchool = BrainChampionRegistration::normalizeSchoolName($resolvedSchoolName);

        DB::transaction(function () use ($rows, $schoolUser, $resolvedSchoolName, $normalizedSchool) {
            $lockedStudents = Student::query()
                ->select(['id', 'class_name', 'school_name', 'school_user_id'])
                ->where('student_type', 'external')
                ->where('course_level', BrainChampionRegistration::EVENT_TAG)
                ->lockForUpdate()
                ->get();

            foreach ($rows as $index => $row) {
                $rowNum = $index + 2;
                $nisn = preg_replace('/\s+/', '', trim((string) ($row['nisn'] ?? ''))) ?? '';
                $name = trim((string) ($row['nama_siswa'] ?? ''));

                if ($nisn === '' && $name === '') {
                    continue;
                }

                if ($name === '' || $nisn === '') {
                    $this->skippedCount++;
                    $this->skipReasons[] = "Baris {$rowNum}: NISN dan Nama Siswa wajib diisi.";
                    continue;
                }

                $rawClass = trim((string) ($row['kelas'] ?? ''));
                $grade = $this->parseGradeNumber($rawClass);
                if (!in_array($grade, [4, 5, 6, 7, 8, 9], true)) {
                    $this->skippedCount++;
                    $this->skipReasons[] = "Baris {$rowNum} ({$name}): Kelas '{$rawClass}' tidak valid (Gunakan Kelas 4 s/d Kelas 9).";
                    continue;
                }

                $classLabel = "Kelas {$grade}";
                $educationLevel = $grade <= 6 ? 'SD' : 'SMP';
                $birthPlace = trim((string) ($row['tempat_lahir'] ?? '')) ?: null;
                $birthDate = $this->parseBirthDate($row['tanggal_lahir_yyyy_mm_dd'] ?? null);
                $rawGender = strtoupper(trim((string) ($row['jenis_kelamin_l_p'] ?? $row['jenis_kelamin_L_P'] ?? 'L')));
                $gender = in_array($rawGender, ['L', 'P'], true) ? $rawGender : 'L';
                $religion = trim((string) ($row['agama'] ?? '')) ?: null;
                $studentPhone = trim((string) ($row['no_hp_siswa'] ?? '')) ?: null;
                $address = trim((string) ($row['alamat'] ?? '')) ?: null;

                $fatherName = trim((string) ($row['nama_ayah'] ?? '')) ?: null;
                $fatherPhone = trim((string) ($row['no_hp_ayah'] ?? '')) ?: null;
                $fatherJob = trim((string) ($row['pekerjaan_ayah'] ?? '')) ?: null;

                $motherName = trim((string) ($row['nama_ibu'] ?? '')) ?: null;
                $motherPhone = trim((string) ($row['no_hp_ibu'] ?? '')) ?: null;
                $motherJob = trim((string) ($row['pekerjaan_ibu'] ?? '')) ?: null;

                $parentName = $motherName ?: ($fatherName ?: 'Orang Tua');
                $parentPhone = $motherPhone ?: ($fatherPhone ?: ($studentPhone ?: ''));
                $resolvedPhone = $studentPhone ?: $parentPhone;

                // 1. Check if NISN already exists under the same school_user_id (or globally for Admin import)
                $existingQuery = Student::query()
                    ->where('course_level', BrainChampionRegistration::EVENT_TAG)
                    ->where('nisn', $nisn);

                if ($this->schoolUserId) {
                    $existingQuery->where('school_user_id', $this->schoolUserId);
                }

                $existingStudent = $existingQuery->first();

                if ($existingStudent) {
                    $existingStudent->update([
                        'name' => $name,
                        'birth_place' => $birthPlace,
                        'birth_date' => $birthDate,
                        'gender' => $gender,
                        'religion' => $religion,
                        'phone' => $resolvedPhone,
                        'address' => $address,
                        'father_name' => $fatherName,
                        'father_phone' => $fatherPhone,
                        'father_job' => $fatherJob,
                        'mother_name' => $motherName,
                        'mother_phone' => $motherPhone,
                        'mother_job' => $motherJob,
                        'parent_name' => $parentName,
                        'parent_phone' => $parentPhone,
                    ]);

                    $code = $existingStudent->competition_code ?: $existingStudent->nis;
                    if ($code) {
                        Admission::where('registration_number', $code)->update([
                            'nisn' => $nisn,
                            'name' => $name,
                            'birth_place' => $birthPlace,
                            'birth_date' => $birthDate,
                            'gender' => $gender,
                            'religion' => $religion,
                            'phone' => $resolvedPhone,
                            'address' => $address,
                            'father_name' => $fatherName,
                            'father_phone' => $fatherPhone,
                            'father_job' => $fatherJob,
                            'mother_name' => $motherName,
                            'mother_phone' => $motherPhone,
                            'mother_job' => $motherJob,
                            'parent_name' => $parentName,
                            'parent_phone' => $parentPhone,
                        ]);
                    }

                    $this->updatedCount++;
                    continue;
                }

                // 2. New participant: enforce 35 global quota & 2 (SD) / 1 (SMP) per-school quota
                $maxPerSchool = BrainChampionRegistration::maxPerSchoolForGrade($grade);
                $gradeRows = $lockedStudents->filter(fn ($a) => trim((string) $a->class_name) === $classLabel);

                if ($gradeRows->count() >= BrainChampionRegistration::MAX_QUOTA_PER_GRADE) {
                    $this->skippedCount++;
                    $this->skipReasons[] = "Baris {$rowNum} ({$name}): Kuota {$classLabel} sudah penuh (35/35).";
                    continue;
                }

                $schoolUsed = $gradeRows->filter(function ($a) use ($normalizedSchool) {
                    if ($this->schoolUserId && (int) $a->school_user_id === (int) $this->schoolUserId) {
                        return true;
                    }
                    return $normalizedSchool !== ''
                        && BrainChampionRegistration::normalizeSchoolName((string) $a->school_name) === $normalizedSchool;
                })->count();

                if ($schoolUsed >= $maxPerSchool) {
                    $this->skippedCount++;
                    $this->skipReasons[] = "Baris {$rowNum} ({$name}): Sekolah {$resolvedSchoolName} sudah mencapai batas {$maxPerSchool} siswa di {$classLabel}.";
                    continue;
                }

                $regCode = $this->generateUniqueBrainChampionCode($grade);

                $admission = Admission::create([
                    'registration_number' => $regCode,
                    'nisn' => $nisn,
                    'competition_code' => $regCode,
                    'student_type' => 'external',
                    'school_user_id' => $this->schoolUserId,
                    'name' => $name,
                    'birth_place' => $birthPlace,
                    'birth_date' => $birthDate,
                    'gender' => $gender,
                    'religion' => $religion,
                    'phone' => $resolvedPhone,
                    'address' => $address,
                    'education_level' => $educationLevel,
                    'course_level' => BrainChampionRegistration::EVENT_TAG,
                    'class_name' => $classLabel,
                    'school_name' => $resolvedSchoolName,
                    'previous_school' => $resolvedSchoolName,
                    'father_name' => $fatherName,
                    'father_phone' => $fatherPhone,
                    'father_job' => $fatherJob,
                    'mother_name' => $motherName,
                    'mother_phone' => $motherPhone,
                    'mother_job' => $motherJob,
                    'parent_name' => $parentName,
                    'parent_phone' => $parentPhone,
                    'status' => 'approved',
                    'tuition_fee' => 0,
                    'registration_fee' => 0,
                    'has_discount' => false,
                    'total_fee' => 0,
                    'amount_paid' => 0,
                    'notes' => BrainChampionRegistration::EVENT_TAG . " (Excel Import) — {$classLabel} — NISN: {$nisn} — Sekolah: {$resolvedSchoolName}",
                ]);

                $student = Student::create([
                    'school_user_id' => $this->schoolUserId,
                    'nis' => $regCode,
                    'nisn' => $nisn,
                    'competition_code' => $regCode,
                    'student_type' => 'external',
                    'name' => $name,
                    'birth_place' => $birthPlace,
                    'birth_date' => $birthDate,
                    'gender' => $gender,
                    'religion' => $religion,
                    'phone' => $resolvedPhone,
                    'address' => $address,
                    'education_level' => $educationLevel,
                    'course_level' => BrainChampionRegistration::EVENT_TAG,
                    'class_name' => $classLabel,
                    'school_name' => $resolvedSchoolName,
                    'father_name' => $fatherName,
                    'father_phone' => $fatherPhone,
                    'father_job' => $fatherJob,
                    'mother_name' => $motherName,
                    'mother_phone' => $motherPhone,
                    'mother_job' => $motherJob,
                    'parent_name' => $parentName,
                    'parent_phone' => $parentPhone,
                    'is_active' => true,
                ]);

                if ($schoolUser) {
                    $student->parents()->syncWithoutDetaching([$schoolUser->id]);
                }

                $lockedStudents->push($student);
                $this->createdCount++;
            }
        });
    }

    protected function parseGradeNumber(string $rawClass): int
    {
        if (preg_match('/(\d+)/', $rawClass, $m)) {
            return (int) $m[1];
        }
        return 0;
    }

    protected function parseBirthDate(mixed $val): ?string
    {
        if ($val === null || trim((string) $val) === '') {
            return null;
        }
        if (is_numeric($val)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $val)->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }
        try {
            return Carbon::parse(trim((string) $val))->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    protected function generateUniqueBrainChampionCode(int $grade): string
    {
        $prefix = "BC-K{$grade}-";
        $existingCodes = Admission::where('registration_number', 'like', "{$prefix}%")
            ->pluck('registration_number')
            ->merge(Student::withTrashed()->where('nis', 'like', "{$prefix}%")->pluck('nis'));

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
}
