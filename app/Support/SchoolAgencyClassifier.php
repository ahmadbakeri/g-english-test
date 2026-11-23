<?php

namespace App\Support;

use App\Livewire\BrainChampionRegistration;
use App\Models\Student;

class SchoolAgencyClassifier
{
    public const AGENCY_KEMENAG = 'kemenag';
    public const AGENCY_KEMENDIKDASMEN = 'kemendikdasmen';

    /**
     * Classify a school/student into 'kemenag' (MI/MTs) or 'kemendikdasmen' (SD/SMP)
     * using explicit education_level override or school_name patterns (both abbreviations and full names).
     */
    public static function classify(?string $schoolName, ?string $educationLevel = null): string
    {
        $edu = strtoupper(trim((string) $educationLevel));
        if (in_array($edu, ['MI', 'MTS'], true)) {
            return self::AGENCY_KEMENAG;
        }

        $name = trim((string) $schoolName);
        if ($name === '') {
            return self::AGENCY_KEMENDIKDASMEN;
        }

        // Match Kemenag abbreviations (MI, MIN, MIS, MIT, MTs, MTsN, MTsS) and full words (Madrasah Ibtidaiyah, Madrasah Tsanawiyah, Madrasah, Ibtidaiyah, Tsanawiyah)
        if (preg_match('/\b(?:MIN|MIS|MIT|MI|MTSN|MTSS|MTS)\s*\d*\b/iu', $name)) {
            return self::AGENCY_KEMENAG;
        }

        if (preg_match('/\b(?:MADRASAH(?:\s+(?:IBTIDAIYAH|TSANAWIYAH))?|IBTIDAIYAH|TSANAWIYAH)\b/iu', $name)) {
            return self::AGENCY_KEMENAG;
        }

        return self::AGENCY_KEMENDIKDASMEN;
    }

    /**
     * Return Brain Champion Student IDs belonging to the given agency ('kemenag' or 'kemendikdasmen').
     *
     * @return array<int, int>
     */
    public static function studentIdsForAgency(string $agency): array
    {
        return Student::query()
            ->select(['id', 'school_name', 'education_level'])
            ->where('course_level', BrainChampionRegistration::EVENT_TAG)
            ->where('student_type', 'external')
            ->get()
            ->filter(fn (Student $student): bool => self::classify($student->school_name, $student->education_level) === $agency)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->values()
            ->all();
    }

    public static function labelForAgency(string $agency): string
    {
        return $agency === self::AGENCY_KEMENAG
            ? 'Kementerian Agama (MI & MTs)'
            : 'Kemendikdasmen (SD & SMP)';
    }

    public static function shortLabelForAgency(string $agency): string
    {
        return $agency === self::AGENCY_KEMENAG ? 'Kemenag' : 'Kemendikdasmen';
    }

    /**
     * Classify a school/student into 'SD', 'MI', 'SMP', or 'MTs'.
     */
    public static function classifyLevel(?string $schoolName, ?string $educationLevel = null, ?string $className = null): string
    {
        $edu = strtoupper(trim((string) $educationLevel));
        if (in_array($edu, ['SD', 'MI', 'SMP', 'MTS'], true)) {
            return $edu === 'MTS' ? 'MTs' : $edu;
        }

        $name = trim((string) $schoolName);
        if ($name !== '') {
            if (preg_match('/\b(?:MTSN|MTSS|MTS)\s*\d*\b/iu', $name) || preg_match('/\bTSANAWIYAH\b/iu', $name)) {
                return 'MTs';
            }
            if (preg_match('/\b(?:MIN|MIS|MIT|MI)\s*\d*\b/iu', $name) || preg_match('/\bIBTIDAIYAH\b/iu', $name)) {
                return 'MI';
            }
            if (preg_match('/\b(?:SMPN|SMPS|SMP)\s*\d*\b/iu', $name)) {
                return 'SMP';
            }
            if (preg_match('/\b(?:SDN|SDS|SD)\s*\d*\b/iu', $name)) {
                return 'SD';
            }
        }

        if (preg_match('/\b([789])\b/', (string) $className)) {
            return self::classify($schoolName, $educationLevel) === self::AGENCY_KEMENAG ? 'MTs' : 'SMP';
        }

        return self::classify($schoolName, $educationLevel) === self::AGENCY_KEMENAG ? 'MI' : 'SD';
    }

    /**
     * Return external Student IDs belonging to a specific level ('SD', 'MI', 'SMP', 'MTs').
     *
     * @return array<int, int>
     */
    public static function externalStudentIdsForLevel(string $level): array
    {
        return Student::query()
            ->select(['id', 'school_name', 'education_level', 'class_name'])
            ->where('student_type', 'external')
            ->get()
            ->filter(fn (Student $student): bool => self::classifyLevel($student->school_name, $student->education_level, $student->class_name) === $level)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->values()
            ->all();
    }
}
