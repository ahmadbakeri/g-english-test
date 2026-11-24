<?php

namespace App\Filament\School\Widgets;

use App\Livewire\BrainChampionRegistration;
use App\Models\Admission;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SchoolQuotaOverviewWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = -1;

    public static function resolveSchoolLevelForUser($user): string
    {
        if (!$user) {
            return 'SD';
        }

        $sampleStudent = Student::where('school_user_id', $user->id)
            ->where('course_level', BrainChampionRegistration::EVENT_TAG)
            ->first();

        if ($sampleStudent) {
            if (in_array(strtoupper((string) $sampleStudent->education_level), ['SMP', 'MTS'], true)) {
                return 'SMP';
            }
            if (preg_match('/Kelas\s*([789])/i', (string) $sampleStudent->class_name)) {
                return 'SMP';
            }
            return 'SD';
        }

        $schoolUpper = strtoupper((string) $user->name);
        if (str_contains($schoolUpper, 'SMP') || str_contains($schoolUpper, 'MTS')) {
            return 'SMP';
        }

        return 'SD';
    }

    protected function getStats(): array
    {
        $user = Auth::user();
        $level = self::resolveSchoolLevelForUser($user);
        $grades = $level === 'SMP' ? [7, 8, 9] : [4, 5, 6];
        $normalizedSchool = BrainChampionRegistration::normalizeSchoolName((string) ($user?->name ?? ''));

        $bcStudents = Student::query()
            ->select(['class_name', 'school_name', 'school_user_id'])
            ->where('student_type', 'external')
            ->where('course_level', BrainChampionRegistration::EVENT_TAG)
            ->get();

        $stats = [];
        foreach ($grades as $grade) {
            $classLabel = "Kelas {$grade}";
            $maxPerSchool = BrainChampionRegistration::maxPerSchoolForGrade($grade);

            $gradeRows = $bcStudents->filter(fn ($row) => trim((string) $row->class_name) === $classLabel);
            $globalUsed = $gradeRows->count();
            $globalRemaining = max(0, BrainChampionRegistration::MAX_QUOTA_PER_GRADE - $globalUsed);

            $schoolUsed = $gradeRows->filter(function ($row) use ($user, $normalizedSchool) {
                if ($user && (int) $row->school_user_id === (int) $user->id) {
                    return true;
                }
                return $normalizedSchool !== ''
                    && BrainChampionRegistration::normalizeSchoolName((string) $row->school_name) === $normalizedSchool;
            })->count();

            $isFull = $globalRemaining === 0;
            $isAlmostFull = !$isFull && $globalUsed >= 28;

            $color = $isFull ? 'danger' : ($isAlmostFull ? 'warning' : 'success');
            $statClass = $isFull ? 'stat-card stat-rose' : ($isAlmostFull ? 'stat-card stat-amber' : 'stat-card stat-emerald');

            $stats[] = Stat::make(
                "Kuota {$classLabel} ({$level})",
                $isFull ? 'PENUH (35/35)' : "Sisa {$globalRemaining} / 35"
            )
                ->description("Delegasi Sekolah Anda: {$schoolUsed}/{$maxPerSchool} Siswa • Global Terisi: {$globalUsed}/35")
                ->descriptionIcon('heroicon-m-user-group')
                ->icon('heroicon-o-trophy')
                ->chart([5, 15, 25, max(1, $globalUsed)])
                ->color($color)
                ->extraAttributes(['class' => $statClass]);
        }

        return $stats;
    }
}
