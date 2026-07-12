<?php

namespace App\Filament\Tutor\Widgets;

use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ClassSchedule;
use App\Models\Tutor;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Carbon;

class TutorTodayScheduleWidget extends TableWidget
{
    protected static ?int $sort = 3;
    
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $today = now()->format('l');

        $user = auth()->user();
        $tutor = Tutor::where('user_id', $user->id)->first();
        $tutorId = $tutor?->id;

        return $table
            ->heading('📅 ' . __('Today\'s Teaching Schedule'))
            ->query(
                fn (): Builder => ClassSchedule::query()
                    ->whereJsonContains('schedule_items', ['day' => $today])
                    ->when($tutorId, fn ($q) => $q->whereHas('classRoom', fn ($q2) => $q2->where('tutor_id', $tutorId)))
                    
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
                TextColumn::make('class_id')
                    ->label(__('Number of Students'))
                    ->formatStateUsing(function ($record) {
                        return $record->classRoom?->students()->count() . ' ' . __('students');
                    })
                    ->icon('heroicon-o-user-group'),
                ])->from('md'),
            ])
            ->emptyStateHeading(__('No schedule today'))
            ->emptyStateDescription(__('You have no classes scheduled for today.'))
            ->emptyStateIcon('heroicon-o-calendar')
            ->paginated(false);
    }
}
