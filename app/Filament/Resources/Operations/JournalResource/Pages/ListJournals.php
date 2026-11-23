<?php
namespace App\Filament\Resources\Operations\JournalResource\Pages;
use App\Filament\Resources\Operations\JournalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListJournals extends ListRecords
{
    protected static string $resource = JournalResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $records = $this->getFilteredTableQuery()->with(['classRoom', 'tutor'])->orderBy('date', 'desc')->get();
                    $filter = $this->tableFilters['date_range'] ?? [];
                    $statusMap = ['draft' => 'Draft', 'submitted' => 'Diajukan', 'validated' => 'Divalidasi', 'rejected' => 'Ditolak'];
                    $rows = [];
                    foreach ($records as $i => $j) {
                        $rows[] = [
                            $i + 1,
                            $j->date ? $j->date->format('d/m/Y') : '-',
                            $j->classRoom?->name ?? '-',
                            $j->tutor?->name ?? '-',
                            $j->topic,
                            ($j->jp_count ?? 0) . ' JP',
                            $statusMap[$j->status] ?? $j->status,
                        ];
                    }

                    return \App\Support\TablePdfExporter::download(
                        filename: 'rekap-jurnal-mengajar-' . now()->format('Y-m-d') . '.pdf',
                        title: 'Rekap Jurnal Mengajar Tutor',
                        subtitle: \App\Support\TablePdfExporter::periodSubtitle($filter['from'] ?? null, $filter['until'] ?? null),
                        headers: ['No', 'Tanggal', 'Kelas', 'Tutor', 'Topik', 'JP', 'Status'],
                        rows: $rows,
                        summary: [
                            'Total Jurnal' => $records->count() . ' sesi',
                            'Total JP' => $records->sum('jp_count') . ' JP',
                            'Tervalidasi' => $records->where('status', 'validated')->count() . ' sesi',
                        ],
                    );
                }),
            Actions\CreateAction::make()->label(__('Add Journal')),
        ];
    }
}
