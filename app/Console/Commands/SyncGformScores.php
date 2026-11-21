<?php

namespace App\Console\Commands;

use App\Models\CbtAttempt;
use App\Models\CbtExam;
use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncGformScores extends Command
{
    protected $signature = 'cbt:sync-gform {--exam_id= : Optional specific CbtExam ID}';

    protected $description = 'Sync student CBT scores from published Google Sheet CSV URLs for google_form exams';

    public function handle(): int
    {
        $query = CbtExam::query()
            ->where('cbt_mode', 'google_form')
            ->where('is_active', true)
            ->whereNotNull('gform_sheet_csv_url')
            ->where('gform_sheet_csv_url', '!=', '');

        if ($examId = $this->option('exam_id')) {
            $query->where('id', $examId);
        }

        $exams = $query->get();

        if ($exams->isEmpty()) {
            $this->info('No active Google Form exams with CSV URLs found.');
            return self::SUCCESS;
        }

        $totalSynced = 0;

        foreach ($exams as $exam) {
            try {
                $response = Http::timeout(15)->get($exam->gform_sheet_csv_url);
                if (!$response->successful()) {
                    $this->warn("Failed fetching CSV for Exam #{$exam->id}: HTTP {$response->status()}");
                    continue;
                }

                $lines = preg_split('/\r\n|\r|\n/', trim($response->body()));
                if (count($lines) < 2) {
                    $exam->update(['last_synced_at' => now()]);
                    continue;
                }

                $headers = str_getcsv((string) array_shift($lines));
                $headersNormalized = array_map(fn ($h) => mb_strtolower(trim((string) $h)), $headers);

                $scoreColName = mb_strtolower(trim((string) ($exam->gform_score_column ?: 'Skor')));
                $scoreIdx = null;
                $nisIdx = null;
                $timestampIdx = 0;

                foreach ($headersNormalized as $idx => $h) {
                    if ($scoreIdx === null && ($h === $scoreColName || str_contains($h, 'skor') || str_contains($h, 'score') || str_contains($h, 'nilai'))) {
                        $scoreIdx = $idx;
                    }
                    if ($nisIdx === null && (str_contains($h, 'nis') || str_contains($h, 'no. siswa') || str_contains($h, 'nomor induk') || str_contains($h, 'id siswa'))) {
                        $nisIdx = $idx;
                    }
                    if (str_contains($h, 'timestamp') || str_contains($h, 'waktu')) {
                        $timestampIdx = $idx;
                    }
                }

                if ($scoreIdx === null) {
                    $scoreIdx = 1;
                }
                if ($nisIdx === null) {
                    $nisIdx = 2;
                }

                $chunks = array_chunk($lines, 50);

                foreach ($chunks as $chunk) {
                    $parsedRows = [];
                    $nisList = [];

                    foreach ($chunk as $line) {
                        if (trim($line) === '') {
                            continue;
                        }
                        $cols = str_getcsv($line);
                        $rawNis = trim((string) ($cols[$nisIdx] ?? ''));
                        if ($rawNis === '') {
                            continue;
                        }

                        $rawTimestamp = trim((string) ($cols[$timestampIdx] ?? ''));
                        $rawScore = trim((string) ($cols[$scoreIdx] ?? '0'));

                        // Parse Google Form score format like "85 / 100" or "85.5"
                        if (str_contains($rawScore, '/')) {
                            $parts = array_map('trim', explode('/', $rawScore));
                            $earned = (float) preg_replace('/[^0-9.]/', '', $parts[0] ?? '0');
                            $max = (float) preg_replace('/[^0-9.]/', '', $parts[1] ?? '100');
                            $numericScore = $max > 0 ? round(($earned / $max) * 100, 2) : $earned;
                        } else {
                            $numericScore = (float) preg_replace('/[^0-9.]/', '', $rawScore);
                        }

                        $nisList[] = $rawNis;
                        $parsedRows[] = [
                            'nis' => $rawNis,
                            'timestamp' => $rawTimestamp,
                            'score' => $numericScore,
                        ];
                    }

                    if (empty($parsedRows)) {
                        continue;
                    }

                    $studentsByNis = Student::query()
                        ->whereIn('nis', array_unique($nisList))
                        ->get()
                        ->keyBy(fn (Student $s) => mb_strtolower(trim($s->nis)));

                    foreach ($parsedRows as $item) {
                        $student = $studentsByNis->get(mb_strtolower($item['nis']));
                        if (!$student) {
                            continue;
                        }

                        $externalId = md5($exam->id . $item['timestamp'] . $student->nis);

                        $attempt = CbtAttempt::where('cbt_exam_id', $exam->id)
                            ->where('student_id', $student->id)
                            ->first();

                        if ($attempt) {
                            $attempt->update([
                                'external_submission_id' => $externalId,
                                'score' => $item['score'],
                                'status' => 'finished',
                                'is_completed' => true,
                                'sync_status' => 'synced',
                                'end_time' => $attempt->end_time ?? now(),
                            ]);
                        } else {
                            CbtAttempt::updateOrCreate(
                                [
                                    'external_submission_id' => $externalId,
                                ],
                                [
                                    'cbt_exam_id' => $exam->id,
                                    'student_id' => $student->id,
                                    'attempt_number' => 1,
                                    'start_time' => now(),
                                    'end_time' => now(),
                                    'score' => $item['score'],
                                    'status' => 'finished',
                                    'is_completed' => true,
                                    'sync_status' => 'synced',
                                ]
                            );
                        }

                        $totalSynced++;
                    }
                }

                $exam->update(['last_synced_at' => now()]);
            } catch (\Throwable $e) {
                $this->error("Error syncing Exam #{$exam->id}: {$e->getMessage()}");
            }
        }

        $this->info("Synced {$totalSynced} attempt(s) across {$exams->count()} exam(s).");
        return self::SUCCESS;
    }
}
