<?php

namespace App\Filament\Resources\Operations\Schedules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordAction('edit')
            ->columns([
                TextColumn::make('classRoom.name')
                    ->label(__('Class Room'))->translateLabel()
                    ->weight('bold')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(__('Tutor'))->translateLabel()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('date')
                    ->label(__('Date'))->translateLabel()
                    ->date('d/m/Y')
                    ->description(fn ($record): string => substr((string) $record->start_time, 0, 5) . ' - ' . substr((string) $record->end_time, 0, 5))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('start_time')
                    ->time()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('end_time')
                    ->time()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                EditAction::make()
                    ->button()
                    ->size('sm'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

