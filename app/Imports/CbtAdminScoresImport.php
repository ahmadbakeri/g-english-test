<?php

namespace App\Imports;

use App\Models\CbtAttempt;
use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CbtAdminScoresImport implements ToCollection, WithHeadingRow
{
    public int $updatedCount = 0;
    public int $skippedCount = 0;

    public function __construct(protected int $examId) {}

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $rawScore = $row['skor']
                ?? $row['score']
                ?? $row['nilai']
                ?? $row['skor_akhir']
                ?? $row['skor_akhir_isi_admin']
                ?? null;

            if ($rawScore === null || trim((string) $rawScore) === '') {
                $this->skippedCount++;
                continue;
            }

            $score = (float) str_replace(',', '.', trim((string) $rawScore));

            $attempt = null;
            $attemptId = $row['attempt_id'] ?? null;
            if ($attemptId !== null && trim((string) $attemptId) !== '') {
                $attempt = CbtAttempt::where('cbt_exam_id', $this->examId)
                    ->where('id', (int) $attemptId)
                    ->first();
            }

            if (!$attempt) {
                $nis = trim((string) ($row['nis'] ?? ''));
                if ($nis !== '') {
                    $student = Student::where('nis', $nis)->first();
                    if ($student) {
                        $attempt = CbtAttempt::where('cbt_exam_id', $this->examId)
                            ->where('student_id', $student->id)
                            ->latest('id')
                            ->first();
                    }
                }
            }

            if (!$attempt) {
                $this->skippedCount++;
                continue;
            }

            $attempt->update([
                'score' => round($score, 2),
                'scoring_status' => 'manual_uploaded',
            ]);
            $this->updatedCount++;
        }
    }
}
