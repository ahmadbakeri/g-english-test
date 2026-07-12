<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutorSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'tutor_id',
        'month',
        'year',
        'total_jp',
        'total_amount',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'date',
    ];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class);
    }
}
