<?php

namespace App\Filament\Resources\HR\StudentResource\Pages;

use App\Filament\Resources\HR\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $records = $this->getFilteredTableQuery()->orderBy('class_name')->orderBy('name')->get();
                    $rows = [];
                    foreach ($records as $i => $s) {
                        $rows[] = [
                            $i + 1,
                            $s->nis ?: '-',
                            $s->name,
                            StudentResource::resolveClassLabel($s),
                            $s->gender ?: '-',
                            $s->phone ?: '-',
                            ($s->parent_name ?: ($s->father_name ?: '-')) . ' (' . ($s->parent_phone ?: ($s->father_phone ?: '-')) . ')',
                            $s->is_active ? 'Aktif' : 'Nonaktif',
                        ];
                    }

                    return \App\Support\TablePdfExporter::download(
                        filename: 'daftar-siswa-internal-' . now()->format('Y-m-d') . '.pdf',
                        title: 'Daftar Siswa Internal G-English',
                        subtitle: 'Total ' . $records->count() . ' siswa sesuai filter aktif',
                        headers: ['No', 'NIS', 'Nama Siswa', 'Kelas', 'L/P', 'No. HP', 'Orang Tua / WA', 'Status'],
                        rows: $rows,
                        summary: [
                            'Total Siswa' => $records->count() . ' siswa',
                            'Aktif' => $records->where('is_active', true)->count() . ' siswa',
                        ],
                    );
                }),
            Actions\ExportAction::make()->exporter(\App\Filament\Exports\StudentExporter::class),
            Actions\CreateAction::make()
                ->label(__('Add Student'))
                ->mutateFormDataUsing(function (array $data): array {
                    $data['student_type'] = $data['student_type'] ?? 'internal';
                    return $data;
                }),
        ];
    }
}
