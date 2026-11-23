<?php

namespace App\Filament\Agency\Widgets;

use App\Livewire\BrainChampionRegistration;
use App\Models\Student;
use App\Support\SchoolAgencyClassifier;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AgencyStatsOverviewWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = -1;

    protected function getStats(): array
    {
        $user = Auth::user();
        $agency = $user?->role === SchoolAgencyClassifier::AGENCY_KEMENAG
            ? SchoolAgencyClassifier::AGENCY_KEMENAG
            : SchoolAgencyClassifier::AGENCY_KEMENDIKDASMEN;

        $agencyStudentIds = SchoolAgencyClassifier::studentIdsForAgency($agency);
        $agencyStudents = Student::query()
            ->whereIn('id', $agencyStudentIds)
            ->get(['id', 'class_name']);

        $allCounts = Student::query()
            ->where('course_level', BrainChampionRegistration::EVENT_TAG)
            ->where('student_type', 'external')
            ->selectRaw('class_name, COUNT(*) as total')
            ->groupBy('class_name')
            ->pluck('total', 'class_name');

        $stats = [];
        foreach ([4, 5, 6, 7, 8, 9] as $grade) {
            $classLabel = "Kelas {$grade}";
            $agencyCount = $agencyStudents->filter(fn ($s) => trim((string) $s->class_name) === $classLabel)->count();
            $globalUsed = (int) ($allCounts[$classLabel] ?? 0);
            $max = BrainChampionRegistration::MAX_QUOTA_PER_GRADE;
            $isFull = $globalUsed >= $max;
            $isAlmostFull = !$isFull && $globalUsed >= 28;

            $levelTag = $agency === SchoolAgencyClassifier::AGENCY_KEMENAG
                ? ($grade <= 6 ? 'MI' : 'MTs')
                : ($grade <= 6 ? 'SD' : 'SMP');

            $statClass = $isFull ? 'stat-card stat-rose' : ($isAlmostFull ? 'stat-card stat-amber' : 'stat-card stat-emerald');
            $color = $isFull ? 'danger' : ($isAlmostFull ? 'warning' : 'success');

            $stats[] = Stat::make("{$classLabel} ({$levelTag})", "{$agencyCount} " . __('participants'))
                ->description(__('Global quota') . ": {$globalUsed} / {$max}")
                ->color($color)
                ->extraAttributes(['class' => $statClass]);
        }

        return $stats;
    }
}
