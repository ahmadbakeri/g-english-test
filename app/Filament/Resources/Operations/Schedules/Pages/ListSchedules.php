<?php

namespace App\Filament\Resources\Operations\Schedules\Pages;

use App\Filament\Resources\Operations\Schedules\ScheduleResource;
use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected string $view = 'filament.admin.pages.schedules-accordion';

    public string $search = '';
    public ?string $dateFrom = null;
    public ?string $dateUntil = null;

    /** @var array<int, string> */
    public array $openGroups = [];

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
        $s = Schedule::find($id);
        if (!$s) {
            return;
        }
        $this->editingId = $s->id;
        $this->editForm = [
            'class_room_id' => $s->class_room_id,
            'user_id' => $s->user_id,
            'date' => $s->date ? $s->date->format('Y-m-d') : '',
            'start_time' => substr((string) $s->start_time, 0, 5),
            'end_time' => substr((string) $s->end_time, 0, 5),
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
        $s = Schedule::find($this->editingId);
        if (!$s) {
            $this->cancelInlineEdit();
            return;
        }
        $s->update([
            'class_room_id' => $this->editForm['class_room_id'],
            'user_id' => $this->editForm['user_id'],
            'date' => $this->editForm['date'] ?: null,
            'start_time' => $this->editForm['start_time'],
            'end_time' => $this->editForm['end_time'],
        ]);
        Notification::make()->title('Jadwal diperbarui')->success()->send();
        $this->cancelInlineEdit();
    }

    public function deleteRecord(int $id): void
    {
        $s = Schedule::find($id);
        if ($s) {
            $s->delete();
            Notification::make()->title('Jadwal dihapus')->success()->send();
        }
        if ($this->editingId === $id) {
            $this->cancelInlineEdit();
        }
    }

    public function getGroupedSchedulesProperty(): array
    {
        $needle = mb_strtolower(trim($this->search));
        $query = Schedule::query()
            ->with(['classRoom:id,name', 'user:id,name'])
            ->orderBy('date', 'desc')
            ->orderBy('start_time');

        if ($this->dateFrom) {
            $query->whereDate('date', '>=', $this->dateFrom);
        }
        if ($this->dateUntil) {
            $query->whereDate('date', '<=', $this->dateUntil);
        }

        $items = $query->get();
        if ($needle !== '') {
            $items = $items->filter(fn (Schedule $s): bool => str_contains(
                mb_strtolower(implode(' ', [$s->classRoom?->name, $s->user?->name, $s->date?->format('d/m/Y')])),
                $needle
            ));
        }

        $groups = [];
        foreach ($items as $s) {
            $cls = $s->classRoom?->name ?? 'Tanpa Kelas';
            $groups[$cls][] = $s;
        }
        ksort($groups, SORT_NATURAL);

        return [
            'groups' => $groups,
            'total' => $items->count(),
            'classes' => ClassRoom::orderBy('name')->pluck('name', 'id')->all(),
            'tutors' => User::where('role', 'tutor')->orderBy('name')->pluck('name', 'id')->all(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('qr_code')
                ->label(__('Scan Attendance'))
                ->icon('heroicon-o-qr-code')
                ->url(fn (): string => \App\Filament\Pages\ScanAttendance::getUrl())
                ->openUrlInNewTab(),

            Action::make('generate')
                ->label(__('Generate Daily Schedule'))
                ->color('success')
                ->icon('heroicon-o-arrow-path')
                ->form([
                    DatePicker::make('date')
                        ->label(__('Date'))->translateLabel()
                        ->required()
                        ->default(now()),
                ])
                ->action(function (array $data) {
                    $date = Carbon::parse($data['date']);
                    $dayOfWeek = $date->format('l');
                    if (!$dayOfWeek) return;

                    $classSchedules = ClassSchedule::all();
                    $count = 0;

                    foreach ($classSchedules as $classSchedule) {
                        if ($classSchedule->schedule_items) {
                            foreach ($classSchedule->schedule_items as $item) {
                                if (isset($item['day']) && $item['day'] === $dayOfWeek) {
                                    $tutorId = null;
                                    if ($classSchedule->tutor_id) {
                                        $tutor = \App\Models\Tutor::find($classSchedule->tutor_id);
                                        if ($tutor) {
                                            $tutorId = $tutor->user_id;
                                        }
                                    }

                                    if ($tutorId) {
                                        Schedule::firstOrCreate([
                                            'class_room_id' => $classSchedule->class_id,
                                            'date' => $date->format('Y-m-d'),
                                            'start_time' => $item['start_time'] ?? '00:00',
                                        ], [
                                            'user_id' => $tutorId,
                                            'end_time' => $item['end_time'] ?? '00:00',
                                        ]);
                                        $count++;
                                    }
                                }
                            }
                        }
                    }

                    Notification::make()
                        ->title(__('Schedule Successfully Created'))
                        ->body("Berhasil meng-generate $count jadwal harian untuk tanggal " . $date->format('d/m/Y'))
                        ->success()
                        ->send();
                }),
            CreateAction::make()->slideOver()->modalWidth('2xl'),
        ];
    }
}
