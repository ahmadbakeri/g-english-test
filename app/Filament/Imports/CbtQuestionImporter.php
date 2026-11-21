<?php

namespace App\Filament\Imports;

use App\Models\CbtQuestion;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class CbtQuestionImporter extends Importer
{
    protected static ?string $model = CbtQuestion::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('question_text')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('option_a')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('option_b')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('option_c')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('option_d')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('option_e'),
            ImportColumn::make('correct_answer')
                ->requiredMapping()
                ->rules(['required', 'max:1']),
            ImportColumn::make('points')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
        ];
    }

    public function resolveRecord(): CbtQuestion
    {
        $question = new CbtQuestion();
        $question->cbt_exam_id = $this->options['cbt_exam_id'];
        return $question;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your cbt question import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
