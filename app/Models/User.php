<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['username', 'name', 'email', 'password', 'role', 'avatar_url'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use \NotificationChannels\WebPush\HasPushSubscriptions;

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->isAdmin();
        }
        
        if ($panel->getId() === 'tutor') {
            return $this->isTutor();
        }
        
        if ($panel->getId() === 'student') {
            return $this->role === 'siswa';
        }
        
        if ($panel->getId() === 'parent') {
            return $this->role === 'parent';
        }

        if ($panel->getId() === 'school') {
            return $this->role === 'school';
        }

        if ($panel->getId() === 'agency') {
            return in_array($this->role, ['kemenag', 'kemendikdasmen'], true);
        }
        
        return false;
    }

    public function validations(): HasMany
    {
        return $this->hasMany(JournalValidation::class, 'validated_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    public function tutor(): HasOne
    {
        return $this->hasOne(Tutor::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function schoolStudents(): HasMany
    {
        return $this->hasMany(Student::class, 'school_user_id');
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_parent');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTutor(): bool
    {
        return $this->role === 'tutor';
    }

    public static function avatarDisk(): string
    {
        return filled(config('filesystems.disks.r2.bucket')) && filled(config('filesystems.disks.r2.key'))
            ? 'r2'
            : 'public';
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->avatar_url) {
            $disk = self::avatarDisk();
            if ($disk === 'r2') {
                return route('avatar.proxy', ['path' => $this->avatar_url]);
            }
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->avatar_url);
        }

        if ($this->role === 'tutor' && $this->tutor?->photo) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->tutor->photo);
        }

        if ($this->role === 'siswa' && $this->student?->photo) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->student->photo);
        }

        return null;
    }

    public function getInitialsAttribute(): string
    {
        $parts = collect(explode(' ', trim((string) $this->name)))->filter();
        $letters = $parts->map(fn ($s) => mb_substr($s, 0, 1))->join('');
        return mb_strtoupper(mb_substr($letters ?: 'G', 0, 2));
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Administrator',
            'tutor' => 'Tutor Pengajar',
            'siswa' => 'Siswa',
            'parent' => 'Orang Tua / Wali',
            'school' => 'Koordinator Sekolah',
            'kemenag' => 'Kementerian Agama',
            'kemendikdasmen' => 'Kemendikdasmen',
            default => ucfirst((string) $this->role),
        };
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->photo_url) {
            return $this->photo_url;
        }

        // Fast local SVG generation (no external API calls to ui-avatars.com which is slow)
        $initials = $this->initials;
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" fill="#0a4282"/><text x="50%" y="50%" dominant-baseline="central" text-anchor="middle" fill="#fbb900" font-family="sans-serif" font-size="40" font-weight="800">' . $initials . '</text></svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
