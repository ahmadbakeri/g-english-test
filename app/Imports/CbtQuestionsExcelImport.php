<?php

namespace App\Imports;

use App\Models\CbtQuestion;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CbtQuestionsExcelImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public int $importedCount = 0;

    public function __construct(protected int $examId)
    {
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $questionText = trim((string) ($row['question_text'] ?? $row[1] ?? $row[0] ?? ''));
            if ($questionText === '') {
                continue;
            }

            $correct = strtoupper(trim((string) ($row['correct_answer'] ?? 'A')));
            if (!in_array($correct, ['A', 'B', 'C', 'D', 'E'], true)) {
                $correct = 'A';
            }

            $optionE = isset($row['option_e']) && trim((string) $row['option_e']) !== ''
                ? trim((string) $row['option_e'])
                : null;

            CbtQuestion::create([
                'cbt_exam_id' => $this->examId,
                'question_text' => $questionText,
                'attachment' => !empty($row['attachment']) ? trim((string) $row['attachment']) : (!empty($row['attachment_optional']) ? trim((string) $row['attachment_optional']) : null),
                'option_a' => trim((string) ($row['option_a'] ?? '')),
                'option_b' => trim((string) ($row['option_b'] ?? '')),
                'option_c' => trim((string) ($row['option_c'] ?? '')),
                'option_d' => trim((string) ($row['option_d'] ?? '')),
                'option_e' => $optionE,
                'correct_answer' => $correct,
                'points' => max(1, (int) ($row['points'] ?? 1)),
            ]);

            $this->importedCount++;
        }
    }

    public function chunkSize(): int
    {
        return 50;
    }
}
