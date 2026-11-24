<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestSession extends Model
{
    protected $fillable = [
        'title',
        'cbt_exam_id',
        'test_id',
        'session_date',
        'start_time',
        'end_time',
        'server_name',
        'max_capacity',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'session_date' => 'date',
        'max_capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function cbtExam(): BelongsTo
    {
        return $this->belongsTo(CbtExam::class, 'cbt_exam_id');
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class, 'test_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(TestScheduleRequest::class, 'test_session_id');
    }

    public function approvedSeats(): int
    {
        return (int) $this->requests()->where('status', 'approved')->sum('requested_seats');
    }

    public function pendingSeats(): int
    {
        return (int) $this->requests()->where('status', 'pending')->sum('requested_seats');
    }

    public function remainingCapacity(): int
    {
        return max(0, (int) $this->max_capacity - $this->approvedSeats());
    }

    public function getOptionLabelAttribute(): string
    {
        $date = $this->session_date ? $this->session_date->format('d M Y') : '-';
        $time = substr((string) $this->start_time, 0, 5) . '-' . substr((string) $this->end_time, 0, 5);
        $used = $this->approvedSeats();
        $rem = $this->remainingCapacity();

        return "{$this->title} ({$date} {$time} • {$this->server_name} • Terisi {$used}/{$this->max_capacity}, Sisa {$rem})";
    }
}
