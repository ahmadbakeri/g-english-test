<?php

namespace App\Console\Commands;

use App\Livewire\BrainChampionRegistration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SeedDummyExternalStudents extends Command
{
    protected $signature = 'dummy:external-students
                            {count=2000 : Total dummy external students to generate}
                            {--bc=180 : How many of them participate in Brain Champion (rest are non-BC external)}
                            {--clean : Remove all dummy external students and dummy accounts}';

    protected $description = 'Generate or clean realistic dummy external students (mixed Brain Champion & non-BC) for load testing';

    public const NIS_PREFIX = 'DMY-EXT-';
    public const SCHOOL_USER_PREFIX = '49';
    public const STUDENT_USER_PREFIX = '9948';

    public function handle(): int
    {
        if ($this->option('clean')) {
            return $this->cleanDummyData();
        }

        $total = max(1, (int) $this->argument('count'));
        $bcTarget = min($total, max(0, (int) $this->option('bc')));

        // Always clean old dummy set first so re-running is idempotent
        $this->cleanDummyData(silent: true);

        $started = microtime(true);
        $now = now()->toDateTimeString();
        $hashedPassword = Hash::make('password');

        // 24 realistic partner schools across SD (6), MI (6), SMP (6), MTs (6) (~83 students/school, ~28/class)
        $schoolCatalog = [
            // SD (6)
            ['name' => 'SDN 1 Tanjung', 'level' => 'SD'],
            ['name' => 'SDN 2 Tanjung', 'level' => 'SD'],
            ['name' => 'SDN 1 Pembataan', 'level' => 'SD'],
            ['name' => 'SDN 1 Murung Pudak', 'level' => 'SD'],
            ['name' => 'SD Hasbunallah Tabalong', 'level' => 'SD'],
            ['name' => 'SDIT An-Nahl Tanjung', 'level' => 'SD'],
            // MI (6)
            ['name' => 'MIN 1 Tabalong', 'level' => 'MI'],
            ['name' => 'MIN 2 Tabalong', 'level' => 'MI'],
            ['name' => 'MIN 3 Tabalong', 'level' => 'MI'],
            ['name' => 'MIS Al-Islamiyah Bangkar', 'level' => 'MI'],
            ['name' => 'MIS Nurul Huda Tanjung', 'level' => 'MI'],
            ['name' => 'MIS Darul Ulum Kelua', 'level' => 'MI'],
            // SMP (6)
            ['name' => 'SMPN 1 Tanjung', 'level' => 'SMP'],
            ['name' => 'SMPN 2 Tanjung', 'level' => 'SMP'],
            ['name' => 'SMPN 3 Tanjung', 'level' => 'SMP'],
            ['name' => 'SMPN 1 Murung Pudak', 'level' => 'SMP'],
            ['name' => 'SMPN 1 Kelua', 'level' => 'SMP'],
            ['name' => 'SMP Hasbunallah Tanjung', 'level' => 'SMP'],
            // MTs (6)
            ['name' => 'MTsN 1 Tabalong', 'level' => 'MTs'],
            ['name' => 'MTsN 2 Tabalong', 'level' => 'MTs'],
            ['name' => 'MTsN 4 Tabalong', 'level' => 'MTs'],
            ['name' => 'MTsN 10 Tabalong', 'level' => 'MTs'],
            ['name' => 'MTsS Al-Ikhlas Tanjung', 'level' => 'MTs'],
            ['name' => 'MTsS Darussalam Kelua', 'level' => 'MTs'],
        ];

        // 1. Reuse existing school user if name matches, else bulk-create dummy school users (username 49001..)
        $existingSchools = DB::table('users')
            ->where('role', 'school')
            ->get(['id', 'name', 'username'])
            ->keyBy(fn ($u) => mb_strtolower(trim((string) $u->name)));

        $schools = [];
        $newSchoolRows = [];
        $seq = 1;
        foreach ($schoolCatalog as $idx => $sc) {
            $norm = mb_strtolower(trim($sc['name']));
            if (isset($existingSchools[$norm])) {
                $schools[] = [
                    'id' => (int) $existingSchools[$norm]->id,
                    'name' => $existingSchools[$norm]->name,
                    'level' => $sc['level'],
                ];
            } else {
                $code = self::SCHOOL_USER_PREFIX . str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
                $seq++;
                $newSchoolRows[] = [
                    'username' => $code,
                    'name' => $sc['name'],
                    'email' => "dummy_school_{$code}@dummy.g-english.local",
                    'password' => $hashedPassword,
                    'role' => 'school',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $schools[] = [
                    'code' => $code,
                    'name' => $sc['name'],
                    'level' => $sc['level'],
                ];
            }
        }

        if (!empty($newSchoolRows)) {
            DB::table('users')->insert($newSchoolRows);
            $insertedSchools = DB::table('users')
                ->where('role', 'school')
                ->where('email', 'like', '%@dummy.g-english.local')
                ->pluck('id', 'username');

            foreach ($schools as &$s) {
                if (!isset($s['id']) && isset($s['code'])) {
                    $s['id'] = (int) $insertedSchools[$s['code']];
                }
            }
            unset($s);
        }

        // Realistic Indonesian names for deterministic variety
        $firstNamesMale = [
            'Ahmad', 'Muhammad', 'Aditya', 'Rizky', 'Fajar', 'Dimas', 'Bagas', 'Rafli', 'Naufal', 'Hafiz',
            'Zaki', 'Ilham', 'Bayu', 'Galih', 'Reza', 'Farhan', 'Alif', 'Daffa', 'Raka', 'Syahrul',
            'Iqbal', 'Arif', 'Rendi', 'Yoga', 'Taufik', 'Haikal', 'Akbar', 'Yusuf', 'Irfan', 'Fikri',
        ];
        $firstNamesFemale = [
            'Aulia', 'Siti', 'Putri', 'Nabila', 'Zahra', 'Aisyah', 'Syifa', 'Keisha', 'Nadia', 'Rania',
            'Salma', 'Kayla', 'Fathiya', 'Khansa', 'Citra', 'Dewi', 'Lestari', 'Intan', 'Rahma', 'Safira',
            'Alifah', 'Nayla', 'Azzahra', 'Amelia', 'Bunga', 'Dina', 'Hasna', 'Mutiara', 'Shafa', 'Tiara',
        ];
        $lastNames = [
            'Pratama', 'Saputra', 'Ramadhan', 'Hidayat', 'Nugroho', 'Maulana', 'Santoso', 'Wijaya', 'Setiawan', 'Rahman',
            'Fauzi', 'Hakim', 'Kurniawan', 'Permana', 'Utama', 'Syahputra', 'Azzahra', 'Salsabila', 'Ramadhani', 'Maharani',
            'Puspita', 'Cahyani', 'Anggraini', 'Kusuma', 'Wardani', 'Wulandari', 'Oktaviani', 'Fitriani', 'Mulyani', 'Handayani',
        ];
        $parentNames = [
            'H. Abdurrahman', 'Budi Santoso', 'Slamet Riyadi', 'Hendra Wijaya', 'M. Noor', 'Syaifullah',
            'Hj. Siti Aminah', 'Dewi Kartika', 'Nurhayati', 'Rina Marlina', 'Ahmad Fauzan', 'Zainal Arifin',
            'Rusdiansyah', 'Rahmadi', 'Junaidi', 'Mulyadi', 'Norhasanah', 'Halimah', 'Susilawati', 'Eko Prasetyo',
        ];

        $nonBcPrograms = [
            'Try Out Bahasa Inggris',
            'Placement Test Sekolah',
            'Kemitraan Sekolah',
            'English Proficiency Test',
        ];

        $schoolCount = count($schools);
        $bcGradeCounters = [4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0];
        $bcPerGradeCap = (int) ceil($bcTarget / 6);
        $bcCreated = 0;

        // 2. Bulk-create dummy student user accounts in chunks of 500
        $userChunk = [];
        $studentMeta = [];

        for ($i = 1; $i <= $total; $i++) {
            $school = $schools[($i - 1) % $schoolCount];
            $rankInSchool = intdiv($i - 1, $schoolCount);
            $isFemale = (($i + $rankInSchool) % 2 === 0);
            $first = $isFemale
                ? $firstNamesFemale[($i * 7) % count($firstNamesFemale)]
                : $firstNamesMale[($i * 7) % count($firstNamesMale)];
            $middle = $isFemale
                ? $firstNamesFemale[($i * 13 + 3) % count($firstNamesFemale)]
                : $firstNamesMale[($i * 11 + 5) % count($firstNamesMale)];
            $last = $lastNames[($i * 17 + 1) % count($lastNames)];
            $fullName = ($i % 3 === 0) ? "{$first} {$middle} {$last}" : "{$first} {$last}";

            // Spread evenly across all classes inside every school using $rankInSchool
            if (in_array($school['level'], ['SD', 'MI'], true)) {
                $grade = [4, 5, 6][$rankInSchool % 3];
            } else {
                $grade = [7, 8, 9][$rankInSchool % 3];
            }

            $isBrainChampion = false;
            $competitionCode = null;
            $courseLevel = $nonBcPrograms[$i % count($nonBcPrograms)];

            if (
                $bcCreated < $bcTarget
                && isset($bcGradeCounters[$grade])
                && $bcGradeCounters[$grade] < $bcPerGradeCap
                && ($i % max(1, (int) floor($total / max(1, $bcTarget))) === 1 || ($total - $i) < ($bcTarget - $bcCreated))
            ) {
                $isBrainChampion = true;
                $bcCreated++;
                $bcGradeCounters[$grade]++;
                $courseLevel = BrainChampionRegistration::EVENT_TAG;
                $competitionCode = sprintf('BC-K%d-D%03d', $grade, $bcGradeCounters[$grade]);
            }

            $username = self::STUDENT_USER_PREFIX . str_pad((string) $i, 5, '0', STR_PAD_LEFT);
            $nis = self::NIS_PREFIX . str_pad((string) $i, 4, '0', STR_PAD_LEFT);
            $nisn = '00' . str_pad((string) (14000000 + $i), 8, '0', STR_PAD_LEFT);
            $parentName = $parentNames[$i % count($parentNames)];
            $phone = '0852' . str_pad((string) (48000000 + $i), 8, '0', STR_PAD_LEFT);

            $userChunk[] = [
                'username' => $username,
                'name' => $fullName,
                'email' => "dummy_ext_{$i}@dummy.g-english.local",
                'password' => $hashedPassword,
                'role' => 'siswa',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $studentMeta[$username] = [
                'school_user_id' => $school['id'],
                'nis' => $nis,
                'nisn' => $nisn,
                'competition_code' => $competitionCode,
                'student_type' => 'external',
                'name' => $fullName,
                'gender' => $isFemale ? 'P' : 'L',
                'phone' => $phone,
                'parent_name' => $parentName,
                'parent_phone' => $phone,
                'is_active' => 1,
                'education_level' => $school['level'],
                'course_level' => $courseLevel,
                'class_name' => "Kelas {$grade}",
                'school_name' => $school['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($userChunk) >= 500) {
                DB::table('users')->insert($userChunk);
                $userChunk = [];
            }
        }

        if (!empty($userChunk)) {
            DB::table('users')->insert($userChunk);
        }

        // 3. Map inserted dummy user IDs and bulk-insert students in chunks of 500
        $insertedUsers = DB::table('users')
            ->where('username', 'like', self::STUDENT_USER_PREFIX . '%')
            ->pluck('id', 'username');

        $studentChunk = [];
        foreach ($studentMeta as $username => $row) {
            $row['user_id'] = isset($insertedUsers[$username]) ? (int) $insertedUsers[$username] : null;
            $studentChunk[] = $row;

            if (count($studentChunk) >= 500) {
                DB::table('students')->insert($studentChunk);
                $studentChunk = [];
            }
        }
        if (!empty($studentChunk)) {
            DB::table('students')->insert($studentChunk);
        }

        $this->flushCaches();

        $elapsed = round((microtime(true) - $started) * 1000);
        $nonBcCreated = $total - $bcCreated;

        $this->info("DONE in {$elapsed}ms: {$total} dummy external students seeded across {$schoolCount} schools ({$bcCreated} Brain Champion, {$nonBcCreated} Non-BC external).");

        return self::SUCCESS;
    }

    protected function cleanDummyData(bool $silent = false): int
    {
        $deletedStudents = DB::table('students')
            ->where('nis', 'like', self::NIS_PREFIX . '%')
            ->delete();

        $deletedUsers = DB::table('users')
            ->where('email', 'like', '%@dummy.g-english.local')
            ->delete();

        $this->flushCaches();

        if (!$silent) {
            $this->info("CLEANED: {$deletedStudents} dummy students and {$deletedUsers} dummy accounts removed.");
        }

        return self::SUCCESS;
    }

    protected function flushCaches(): void
    {
        \App\Filament\Resources\HR\ExternalStudentResource\Pages\ListExternalStudents::flushSummaryCache();
    }
}
