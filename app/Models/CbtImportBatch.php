<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class CbtImportBatch extends Model
{
    protected $fillable = [
        'cbt_exam_id',
        'uploaded_by',
        'title',
        'original_filename',
        'stored_path',
        'media_dir',
        'status',
        'total_questions',
        'valid_count',
        'warning_count',
        'error_count',
        'parse_summary',
        'published_at',
        'published_by',
    ];

    protected $casts = [
        'parse_summary' => 'array',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleting(function (CbtImportBatch $batch) {
            if ($batch->stored_path && Storage::disk('local')->exists($batch->stored_path)) {
                Storage::disk('local')->delete($batch->stored_path);
            }
            // Only remove media directory if batch was NEVER published (published questions reference those images)
            if (!$batch->published_at && $batch->media_dir && Storage::disk('public')->exists($batch->media_dir)) {
                Storage::disk('public')->deleteDirectory($batch->media_dir);
            }
        });
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(CbtExam::class, 'cbt_exam_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function drafts(): HasMany
    {
        return $this->hasMany(CbtQuestionDraft::class, 'batch_id')->orderBy('sequence_no');
    }

    public function recalculateCounts(): void
    {
        $counts = $this->drafts()
            ->reorder()
            ->selectRaw('validation_status, COUNT(*) as cnt')
            ->groupBy('validation_status')
            ->pluck('cnt', 'validation_status');

        $valid = (int) ($counts['valid'] ?? 0);
        $warning = (int) ($counts['warning'] ?? 0);
        $error = (int) ($counts['error'] ?? 0);

        $this->update([
            'total_questions' => $valid + $warning + $error,
            'valid_count' => $valid,
            'warning_count' => $warning,
            'error_count' => $error,
        ]);
    }
}
