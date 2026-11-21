<?php

namespace App\Exports;

use App\Filament\School\Widgets\SchoolQuotaOverviewWidget;
use App\Livewire\BrainChampionRegistration;
use App\Models\Admission;
use App\Models\Student;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BrainChampionStudentTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function __construct(protected ?int $schoolUserId = null) {}

    public function headings(): array
    {
        return [
            'kelas',
            'nisn',
            'nama_siswa',
            'tempat_lahir',
            'tanggal_lahir_yyyy_mm_dd',
            'jenis_kelamin_L_P',
            'agama',
            'no_hp_siswa',
            'alamat',
            'nama_ayah',
            'no_hp_ayah',
            'pekerjaan_ayah',
            'nama_ibu',
            'no_hp_ibu',
            'pekerjaan_ibu',
        ];
    }

    public function array(): array
    {
        $rows = [];

        if ($this->schoolUserId) {
            $schoolUser = User::find($this->schoolUserId);
            $level = SchoolQuotaOverviewWidget::resolveSchoolLevelForUser($schoolUser);
            $grades = $level === 'SMP' ? [7, 8, 9] : [4, 5, 6];

            $existingStudents = Student::query()
                ->where('school_user_id', $this->schoolUserId)
                ->where('course_level', BrainChampionRegistration::EVENT_TAG)
                ->orderBy('class_name')
                ->orderBy('id')
                ->get();

            foreach ($grades as $grade) {
                $classLabel = "Kelas {$grade}";
                $maxPerSchool = BrainChampionRegistration::maxPerSchoolForGrade($grade);

                $gradeStudents = $existingStudents
                    ->filter(fn (Student $s) => trim((string) $s->class_name) === $classLabel)
                    ->values();

                foreach ($gradeStudents as $s) {
                    $rows[] = $this->formatStudentRow($s);
                }

                $globalUsed = Admission::where('course_level', BrainChampionRegistration::EVENT_TAG)
                    ->where('status', '!=', 'rejected')
                    ->where('class_name', $classLabel)
                    ->count();

                $globalRemaining = max(0, BrainChampionRegistration::MAX_QUOTA_PER_GRADE - $globalUsed);
                $schoolRemaining = max(0, $maxPerSchool - $gradeStudents->count());
                $emptySlotsToAdd = min($globalRemaining, $schoolRemaining);

                for ($i = 0; $i < $emptySlotsToAdd; $i++) {
                    $rows[] = [
                        $classLabel,
                        '',
                        '',
                        'Tanjung',
                        '2014-05-10',
                        'L',
                        'Islam',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                    ];
                }
            }

            if (!empty($rows)) {
                return $rows;
            }
        }

        // Default sample template rows (for Admin or empty school)
        return [
            [
                'Kelas 4',
                '0123456789',
                'Contoh Siswa Brain Champion',
                'Tanjung',
                '2014-05-15',
                'L',
                'Islam',
                '081234567890',
                'Jl. Ir. PHM Noor RT 4 Tanjung',
                'Budi Santoso',
                '081234567891',
                'Wiraswasta',
                'Siti Aminah',
                '081234567892',
                'Ibu Rumah Tangga',
            ],
        ];
    }

    protected function formatStudentRow(Student $s): array
    {
        return [
            $s->class_name ?: 'Kelas 4',
            (string) ($s->nisn ?? ''),
            (string) ($s->name ?? ''),
            (string) ($s->birth_place ?? ''),
            $s->birth_date ? $s->birth_date->format('Y-m-d') : '',
            (string) ($s->gender ?: 'L'),
            (string) ($s->religion ?? ''),
            (string) ($s->phone ?? ''),
            (string) ($s->address ?? ''),
            (string) ($s->father_name ?? ''),
            (string) ($s->father_phone ?? ''),
            (string) ($s->father_job ?? ''),
            (string) ($s->mother_name ?: ($s->parent_name ?? '')),
            (string) ($s->mother_phone ?: ($s->parent_phone ?? '')),
            (string) ($s->mother_job ?? ''),
        ];
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
            'G' => 14,
            'H' => 18,
            'I' => 32,
            'J' => 24,
            'K' => 18,
            'L' => 20,
            'M' => 24,
            'N' => 18,
            'O' => 20,
        ];
    }
}
