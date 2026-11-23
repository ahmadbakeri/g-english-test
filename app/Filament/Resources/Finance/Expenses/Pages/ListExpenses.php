<?php

namespace App\Filament\Resources\Finance\Expenses\Pages;

use App\Filament\Resources\Finance\Expenses\ExpenseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExpenses extends ListRecords
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $records = $this->getFilteredTableQuery()->orderBy('date', 'desc')->get();
                    $filter = $this->tableFilters['date_range'] ?? [];
                    $rows = [];
                    foreach ($records as $i => $e) {
                        $rows[] = [
                            $i + 1,
                            $e->date ? $e->date->format('d M Y') : '-',
                            $e->category,
                            $e->description ?: '-',
                            \App\Support\TablePdfExporter::formatIdr($e->amount),
                        ];
                    }

                    return \App\Support\TablePdfExporter::download(
                        filename: 'laporan-pengeluaran-' . now()->format('Y-m-d') . '.pdf',
                        title: 'Laporan Pengeluaran Operasional',
                        subtitle: \App\Support\TablePdfExporter::periodSubtitle($filter['from'] ?? null, $filter['until'] ?? null),
                        headers: ['No', 'Tanggal', 'Kategori', 'Keterangan', 'Nominal'],
                        rows: $rows,
                        summary: [
                            'Total Transaksi' => $records->count() . ' data',
                            'Total Pengeluaran' => \App\Support\TablePdfExporter::formatIdr($records->sum('amount')),
                        ],
                        orientation: 'portrait',
                    );
                }),
            CreateAction::make(),
        ];
    }
}
