<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CbtAnswer extends Model
{
    protected $fillable = [
        'cbt_attempt_id',
        'cbt_question_id',
        'selected_answer',
        'answer_payload',
        'is_correct',
    ];

    protected $casts = [
        'answer_payload' => 'array',
        'is_correct' => 'boolean',
    ];

    public function attempt()
    {
        return $this->belongsTo(CbtAttempt::class, 'cbt_attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(CbtQuestion::class, 'cbt_question_id');
    }
}
