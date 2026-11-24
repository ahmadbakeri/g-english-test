<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CbtQuestion extends Model
{
    protected $fillable = [
        'cbt_exam_id',
        'order_number',
        'question_type',
        'question_text',
        'attachment',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'statements',
        'correct_answer',
        'points',
        'explanation',
    ];

    protected $casts = [
        'statements' => 'array',
    ];

    protected static function booted(): void
    {
        $flush = function (CbtQuestion $q) {
            if ($q->cbt_exam_id) {
                \Illuminate\Support\Facades\Cache::forget("exam_q_{$q->cbt_exam_id}");
                \Illuminate\Support\Facades\Cache::forget("exam_grade_{$q->cbt_exam_id}");
            }
        };
        static::saved($flush);
        static::deleted($flush);
    }

    public function exam()
    {
        return $this->belongsTo(CbtExam::class, 'cbt_exam_id');
    }
}
