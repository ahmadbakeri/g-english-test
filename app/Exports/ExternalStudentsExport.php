<?php

namespace App\Exports;

use App\Livewire\BrainChampionRegistration;
use App\Models\Student;
use App\Support\SchoolAgencyClassifier;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExternalStudentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected int $rowNo = 0;

    public function __construct(
        protected string $programFilter = 'all',
        protected string $search = '',
    ) {}

    public function query()
    {
        $query = Student::query()
            ->with(['user:id,username'])
            ->where('student_type', 'external');

        if ($this->programFilter === 'brain_champion') {
            $query->where('course_level', BrainChampionRegistration::EVENT_TAG);
        } elseif ($this->programFilter === 'non_bc') {
            $query->where(function (Builder $q) {
                $q->whereNull('course_level')
                    ->orWhere('course_level', '!=', BrainChampionRegistration::EVENT_TAG);
            });
        }

        $needle = trim($this->search);
        if ($needle !== '') {
            $like = '%' . $needle . '%';
            $query->where(function (Builder $q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('nis', 'like', $like)
                    ->orWhere('nisn', 'like', $like)
                    ->orWhere('competition_code', 'like', $like)
                    ->orWhere('school_name', 'like', $like)
                    ->orWhere('class_name', 'like', $like)
                    ->orWhere('course_level', 'like', $like)
                    ->orWhereHas('user', fn (Builder $u) => $u->where('username', 'like', $like))
                    ->orWhereHas('schoolUser', fn (Builder $su) => $su->where('username', 'like', $like));
            });
        }

        return $query->orderBy('school_name')->orderBy('class_name')->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'No',
            'Jenjang',
            'Sekolah',
            'Kelas',
            'Program',
            'Username / Kode',
            'NISN',
            'Nama Siswa',
            'L/P',
            'No. HP Siswa',
            'Orang Tua / Wali',
            'No. HP Orang Tua',
            'Status',
        ];
    }

    /**
     * @param  Student  $student
     */
    public function map($student): array
    {
        $this->rowNo++;
        $lvl = SchoolAgencyClassifier::classifyLevel($student->school_name, $student->education_level, $student->class_name);
        $program = $student->course_level === BrainChampionRegistration::EVENT_TAG
            ? 'Brain Champion'
            : ($student->course_level ?: 'Eksternal Umum');

        return [
            $this->rowNo,
            $lvl,
            $student->school_name ?: 'Tanpa Sekolah',
            $student->class_name ?: '-',
            $program,
            $student->user?->username ?: ($student->competition_code ?: ($student->nis ?: '-')),
            $student->nisn ?: '-',
            $student->name,
            $student->gender ?: '-',
            $student->phone ?: '-',
            $student->parent_name ?: ($student->father_name ?: ($student->mother_name ?: '-')),
            $student->parent_phone ?: ($student->father_phone ?: ($student->mother_phone ?: '-')),
            $student->is_active ? 'Aktif' : 'Nonaktif',
        ];
    }

    public function styles(Worksheet $sheet): array
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
            'A' => 6,
            'B' => 10,
            'C' => 28,
            'D' => 12,
            'E' => 22,
            'F' => 18,
            'G' => 16,
            'H' => 28,
            'I' => 7,
            'J' => 18,
            'K' => 24,
            'L' => 18,
            'M' => 12,
        ];
    }
}
