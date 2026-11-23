<?php

namespace App\Filament\Resources\HR\Evaluations\Pages;

use App\Filament\Resources\HR\Evaluations\EvaluationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEvaluations extends ListRecords
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $records = $this->getFilteredTableQuery()->with(['student', 'subject'])->orderBy('date', 'desc')->get();
                    $filter = $this->tableFilters['date_range'] ?? [];
                    $rows = [];
                    foreach ($records as $i => $ev) {
                        $rows[] = [
                            $i + 1,
                            $ev->date ? \Carbon\Carbon::parse($ev->date)->format('d/m/Y') : '-',
                            $ev->student?->name ?? '-',
                            $ev->subject?->name ?? '-',
                            $ev->score,
                            $ev->notes ?: '-',
                        ];
                    }

                    return \App\Support\TablePdfExporter::download(
                        filename: 'rekap-evaluasi-nilai-' . now()->format('Y-m-d') . '.pdf',
                        title: 'Rekap Nilai & Evaluasi Siswa',
                        subtitle: \App\Support\TablePdfExporter::periodSubtitle($filter['from'] ?? null, $filter['until'] ?? null),
                        headers: ['No', 'Tanggal', 'Siswa', 'Mata Pelajaran', 'Skor', 'Catatan'],
                        rows: $rows,
                        summary: [
                            'Total Data' => $records->count() . ' evaluasi',
                            'Rata-rata Skor' => $records->count() ? round($records->avg('score'), 1) : 0,
                        ],
                        orientation: 'portrait',
                    );
                }),
            \Filament\Actions\ExportAction::make()->exporter(\App\Filament\Exports\EvaluationExporter::class),
            CreateAction::make(),
        ];
    }
}
