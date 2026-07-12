<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        return $this->avatar_url ? \Illuminate\Support\Facades\Storage::url($this->avatar_url) : null;
    }
}
