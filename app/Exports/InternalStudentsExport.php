<?php

namespace App\Exports;

use App\Filament\Resources\HR\StudentResource;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InternalStudentsExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function __construct(protected string $search = '') {}

    public function headings(): array
    {
        return [
            'No',
            'NIS',
            'NISN',
            'Nama Siswa',
            'L/P',
            'Kelas (Survival English)',
            'Jenjang',
            'Sekolah Asal',
            'No. HP Siswa',
            'Nama Ayah',
            'No. HP Ayah',
            'Nama Ibu',
            'No. HP Ibu',
            'Status',
        ];
    }

    public function array(): array
    {
        $needle = mb_strtolower(trim($this->search));

        $students = Student::query()
            ->with(['user:id,username', 'classes:id,name'])
            ->where(fn ($q) => $q->where('student_type', 'internal')->orWhereNull('student_type'))
            ->orderBy('name')
            ->get();

        if ($needle !== '') {
            $students = $students->filter(function (Student $s) use ($needle): bool {
                $hay = implode(' ', [
                    $s->name, $s->nis, $s->nisn, $s->phone, $s->school_name,
                    $s->father_name, $s->mother_name,
                    StudentResource::resolveClassLabel($s), $s->user?->username,
                ]);
                return str_contains(mb_strtolower($hay), $needle);
            });
        }

        $rows = [];
        $no = 1;
        foreach ($students as $s) {
            $rows[] = [
                $no++,
                $s->nis ?: '-',
                $s->nisn ?: '-',
                $s->name,
                $s->gender ?: '-',
                StudentResource::resolveClassLabel($s),
                $s->education_level ?: '-',
                $s->school_name ?: '-',
                $s->phone ?: '-',
                $s->father_name ?: '-',
                $s->father_phone ?: '-',
                $s->mother_name ?: '-',
                $s->mother_phone ?: '-',
                $s->is_active ? 'Aktif' : 'Nonaktif',
            ];
        }

        return $rows;
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
            'B' => 16,
            'C' => 16,
            'D' => 28,
            'E' => 7,
            'F' => 26,
            'G' => 12,
            'H' => 24,
            'I' => 18,
            'J' => 22,
            'K' => 18,
            'L' => 22,
            'M' => 18,
            'N' => 12,
        ];
    }
}
