<?php

namespace App\Services\CbtDocx;

use App\Models\CbtExam;
use App\Models\CbtImportBatch;
use App\Models\CbtQuestion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DraftPublishService
{
    /**
     * Publish staging drafts into the target CbtExam's cbt_questions table.
     *
     * @param  string  $mode  'append' | 'replace'
     * @return int Number of published questions
     */
    public function publish(CbtImportBatch $batch, CbtExam $exam, int $userId, string $mode = 'append'): int
    {
        $batch->load('drafts');

        if ($batch->drafts->isEmpty()) {
            throw new RuntimeException('Tidak ada soal draft di batch ini untuk dipublish.');
        }

        foreach ($batch->drafts as $draft) {
            $draft->revalidate();
        }
        $batch->recalculateCounts();
        $batch->refresh();

        if ($batch->error_count > 0) {
            throw new RuntimeException("Masih terdapat {$batch->error_count} soal berstatus ERROR. Perbaiki atau hapus soal bermasalah sebelum publish.");
        }

        $publishedCount = 0;

        DB::transaction(function () use ($batch, $exam, $userId, $mode, &$publishedCount) {
            if ($mode === 'replace') {
                $exam->questions()->delete();
                $startOrder = 1;
            } else {
                $startOrder = ((int) $exam->questions()->max('order_number')) + 1;
            }

            foreach ($batch->drafts()->orderBy('sequence_no')->get() as $draft) {
                // Avoid duplicate creation if re-publishing an already linked draft in append mode
                if ($mode === 'append' && $draft->published_question_id && CbtQuestion::whereKey($draft->published_question_id)->exists()) {
                    $question = CbtQuestion::find($draft->published_question_id);
                    $question->update([
                        'cbt_exam_id' => $exam->id,
                        'order_number' => $draft->sequence_no,
                        'question_type' => $draft->question_type,
                        'question_text' => $draft->question_html,
                        'option_a' => $draft->option_a,
                        'option_b' => $draft->option_b,
                        'option_c' => $draft->option_c,
                        'option_d' => $draft->option_d,
                        'option_e' => $draft->option_e,
                        'statements' => $draft->statements,
                        'correct_answer' => $draft->correct_answer ?: null,
                        'points' => max(1, (int) $draft->points),
                        'explanation' => $draft->explanation_html,
                    ]);
                    $publishedCount++;
                    continue;
                }

                $question = CbtQuestion::create([
                    'cbt_exam_id' => $exam->id,
                    'order_number' => $startOrder++,
                    'question_type' => $draft->question_type,
                    'question_text' => $draft->question_html,
                    'option_a' => $draft->option_a,
                    'option_b' => $draft->option_b,
                    'option_c' => $draft->option_c,
                    'option_d' => $draft->option_d,
                    'option_e' => $draft->option_e,
                    'statements' => $draft->statements,
                    'correct_answer' => $draft->correct_answer ?: null,
                    'points' => max(1, (int) $draft->points),
                    'explanation' => $draft->explanation_html,
                ]);

                $draft->update(['published_question_id' => $question->id]);
                $publishedCount++;
            }

            $batch->update([
                'cbt_exam_id' => $exam->id,
                'status' => 'published',
                'published_at' => now(),
                'published_by' => $userId,
            ]);
        });

        Cache::forget("exam_q_{$exam->id}");
        Cache::forget("exam_{$exam->id}_question_ids");

        return $publishedCount;
    }
}
