<?php

namespace App\Exports;

use App\Models\CbtAttempt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CbtExamResultsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $examId;

    public function __construct($examId)
    {
        $this->examId = $examId;
    }

    public function collection()
    {
        return CbtAttempt::with('student.user')
            ->where('cbt_exam_id', $this->examId)
            ->where('status', 'finished')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Attempt',
            'NIS / Username',
            'Nama Siswa',
            'Skor Akhir',
            'Total Pelanggaran',
            'Disubmit Paksa?',
            'Waktu Mulai',
            'Waktu Selesai',
        ];
    }

    public function map($attempt): array
    {
        return [
            $attempt->id,
            $attempt->student->user->username ?? '-',
            $attempt->student->name,
            $attempt->score,
            $attempt->violation_count,
            $attempt->is_force_submitted ? 'Ya' : 'Tidak',
            $attempt->start_time ? $attempt->start_time->format('Y-m-d H:i:s') : '-',
            $attempt->end_time ? $attempt->end_time->format('Y-m-d H:i:s') : '-',
        ];
    }
}
