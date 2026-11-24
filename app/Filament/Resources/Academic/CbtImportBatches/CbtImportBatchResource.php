<?php

namespace App\Filament\Resources\Academic\CbtImportBatches;

use App\Filament\Resources\Academic\CbtImportBatches\Pages\ListCbtImportBatches;
use App\Filament\Resources\Academic\CbtImportBatches\Pages\ReviewCbtImportBatch;
use App\Models\CbtImportBatch;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CbtImportBatchResource extends Resource
{
    protected static ?string $model = CbtImportBatch::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return 'Import Soal Word (.docx)';
    }

    public static function getModelLabel(): string
    {
        return 'Draft Import Soal Word';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Staging Import Soal Word (.docx)';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Exams & Evaluation');
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Paket / Batch')
                    ->weight('bold')
                    ->color('primary')
                    ->description(fn (CbtImportBatch $record): string => $record->original_filename)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('exam.title')
                    ->label('Target Ujian CBT')
                    ->placeholder('Belum dipilih (pilih saat publish)')
                    ->badge()
                    ->color(fn (?string $state): string => filled($state) ? 'info' : 'gray')
                    ->searchable(),
                TextColumn::make('total_questions')
                    ->label('Total')
                    ->suffix(' soal')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('valid_count')
                    ->label('Siap')
                    ->badge()
                    ->color('success')
                    ->alignCenter(),
                TextColumn::make('warning_count')
                    ->label('Perlu Cek')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'warning' : 'gray')
                    ->alignCenter(),
                TextColumn::make('error_count')
                    ->label('Error')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'gray')
                    ->alignCenter(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'parsed' => 'Staging / Draft',
                        'published' => 'Dipublish',
                        'failed' => 'Gagal Parse',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'parsed' => 'warning',
                        'published' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Diupload')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('review')
                    ->label('Preview & Edit')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->button()
                    ->size('sm')
                    ->url(fn (CbtImportBatch $record): string => static::getUrl('review', ['record' => $record])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCbtImportBatches::route('/'),
            'review' => ReviewCbtImportBatch::route('/{record}/review'),
        ];
    }
}
