<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CbtAttempt extends Model
{
    protected $fillable = [
        'cbt_exam_id',
        'student_id',
        'attempt_number',
        'start_time',
        'end_time',
        'status',
        'score',
        'is_completed',
        'violation_count',
        'max_violations',
        'question_order',
        'is_force_submitted',
        'last_auto_save_at',
        'external_submission_id',
        'sync_status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_completed' => 'boolean',
        'question_order' => 'array',
        'is_force_submitted' => 'boolean',
        'last_auto_save_at' => 'datetime',
    ];

    public function exam()
    {
        return $this->belongsTo(CbtExam::class, 'cbt_exam_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function answers()
    {
        return $this->hasMany(CbtAnswer::class, 'cbt_attempt_id');
    }}
