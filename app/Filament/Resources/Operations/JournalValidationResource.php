<?php

namespace App\Filament\Resources\Operations;

use App\Filament\Resources\Operations\JournalValidationResource\Pages;
use App\Models\JournalValidation;
use App\Models\Journal;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class JournalValidationResource extends Resource
{
    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }

    protected static ?string $model = Journal::class;
    protected static ?string $slug = 'journal-validations';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';
    public static function getNavigationLabel(): string { return __('Journal Validations'); }
    public static function getModelLabel(): string { return __('Journal Validations'); }
    public static function getPluralModelLabel(): string { return __('Journal Validations'); }
    public static function getNavigationGroup(): ?string
    {
        return __('Academic');
    }
    public static function getNavigationSort(): ?int
    {
        return 6;
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('date')
                ->label(__('Date'))->translateLabel()
                ->date('d/m/Y')
                ->description(fn (Journal $record): string => ($record->jp_count ?? 0) . ' JP')
                ->sortable(),
            Tables\Columns\TextColumn::make('topic')
                ->label(__('Topic / Material'))
                ->weight('bold')
                ->description(fn (Journal $record): string => $record->tutor?->name ?? '-')
                ->searchable(),
            Tables\Columns\TextColumn::make('status')->label(__('Status'))
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'draft' => 'gray',
                    'submitted' => 'warning',
                    'validated' => 'success',
                    'rejected' => 'danger',
                    default => 'gray',
                })
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'draft' => 'Draft',
                    'submitted' => 'Pending',
                    'validated' => 'Disetujui',
                    'rejected' => 'Ditolak',
                    default => $state,
                }),
        ])
        ->defaultSort('date', 'desc')
        ->filters([
            Tables\Filters\SelectFilter::make('status')->label(__('Status'))
                ->options([
                    'submitted' => 'Pending (Menunggu Validasi)',
                    'validated' => 'Disetujui',
                    'rejected' => 'Ditolak',
                ])
                ->default('submitted'), // Default to pending
        ])
        ->actions([
            \Filament\Actions\Action::make('approve')
                ->label(__('Approve'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->button()
                ->size('sm')
                ->visible(fn (Journal $record) => $record->status === 'submitted')
                ->requiresConfirmation()
                ->action(function (Journal $record) {
                    \App\Models\JournalValidation::create([
                        'journal_id' => $record->id,
                        'validated_by' => Auth::id(),
                        'status' => 'approved',
                        'notes' => 'Disetujui',
                        'validated_at' => now(),
                    ]);
                    $record->update(['status' => 'validated']);
                    \Filament\Notifications\Notification::make()->title(__('Journal Approved'))->success()->send();
                }),
            \Filament\Actions\Action::make('reject')
                ->label(__('Reject'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->button()
                ->size('sm')
                ->visible(fn (Journal $record) => $record->status === 'submitted')
                ->form([
                    Forms\Components\Textarea::make('notes')->label(__('Rejection Reason'))->required(),
                ])
                ->action(function (array $data, Journal $record) {
                    \App\Models\JournalValidation::create([
                        'journal_id' => $record->id,
                        'validated_by' => Auth::id(),
                        'status' => 'rejected',
                        'notes' => $data['notes'],
                        'validated_at' => now(),
                    ]);
                    $record->update(['status' => 'rejected']);
                    \Filament\Notifications\Notification::make()->title(__('Journal Rejected'))->danger()->send();
                }),
        ])
        ->bulkActions([
            // Use the bulk validation action defined in ListJournalValidations page
        ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['classRoom', 'tutor', 'validation']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJournalValidations::route('/'),
        ];
    }
}



