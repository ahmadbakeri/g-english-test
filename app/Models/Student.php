<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nis',
        'name',
        'gender',
        'birth_date',
        'address',
        'phone',
        'mother_name',
        'mother_phone',
        'mother_email',
        'mother_job',
        'father_name',
        'father_phone',
        'father_email',
        'father_job',
        'photo',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::created(function (Student $student) {
            // Create user account automatically
            if (!$student->user_id) {
                $latestUser = User::where('role', 'siswa')->whereNotNull('username')->orderBy('id', 'desc')->first();
                $nextNum = $latestUser ? intval($latestUser->username) + 1 : 481001;
                $user = User::create([
                    'username' => (string) $nextNum,
                    'name' => $student->name,
                    'email' => strtolower($student->nis) . '@g-english.com',
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'role' => 'siswa',
                ]);
                $student->user_id = $user->id;
                $student->saveQuietly();
            }
        });

        static::deleting(function (Student $student) {
            if ($student->isForceDeleting() && $student->user) {
                $student->user->delete();
            }
        });
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(ClassRoom::class, 'class_student', 'student_id', 'class_id')
            ->withPivot('enrolled_at')
            ->withTimestamps();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function testScores(): HasMany
    {
        return $this->hasMany(TestScore::class);
    }

    public function honors(): HasMany
    {
        return $this->hasMany(Honor::class);
    }
}
