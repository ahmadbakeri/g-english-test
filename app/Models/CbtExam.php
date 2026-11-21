<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CbtExam extends Model
{
    protected $fillable = [
        'title',
        'description',
        'class_id',
        'subject_id',
        'cbt_mode',
        'gform_embed_url',
        'gform_sheet_csv_url',
        'gform_nis_entry_id',
        'gform_name_entry_id',
        'gform_score_column',
        'imported_excel_path',
        'last_synced_at',
        'duration_minutes',
        'max_attempts',
        'is_active',
        'start_time',
        'end_time',
        'random_questions',
        'max_concurrent_users',
        'shuffle_questions',
        'shuffle_options',
        'show_result_immediately',
        'allow_review',
        'auto_save_interval',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'last_synced_at' => 'datetime',
        'random_questions' => 'boolean',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'show_result_immediately' => 'boolean',
        'allow_review' => 'boolean',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions()
    {
        return $this->hasMany(CbtQuestion::class, 'cbt_exam_id');
    }

    public function attempts()
    {
        return $this->hasMany(CbtAttempt::class, 'cbt_exam_id');
    }}
