<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Test extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'class_id',
        'subject_id',
        'name',
        'type',
        'date',
        'max_score',
    ];

    protected $casts = [
        'date' => 'date',
        'max_score' => 'integer',
    ];

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(TestScore::class);
    }
}
