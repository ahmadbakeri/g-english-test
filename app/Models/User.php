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

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->avatar_url) {
            return \Illuminate\Support\Facades\Storage::disk('r2')->url($this->avatar_url);
        }

        // Fast local SVG generation (no external API calls to ui-avatars.com which is slow)
        $name = trim(collect(explode(' ', $this->name))->map(fn ($s) => mb_substr($s, 0, 1))->join(''));
        $initials = strtoupper(mb_substr($name, 0, 2));
        
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" fill="#1e3a8a"/><text x="50%" y="50%" dominant-baseline="central" text-anchor="middle" fill="#ffffff" font-family="sans-serif" font-size="40" font-weight="600">'.$initials.'</text></svg>';
        
        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
