<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CbtQuestionDraft extends Model
{
    protected $fillable = [
        'batch_id',
        'sequence_no',
        'question_type',
        'question_html',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'statements',
        'correct_answer',
        'points',
        'explanation_html',
        'extracted_media',
        'validation_status',
        'validation_issues',
        'has_shape_warning',
        'raw_source_debug',
        'published_question_id',
    ];

    protected $casts = [
        'statements' => 'array',
        'extracted_media' => 'array',
        'validation_issues' => 'array',
        'has_shape_warning' => 'boolean',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(CbtImportBatch::class, 'batch_id');
    }

    public function publishedQuestion(): BelongsTo
    {
        return $this->belongsTo(CbtQuestion::class, 'published_question_id');
    }

    public function revalidate(): void
    {
        $errors = [];
        $warnings = [];

        $plainQuestion = trim(strip_tags((string) $this->question_html));
        $hasImg = str_contains((string) $this->question_html, '<img');

        if ($plainQuestion === '' && !$hasImg) {
            $errors[] = 'Isi teks/gambar soal kosong.';
        }

        if ($this->question_type === 'mcq') {
            $nonEmptyOptions = array_filter([
                'A' => trim(strip_tags((string) $this->option_a)) !== '' || str_contains((string) $this->option_a, '<img'),
                'B' => trim(strip_tags((string) $this->option_b)) !== '' || str_contains((string) $this->option_b, '<img'),
                'C' => trim(strip_tags((string) $this->option_c)) !== '' || str_contains((string) $this->option_c, '<img'),
                'D' => trim(strip_tags((string) $this->option_d)) !== '' || str_contains((string) $this->option_d, '<img'),
                'E' => trim(strip_tags((string) $this->option_e)) !== '' || str_contains((string) $this->option_e, '<img'),
            ]);

            if (count($nonEmptyOptions) < 2) {
                $errors[] = 'Soal pilihan ganda minimal harus memiliki 2 opsi jawaban.';
            }

            if ($this->correct_answer !== null && trim($this->correct_answer) !== '') {
                $key = strtoupper(trim($this->correct_answer));
                if (!in_array($key, ['A', 'B', 'C', 'D', 'E'], true)) {
                    $errors[] = "Kunci jawaban '{$key}' tidak valid (harus A–E atau dikosongkan).";
                } elseif (!isset($nonEmptyOptions[$key])) {
                    $errors[] = "Kunci jawaban '{$key}' menunjuk ke opsi yang kosong.";
                }
            }
        } elseif ($this->question_type === 'complex_tf') {
            $stmts = is_array($this->statements) ? $this->statements : [];
            if (count($stmts) < 1) {
                $errors[] = 'Soal PG Kompleks Benar/Salah minimal harus memiliki 1 pernyataan.';
            }
        }

        if ($this->has_shape_warning) {
            $warnings[] = 'Diagram/Word Shapes terdeteksi di file Word — unggah atau paste (Ctrl+V) gambar diagram jika belum muncul.';
        }

        // Preserve any non-cleared parser warnings that start with [Parser]
        $existingIssues = is_array($this->validation_issues) ? $this->validation_issues : [];
        foreach ($existingIssues as $issue) {
            if (is_string($issue) && str_starts_with($issue, '[Parser]')) {
                $warnings[] = $issue;
            }
        }

        $warnings = array_values(array_unique($warnings));
        $allIssues = array_merge($errors, $warnings);

        $status = !empty($errors) ? 'error' : (!empty($warnings) ? 'warning' : 'valid');

        $this->update([
            'validation_status' => $status,
            'validation_issues' => $allIssues,
        ]);
    }
}
