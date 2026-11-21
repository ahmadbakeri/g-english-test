<?php

namespace App\Filament\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Schedule;
use Filament\Tables\Columns\TextColumn;

class TodayScheduleWidget extends TableWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 2; // Position after StatsOverview
    protected static ?string $heading = 'Jadwal Kelas Hari Ini';

    public function table(Table $table): Table
    {
        $today = now()->format('l');

        return $table
            ->query(fn (): Builder => ClassSchedule::query()->with(['classRoom', 'tutor.user'])->whereJsonContains('schedule_items', ['day' => $today]))
            ->columns([
                \Filament\Tables\Columns\Layout\Split::make([
                TextColumn::make('classRoom.name')
                    ->label(__('Class Room')),
                TextColumn::make('tutor.user.name')
                    ->label(__('Tutor')),
                TextColumn::make('start_time')
                    ->label(__('Start Time'))
                    ->time('H:i'),
                TextColumn::make('end_time')
                    ->label(__('End Time'))
                    ->time('H:i'),
                ])->from('md'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
