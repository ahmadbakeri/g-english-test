<?php

namespace App\Filament\Resources\Academic\ClassRoomResource\Pages;

use App\Filament\Resources\Academic\ClassRoomResource;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Tutor;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListClassRooms extends ListRecords
{
    protected static string $resource = ClassRoomResource::class;

    protected string $view = 'filament.admin.pages.classrooms-accordion';

    public string $search = '';

    /** @var array<int, string> */
    public array $openGroups = [];

    public ?int $editingId = null;

    /** @var array<string, mixed> */
    public array $editForm = [];

    public function mount(): void
    {
        parent::mount();
        $firstYear = ClassRoom::with('academicYear')->first()?->academicYear?->name;
        if ($firstYear) {
            $this->openGroups = [$firstYear];
        }
    }

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
        $c = ClassRoom::find($id);
        if (!$c) {
            return;
        }

        $this->editingId = $c->id;
        $this->editForm = [
            'name' => (string) $c->name,
            'subject_id' => $c->subject_id,
            'tutor_id' => $c->tutor_id,
            'schedule' => (string) ($c->schedule ?? ''),
            'capacity' => (int) ($c->capacity ?? 12),
            'is_active' => (bool) $c->is_active,
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
        $c = ClassRoom::find($this->editingId);
        if (!$c) {
            $this->cancelInlineEdit();
            return;
        }

        $c->update([
            'name' => trim((string) ($this->editForm['name'] ?? $c->name)),
            'subject_id' => $this->editForm['subject_id'] ?: null,
            'tutor_id' => $this->editForm['tutor_id'] ?: null,
            'schedule' => trim((string) ($this->editForm['schedule'] ?? '')) ?: null,
            'capacity' => max(1, (int) ($this->editForm['capacity'] ?? 12)),
            'is_active' => (bool) ($this->editForm['is_active'] ?? true),
        ]);

        Notification::make()->title('Data kelas diperbarui')->success()->send();
        $this->cancelInlineEdit();
    }

    public function deleteRecord(int $id): void
    {
        $c = ClassRoom::find($id);
        if ($c) {
            $c->delete();
            Notification::make()->title('Kelas dihapus')->success()->send();
        }
        if ($this->editingId === $id) {
            $this->cancelInlineEdit();
        }
    }

    public function getGroupedClassesProperty(): array
    {
        $needle = mb_strtolower(trim($this->search));
        $classes = ClassRoom::query()
            ->with(['academicYear:id,name', 'subject:id,name', 'tutor:id,name'])
            ->withCount('students')
            ->orderBy('name')
            ->get();

        if ($needle !== '') {
            $classes = $classes->filter(fn (ClassRoom $c): bool => str_contains(
                mb_strtolower(implode(' ', [$c->name, $c->academicYear?->name, $c->subject?->name, $c->tutor?->name, $c->schedule])),
                $needle
            ));
        }

        $groups = [];
        foreach ($classes as $c) {
            $year = $c->academicYear?->name ?? 'Tanpa Tahun Ajaran';
            $groups[$year][] = $c;
        }
        krsort($groups);

        return [
            'groups' => $groups,
            'total' => $classes->count(),
            'subjects' => Subject::orderBy('name')->pluck('name', 'id')->all(),
            'tutors' => Tutor::orderBy('name')->pluck('name', 'id')->all(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label(__('Add Class'))->slideOver()->modalWidth('3xl'),
        ];
    }
}
