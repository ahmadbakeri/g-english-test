<?php

namespace App\Filament\Resources\Academic\ClassSchedules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClassSchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordAction('edit')
            ->columns([
                TextColumn::make('classRoom.name')
                    ->label(__('Class Room'))->translateLabel()
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('tutor.name')
                    ->label(__('Tutor'))->translateLabel()
                    ->searchable(),
                TextColumn::make('schedule_items')
                    ->label(__('Day & Time'))
                    ->formatStateUsing(function ($state) {
                        if (!is_array($state)) return '-';
                        $day = $state['day'] ?? '';
                        $start = $state['start_time'] ?? '';
                        $end = $state['end_time'] ?? '';
                        if (!$day) return '-';
                        return "$day ($start - $end)";
                    })
                    ->listWithLineBreaks()
                    ->bulleted(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->button()->size('sm'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

