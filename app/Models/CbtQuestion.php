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

    public function exam()
    {
        return $this->belongsTo(CbtExam::class, 'cbt_exam_id');
    }
}
