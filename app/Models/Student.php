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
        'school_user_id',
        'nis',
        'nisn',
        'competition_code',
        'student_type',
        'name',
        'gender',
        'birth_date',
        'address',
        'phone',
        'parent_name',
        'parent_phone',
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
        'birth_place',
        'religion',
        'social_media',
        'education_level',
        'course_level',
        'class_name',
        'school_name',
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
                $maxNum = User::where('role', 'siswa')
                    ->whereNotNull('username')
                    ->pluck('username')
                    ->filter(fn ($u) => ctype_digit((string) $u))
                    ->map(fn ($u) => (int) $u)
                    ->max();

                $nextNum = $maxNum && $maxNum >= 481000 ? $maxNum + 1 : 481001;
                while (User::where('username', (string) $nextNum)->exists()) {
                    $nextNum++;
                }

                $baseEmail = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $student->nis ?: 'student')) . '@g-english.com';
                if (User::where('email', $baseEmail)->exists()) {
                    $baseEmail = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $student->nis ?: 'student')) . '_' . $nextNum . '@g-english.com';
                }

                $user = User::create([
                    'username' => (string) $nextNum,
                    'name' => $student->name,
                    'email' => $baseEmail,
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'role' => 'siswa',
                ]);
                $student->user_id = $user->id;
                $student->saveQuietly();
            }
        });

        static::saved(function (Student $student) {
            // Auto-provision & Sync Parent / Guardian Users
            $parentsToSync = [];

            $provisionParent = function ($phone, $name, $email = null) use (&$parentsToSync) {
                $phone = trim((string) $phone);
                if ($phone !== '') {
                    $resolvedName = trim((string) $name) !== '' ? trim((string) $name) : 'Orang Tua';
                    $resolvedEmail = trim((string) $email) !== '' ? trim((string) $email) : ($phone . '@parent.g-english.com');

                    $user = User::firstOrCreate(
                        ['username' => $phone],
                        [
                            'name' => $resolvedName,
                            'email' => $resolvedEmail,
                            'password' => \Illuminate\Support\Facades\Hash::make($phone),
                            'role' => 'parent',
                        ]
                    );

                    // Sync updated name/email directly to linked parent User account
                    $updates = [];
                    if (trim((string) $name) !== '' && $user->name !== $resolvedName) {
                        $updates['name'] = $resolvedName;
                    }
                    if (trim((string) $email) !== '' && $user->email !== $resolvedEmail && !User::where('email', $resolvedEmail)->where('id', '!=', $user->id)->exists()) {
                        $updates['email'] = $resolvedEmail;
                    }
                    if (!empty($updates)) {
                        $user->update($updates);
                    }

                    $parentsToSync[] = $user->id;
                }
            };

            $provisionParent($student->father_phone, $student->father_name, $student->father_email);
            $provisionParent($student->mother_phone, $student->mother_name, $student->mother_email);
            $provisionParent($student->parent_phone, $student->parent_name);

            if (count($parentsToSync) > 0) {
                $student->parents()->sync(array_unique($parentsToSync));
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

    public function cbtAttempts(): HasMany
    {
        return $this->hasMany(CbtAttempt::class);
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_parent');
    }
}
