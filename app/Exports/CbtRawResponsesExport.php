<?php

namespace App\Exports;

use App\Models\CbtExam;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CbtRawResponsesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected CbtExam $exam;
    protected Collection $questions;

    public function __construct(CbtExam $exam)
    {
        $this->exam = $exam;
        $this->questions = $exam->questions()->orderBy('order_number')->orderBy('id')->get();
    }

    public function headings(): array
    {
        $headers = [
            'attempt_id',
            'nis',
            'nama_siswa',
            'kelas',
            'status',
            'scoring_status',
            'waktu_submit',
            'skor',
        ];

        foreach ($this->questions as $idx => $q) {
            $no = $idx + 1;
            $type = $q->question_type ?? 'mcq';
            $headers[] = "Q{$no} ({$type})";
        }

        return $headers;
    }

    public function collection(): Collection
    {
        $attempts = $this->exam->attempts()
            ->with(['student', 'answers'])
            ->orderBy('id')
            ->get();

        $rows = collect();

        foreach ($attempts as $attempt) {
            $answerMap = $attempt->answers->keyBy('cbt_question_id');
            $row = [
                $attempt->id,
                $attempt->student?->nis ?? '-',
                $attempt->student?->name ?? '-',
                $attempt->student?->class_name ?? ($this->exam->classRoom?->name ?? '-'),
                $attempt->status,
                $attempt->scoring_status ?? 'graded',
                $attempt->end_time?->format('Y-m-d H:i:s') ?? '-',
                $attempt->score !== null ? (float) $attempt->score : '',
            ];

            foreach ($this->questions as $q) {
                $ans = $answerMap->get($q->id);
                if (!$ans) {
                    $row[] = '';
                    continue;
                }

                if (($q->question_type ?? 'mcq') === 'complex_tf' && is_array($ans->answer_payload)) {
                    $payload = $ans->answer_payload;
                    ksort($payload);
                    $parts = [];
                    foreach ($payload as $k => $v) {
                        $parts[] = "{$k}:{$v}";
                    }
                    $row[] = implode(' | ', $parts);
                } else {
                    $row[] = (string) ($ans->selected_answer ?? '');
                }
            }

            $rows->push($row);
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0A4282'],
                ],
            ],
        ];
    }
}
