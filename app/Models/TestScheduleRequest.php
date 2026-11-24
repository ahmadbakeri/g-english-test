<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class TestScheduleRequest extends Model
{
    protected $fillable = [
        'test_session_id',
        'requester_user_id',
        'requester_role',
        'school_name',
        'student_id',
        'requested_seats',
        'status',
        'notes',
        'review_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'requested_seats' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(TestSession::class, 'test_session_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_user_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Atomically approve this request while checking server capacity per session.
     *
     * @return array{ok: bool, message: string}
     */
    public function approveBy(User $reviewer, ?string $reviewNotes = null): array
    {
        return DB::transaction(function () use ($reviewer, $reviewNotes): array {
            $session = TestSession::whereKey($this->test_session_id)->lockForUpdate()->first();
            if (!$session) {
                return ['ok' => false, 'message' => 'Sesi tes tidak ditemukan.'];
            }

            $approvedSeats = (int) TestScheduleRequest::where('test_session_id', $session->id)
                ->where('status', 'approved')
                ->where('id', '!=', $this->id)
                ->sum('requested_seats');

            $remaining = max(0, (int) $session->max_capacity - $approvedSeats);
            if ($this->requested_seats > $remaining) {
                return [
                    'ok' => false,
                    'message' => "Kapasitas {$session->server_name} tidak cukup! Diminta {$this->requested_seats} kursi, sisa {$remaining}/{$session->max_capacity}.",
                ];
            }

            $this->update([
                'status' => 'approved',
                'review_notes' => $reviewNotes ?: 'Disetujui sesuai kapasitas server.',
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            return [
                'ok' => true,
                'message' => "Jadwal disetujui ({$this->requested_seats} kursi). Sisa kapasitas server: " . ($remaining - $this->requested_seats) . "/{$session->max_capacity}.",
            ];
        });
    }

    public function rejectBy(User $reviewer, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'review_notes' => $reason,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);
    }
}
