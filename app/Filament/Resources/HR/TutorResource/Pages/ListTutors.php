<?php

namespace App\Filament\Resources\HR\TutorResource\Pages;

use App\Filament\Resources\HR\TutorResource;
use App\Models\Tutor;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListTutors extends ListRecords
{
    protected static string $resource = TutorResource::class;

    protected string $view = 'filament.admin.pages.tutors-accordion';

    public string $search = '';

    /** @var array<int, string> */
    public array $openGroups = ['Grade A', 'Grade B', 'Grade C'];

    public ?int $editingId = null;

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

        $t = Tutor::find($id);
        if (!$t) {
            return;
        }

        $this->editingId = $t->id;
        $this->editForm = [
            'name' => (string) $t->name,
            'nip' => (string) ($t->nip ?? ''),
            'phone' => (string) ($t->phone ?? ''),
            'specialization' => (string) ($t->specialization ?? ''),
            'grade' => (string) ($t->grade ?: 'A'),
            'is_active' => (bool) $t->is_active,
        ];
    }

    public function cancelInlineEdit(): void
    {
        $this->editingId = null;
        $this->editForm = [];
    }

    public function saveInlineEdit(): void
    {
        if (!$this->editingId) {
            return;
        }
        $t = Tutor::find($this->editingId);
        if (!$t) {
            $this->cancelInlineEdit();
            return;
        }

        $t->update([
            'name' => trim((string) ($this->editForm['name'] ?? $t->name)),
            'nip' => trim((string) ($this->editForm['nip'] ?? $t->nip)),
            'phone' => trim((string) ($this->editForm['phone'] ?? '')) ?: null,
            'specialization' => trim((string) ($this->editForm['specialization'] ?? '')) ?: null,
            'grade' => (string) ($this->editForm['grade'] ?? 'A'),
            'is_active' => (bool) ($this->editForm['is_active'] ?? true),
        ]);

        if ($t->user) {
            $t->user->update(['name' => $t->name]);
        }

        Notification::make()->title('Data tutor diperbarui')->success()->send();
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
        $tutors = Tutor::query()->with('user:id,email,username')->orderBy('grade')->orderBy('name')->get();

        if ($needle !== '') {
            $tutors = $tutors->filter(fn (Tutor $t): bool => str_contains(
                mb_strtolower(implode(' ', [$t->name, $t->nip, $t->phone, $t->specialization, $t->grade, $t->user?->email])),
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
            Actions\CreateAction::make()->label(__('Add Tutor'))->slideOver()->modalWidth('2xl'),
        ];
    }
}
