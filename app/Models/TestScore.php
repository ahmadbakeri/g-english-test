<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'student_id',
        'score',
        'notes',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
