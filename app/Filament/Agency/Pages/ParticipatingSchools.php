<?php

namespace App\Filament\Agency\Pages;

use App\Livewire\BrainChampionRegistration;
use App\Models\Student;
use App\Support\SchoolAgencyClassifier;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ParticipatingSchools extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?int $navigationSort = 2;
    protected string $view = 'filament.agency.pages.participating-schools';

    public string $search = '';

    public static function getNavigationLabel(): string
    {
        return __('Participating Schools');
    }

    public function getTitle(): string
    {
        return __('Participating Schools');
    }

    public static function canAccess(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['kemenag', 'kemendikdasmen'], true);
    }

    public function getSchoolsProperty(): array
    {
        $user = Auth::user();
        $agency = $user?->role === SchoolAgencyClassifier::AGENCY_KEMENAG
            ? SchoolAgencyClassifier::AGENCY_KEMENAG
            : SchoolAgencyClassifier::AGENCY_KEMENDIKDASMEN;

        $ids = SchoolAgencyClassifier::studentIdsForAgency($agency);
        $students = Student::query()
            ->with('schoolUser')
            ->whereIn('id', $ids ?: [0])
            ->orderBy('school_name')
            ->orderBy('class_name')
            ->get();

        $grouped = [];
        foreach ($students as $student) {
            $key = BrainChampionRegistration::normalizeSchoolName((string) $student->school_name);
            if ($key === '') {
                continue;
            }

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'school_name' => $student->school_name,
                    'coordinator_name' => $student->schoolUser?->name ?? '-',
                    'coordinator_phone' => $student->schoolUser?->username ?? '-',
                    'total' => 0,
                    'grades' => [],
                ];
            }

            $grouped[$key]['total']++;
            $grade = $student->class_name ?: '-';
            $grouped[$key]['grades'][$grade] = ($grouped[$key]['grades'][$grade] ?? 0) + 1;
        }

        $schools = array_values($grouped);
        if (trim($this->search) !== '') {
            $needle = mb_strtolower(trim($this->search));
            $schools = array_values(array_filter($schools, function (array $item) use ($needle): bool {
                return str_contains(mb_strtolower((string) $item['school_name']), $needle)
                    || str_contains(mb_strtolower((string) $item['coordinator_name']), $needle);
            }));
        }

        return $schools;
    }
}
