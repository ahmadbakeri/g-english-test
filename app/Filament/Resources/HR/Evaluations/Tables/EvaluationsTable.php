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
            ->recordAction('edit')
            ->columns([
                TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->weight('bold')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('subject.name')
                    ->label(__('Subject'))->translateLabel()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('score')
                    ->label(__('Score'))
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('date')
                    ->date('d/m/Y')
                    ->sortable(),
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
                \Filament\Actions\EditAction::make()
                    ->button()
                    ->size('sm'),
                \Filament\Actions\Action::make('cetak_rapor')
                    ->label(__('Print'))
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->button()
                    ->size('sm')
                    ->url(fn ($record) => route('report.evaluation', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

