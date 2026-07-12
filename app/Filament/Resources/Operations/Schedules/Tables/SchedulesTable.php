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
            ->columns([
                \Filament\Tables\Columns\Layout\Split::make([
                TextColumn::make('classRoom.name')
                    ->label(__('Class Room'))->translateLabel()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(__('Tutor'))->translateLabel()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('date')
                    ->label(__('Date'))->translateLabel()
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('start_time')
                    ->time()
                    ->sortable(),
                TextColumn::make('end_time')
                    ->time()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ])->from('md'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

