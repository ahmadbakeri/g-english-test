<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalValidation extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_id',
        'validated_by',
        'status',
        'notes',
        'validated_at',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
    ];

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    protected static function booted()
    {
        static::created(function ($validation) {
            $journal = $validation->journal;
            if ($journal) {
                $journal->status = $validation->status === 'approved' ? 'validated' : 'rejected';
                $journal->save();
            }
        });
        
        static::updated(function ($validation) {
            $journal = $validation->journal;
            if ($journal) {
                $journal->status = $validation->status === 'approved' ? 'validated' : 'rejected';
                $journal->save();
            }
        });
    }
}
