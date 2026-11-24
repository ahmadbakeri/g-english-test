<?php

namespace App\Filament\Resources\HR\TutorResource\Pages;

use App\Filament\Resources\HR\TutorResource;
use App\Models\Tutor;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class ListTutors extends ListRecords
{
    use WithFileUploads;

    protected static string $resource = TutorResource::class;

    protected string $view = 'filament.admin.pages.tutors-accordion';

    public string $search = '';

    /** @var array<int, string> */
    public array $openGroups = ['Grade A', 'Grade B', 'Grade C'];

    public ?int $editingId = null;

    public $editPhoto = null;

    /** @var array<string, mixed> */
    public array $editForm = [];

    public function toggleGroup(string $group): void
    {
        if (in_array($group, $this->openGroups, true)) {
            $this->openGroups = array_values(array_diff($this->openGroups, [$group]));
        } else {
            $this->openGroups[] = $group;
        }
    }

    public function startInlineEdit(int $id): void
    {
        if ($this->editingId === $id) {
            $this->cancelInlineEdit();
            return;
        }

        $t = Tutor::with('user')->find($id);
        if (!$t) {
            return;
        }

        $this->editingId = $t->id;
        $this->editPhoto = null;
        $this->editForm = [
            'name' => (string) $t->name,
            'nip' => (string) ($t->nip ?? ''),
            'username' => (string) ($t->user?->username ?? ''),
            'email' => (string) ($t->user?->email ?? ''),
            'new_password' => '',
            'phone' => (string) ($t->phone ?? ''),
            'specialization' => (string) ($t->specialization ?? ''),
            'grade' => (string) ($t->grade ?: 'A'),
            'is_active' => (bool) $t->is_active,
            'current_photo' => (string) ($t->photo ?? ''),
        ];
    }

    public function cancelInlineEdit(): void
    {
        $this->editingId = null;
        $this->editPhoto = null;
        $this->editForm = [];
    }

    public function removeTutorPhoto(): void
    {
        if (!$this->editingId) {
            return;
        }

        $t = Tutor::find($this->editingId);
        if (!$t) {
            return;
        }

        if ($t->photo) {
            Storage::disk('public')->delete($t->photo);
            $t->update(['photo' => null]);
        }
        $this->editPhoto = null;
        $this->editForm['current_photo'] = '';

        Notification::make()->title('Foto tutor dihapus')->success()->send();
    }

    public function saveInlineEdit(): void
    {
        if (!$this->editingId) {
            return;
        }
        $t = Tutor::with('user')->find($this->editingId);
        if (!$t) {
            $this->cancelInlineEdit();
            return;
        }

        $name = trim((string) ($this->editForm['name'] ?? $t->name));
        $nip = trim((string) ($this->editForm['nip'] ?? ''));
        $username = trim((string) ($this->editForm['username'] ?? ''));
        $email = strtolower(trim((string) ($this->editForm['email'] ?? '')));
        $newPassword = trim((string) ($this->editForm['new_password'] ?? ''));

        if ($name === '') {
            Notification::make()->title('Nama tutor wajib diisi')->danger()->send();
            return;
        }

        if ($nip !== '' && Tutor::where('nip', $nip)->where('id', '!=', $t->id)->exists()) {
            Notification::make()->title("ID Buatan / NIP '{$nip}' sudah dipakai tutor lain")->danger()->send();
            return;
        }

        $userId = $t->user?->id;
        if ($username !== '' && User::where('username', $username)->when($userId, fn ($q) => $q->where('id', '!=', $userId))->exists()) {
            Notification::make()->title("Username '{$username}' sudah digunakan akun lain")->danger()->send();
            return;
        }

        if ($email !== '') {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Notification::make()->title('Format email tidak valid')->danger()->send();
                return;
            }
            if (User::whereRaw('LOWER(email) = ?', [$email])->when($userId, fn ($q) => $q->where('id', '!=', $userId))->exists()) {
                Notification::make()->title("Email '{$email}' sudah digunakan akun lain")->danger()->send();
                return;
            }
        }

        $photoPath = $t->photo;
        if ($this->editPhoto) {
            $this->validate([
                'editPhoto' => 'image|max:4096',
            ]);
            if ($t->photo) {
                Storage::disk('public')->delete($t->photo);
            }
            $photoPath = $this->editPhoto->store('tutors', 'public');
        }

        $t->update([
            'name' => $name,
            'nip' => $nip !== '' ? $nip : null,
            'phone' => trim((string) ($this->editForm['phone'] ?? '')) ?: null,
            'specialization' => trim((string) ($this->editForm['specialization'] ?? '')) ?: null,
            'grade' => (string) ($this->editForm['grade'] ?? 'A'),
            'is_active' => (bool) ($this->editForm['is_active'] ?? true),
            'photo' => $photoPath,
        ]);

        if ($t->user) {
            $userUpdates = ['name' => $name];
            if ($username !== '') {
                $userUpdates['username'] = $username;
            }
            if ($email !== '') {
                $userUpdates['email'] = $email;
            }
            if ($newPassword !== '') {
                $userUpdates['password'] = Hash::make($newPassword);
            }
            $t->user->update($userUpdates);
        } else {
            $resolvedUsername = $username !== '' ? $username : ($nip !== '' ? $nip : 'tutor_' . $t->id);
            $resolvedEmail = $email !== '' ? $email : strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $resolvedUsername)) . '@g-english.com';
            $u = User::create([
                'name' => $name,
                'username' => $resolvedUsername,
                'email' => $resolvedEmail,
                'password' => Hash::make($newPassword !== '' ? $newPassword : 'ganzajaya'),
                'role' => 'tutor',
            ]);
            $t->update(['user_id' => $u->id]);
        }

        Notification::make()->title('Data & akun login tutor diperbarui')->success()->send();
        $this->cancelInlineEdit();
    }

    public function deleteRecord(int $id): void
    {
        $t = Tutor::find($id);
        if ($t) {
            $t->delete();
            Notification::make()->title('Tutor dihapus')->success()->send();
        }
        if ($this->editingId === $id) {
            $this->cancelInlineEdit();
        }
    }

    public function getGroupedTutorsProperty(): array
    {
        $needle = mb_strtolower(trim($this->search));
        $tutors = Tutor::query()->with('user:id,email,username,avatar_url')->orderBy('grade')->orderBy('name')->get();

        if ($needle !== '') {
            $tutors = $tutors->filter(fn (Tutor $t): bool => str_contains(
                mb_strtolower(implode(' ', [$t->name, $t->nip, $t->phone, $t->specialization, $t->grade, $t->user?->username, $t->user?->email])),
                $needle
            ));
        }

        $groups = [];
        foreach ($tutors as $t) {
            $key = 'Grade ' . ($t->grade ?: 'Lainnya');
            $groups[$key][] = $t;
        }
        ksort($groups);

        return ['groups' => $groups, 'total' => $tutors->count(), 'active' => $tutors->where('is_active', true)->count()];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Add Tutor'))
                ->slideOver()
                ->modalWidth('2xl')
                ->after(function (Tutor $record, array $data): void {
                    $record->refresh();
                    if (!$record->user) {
                        return;
                    }
                    $updates = [];
                    $customUsername = trim((string) ($data['login_username'] ?? ''));
                    $customEmail = strtolower(trim((string) ($data['login_email'] ?? '')));
                    $customPassword = trim((string) ($data['login_password'] ?? ''));

                    if ($customUsername !== '' && !User::where('username', $customUsername)->where('id', '!=', $record->user->id)->exists()) {
                        $updates['username'] = $customUsername;
                    }
                    if ($customEmail !== '' && filter_var($customEmail, FILTER_VALIDATE_EMAIL) && !User::whereRaw('LOWER(email) = ?', [$customEmail])->where('id', '!=', $record->user->id)->exists()) {
                        $updates['email'] = $customEmail;
                    }
                    if ($customPassword !== '') {
                        $updates['password'] = Hash::make($customPassword);
                    }
                    if (!empty($updates)) {
                        $record->user->update($updates);
                    }
                }),
        ];
    }
}
