<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyMaterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject_id',
        'type',
        'title',
        'description',
        'youtube_url',
        'mindmap_image',
        'attachment_file',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
