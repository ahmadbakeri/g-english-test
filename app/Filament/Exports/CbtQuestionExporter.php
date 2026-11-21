<?php

namespace App\Filament\Exports;

use App\Models\CbtQuestion;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class CbtQuestionExporter extends Exporter
{
    protected static ?string $model = CbtQuestion::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('question_text'),
            ExportColumn::make('option_a'),
            ExportColumn::make('option_b'),
            ExportColumn::make('option_c'),
            ExportColumn::make('option_d'),
            ExportColumn::make('option_e'),
            ExportColumn::make('correct_answer'),
            ExportColumn::make('points'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your cbt question export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
