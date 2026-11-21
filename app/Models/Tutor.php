<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tutor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nip',
        'name',
        'phone',
        'specialization',
        'grade',
        'photo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(ClassRoom::class);
    }

    public function salaries(): HasMany
    {
        return $this->hasMany(TutorSalary::class, 'tutor_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'tutor_id');
    }

    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class);
    }

    protected static function booted(): void
    {
        static::created(function (Tutor $tutor) {
            // Create user account automatically if not set
            if (!$tutor->user_id) {
                $maxNum = User::where('role', 'tutor')
                    ->whereNotNull('username')
                    ->pluck('username')
                    ->filter(fn ($u) => ctype_digit((string) $u))
                    ->map(fn ($u) => (int) $u)
                    ->max();

                $nextNum = $maxNum && $maxNum >= 48000 ? $maxNum + 1 : 48001;
                while (User::where('username', (string) $nextNum)->exists()) {
                    $nextNum++;
                }

                $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $tutor->name)) ?: 'tutor';
                $baseEmail = $slug . '@g-english.com';
                if (User::where('email', $baseEmail)->exists()) {
                    $baseEmail = $slug . '_' . $nextNum . '@g-english.com';
                }

                $user = User::create([
                    'username' => (string) $nextNum,
                    'name' => $tutor->name,
                    'email' => $baseEmail,
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'role' => 'tutor',
                ]);
                $tutor->user_id = $user->id;
                $tutor->saveQuietly();
            }
        });

        static::deleting(function (Tutor $tutor) {
            if ($tutor->isForceDeleting() && $tutor->user) {
                $tutor->user->delete();
            }
        });
    }
}
