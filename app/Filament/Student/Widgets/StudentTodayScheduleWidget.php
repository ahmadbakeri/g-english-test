<?php

namespace App\Filament\Student\Widgets;

use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ClassSchedule;
use App\Models\Student;
use Filament\Tables\Columns\TextColumn;

class StudentTodayScheduleWidget extends TableWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 3;
    
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $today = now()->format('l');

        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->first();
        $studentId = $student?->id;

        return $table
            ->heading('Ã°Å¸â€œâ€¦ ' . __('Today\'s Class Schedule'))
            ->query(
                fn (): Builder => ClassSchedule::query()
                    ->whereJsonContains('schedule_items', ['day' => $today])
                    ->when($studentId, fn ($q) => $q->whereHas('classRoom.students', fn ($q2) => $q2->where('students.id', $studentId)))
                    
            )
            ->columns([
                \Filament\Tables\Columns\Layout\Split::make([
                TextColumn::make('classRoom.name')
                    ->label(__('Class'))
                    ->icon('heroicon-o-building-library')
                    ->weight('bold'),
                TextColumn::make('classRoom.subject.name')
                    ->label(__('Subject'))
                    ->icon('heroicon-o-book-open'),
                TextColumn::make('classRoom.tutor.name')
                    ->label(__('Tutor'))
                    ->icon('heroicon-o-user'),
                                TextColumn::make('start_time')
                    ->label(__('Start Time'))
                    ->getStateUsing(function ($record) use ($today) {
                        $items = $record->schedule_items ?? [];
                        foreach ($items as $item) {
                            if (($item['day'] ?? '') === $today) return $item['start_time'] ?? '-';
                        }
                        return '-';
                    })
                    ->badge()
                    ->color('success'),
                TextColumn::make('end_time')
                    ->label(__('End Time'))
                    ->getStateUsing(function ($record) use ($today) {
                        $items = $record->schedule_items ?? [];
                        foreach ($items as $item) {
                            if (($item['day'] ?? '') === $today) return $item['end_time'] ?? '-';
                        }
                        return '-';
                    })
                    ->badge()
                    ->color('gray'),
                ])->from('md'),
            ])
            ->emptyStateHeading(__('No schedule today'))
            ->emptyStateDescription(__('You have no classes scheduled for today. Have a good rest! Ã°Å¸Å½â€°'))
            ->emptyStateIcon('heroicon-o-calendar')
            ->paginated(false);
    }
}
