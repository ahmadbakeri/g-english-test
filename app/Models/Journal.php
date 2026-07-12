<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'class_id',
        'tutor_id',
        'date',
        'topic',
        'description',
        'materials',
        'jp_count',
        'status',
        'photo',
        'link',
    ];

    protected $casts = [
        'date' => 'date',
        'photo' => 'array',
    ];

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }

    public function validation(): HasOne
    {
        return $this->hasOne(JournalValidation::class);
    }

    public function honors(): HasMany
    {
        return $this->hasMany(Honor::class);
    }
}
