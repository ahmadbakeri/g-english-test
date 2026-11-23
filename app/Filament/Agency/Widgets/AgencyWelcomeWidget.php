<?php

namespace App\Filament\Agency\Widgets;

use App\Livewire\BrainChampionRegistration;
use App\Models\Student;
use App\Support\SchoolAgencyClassifier;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AgencyWelcomeWidget extends Widget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = -2;
    protected int|string|array $columnSpan = 'full';
    protected string $view = 'filament.agency.widgets.agency-welcome-widget';

    protected function getViewData(): array
    {
        $user = Auth::user();
        $agency = $user?->role === SchoolAgencyClassifier::AGENCY_KEMENAG
            ? SchoolAgencyClassifier::AGENCY_KEMENAG
            : SchoolAgencyClassifier::AGENCY_KEMENDIKDASMEN;

        $studentIds = SchoolAgencyClassifier::studentIdsForAgency($agency);
        $students = Student::query()
            ->whereIn('id', $studentIds)
            ->get(['id', 'school_name']);

        $totalParticipants = $students->count();
        $totalSchools = $students
            ->map(fn (Student $s): string => BrainChampionRegistration::normalizeSchoolName((string) $s->school_name))
            ->filter()
            ->unique()
            ->count();

        return [
            'agency' => $agency,
            'agencyLabel' => SchoolAgencyClassifier::labelForAgency($agency),
            'totalParticipants' => $totalParticipants,
            'totalSchools' => $totalSchools,
        ];
    }
}
