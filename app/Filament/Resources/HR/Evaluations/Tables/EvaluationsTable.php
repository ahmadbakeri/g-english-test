<?php

namespace App\Filament\Resources\HR\Evaluations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EvaluationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\Layout\Split::make([
                TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('subject.name')
                    ->label(__('Subject'))->translateLabel()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('score')
                    ->label(__('Score'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('date')
                    ->date()
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
                \Filament\Actions\Action::make('cetak_rapor')
                    ->label(__('Print'))
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn ($record) => route('report.evaluation', $record->id))
                    ->openUrlInNewTab(),
                \Filament\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

