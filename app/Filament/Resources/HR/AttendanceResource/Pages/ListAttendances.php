<?php
namespace App\Filament\Resources\HR\AttendanceResource\Pages;
use App\Filament\Resources\HR\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;
    
    protected function getHeaderActions(): array 
    { 
        return [
            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $records = $this->getFilteredTableQuery()->with(['student', 'tutor', 'user', 'classRoom'])->orderBy('date', 'desc')->get();
                    $filter = $this->tableFilters['date_range'] ?? [];
                    $statusMap = ['present' => 'Hadir', 'absent' => 'Tidak Hadir', 'late' => 'Terlambat', 'excused' => 'Izin'];
                    $roleMap = ['student' => 'Siswa', 'tutor' => 'Tutor', 'employee' => 'Karyawan'];
                    $rows = [];
                    foreach ($records as $i => $a) {
                        $name = $a->role_type === 'student' ? ($a->student?->name ?? '-') : ($a->role_type === 'tutor' ? ($a->tutor?->name ?? '-') : ($a->user?->name ?? '-'));
                        $rows[] = [
                            $i + 1,
                            $a->date ? $a->date->format('d/m/Y') : '-',
                            $roleMap[$a->role_type] ?? $a->role_type,
                            $name,
                            $a->classRoom?->name ?? '-',
                            ($a->time_in ? substr((string) $a->time_in, 0, 5) : '--:--') . ' - ' . ($a->time_out ? substr((string) $a->time_out, 0, 5) : '--:--'),
                            $statusMap[$a->status] ?? $a->status,
                        ];
                    }

                    return \App\Support\TablePdfExporter::download(
                        filename: 'rekap-absensi-' . now()->format('Y-m-d') . '.pdf',
                        title: 'Rekap Kehadiran / Absensi',
                        subtitle: \App\Support\TablePdfExporter::periodSubtitle($filter['from'] ?? null, $filter['until'] ?? null),
                        headers: ['No', 'Tanggal', 'Tipe', 'Nama', 'Kelas', 'Jam', 'Status'],
                        rows: $rows,
                        summary: [
                            'Total Data' => $records->count() . ' baris',
                            'Hadir' => $records->where('status', 'present')->count(),
                            'Terlambat / Izin / Alpa' => $records->where('status', '!=', 'present')->count(),
                        ],
                    );
                }),
            Actions\CreateAction::make()->label(__('Add Attendance'))
        ]; 
    }

    public function getTabs(): array
    {
        return [
            'semua' => \Filament\Schemas\Components\Tabs\Tab::make('Semua'),
            'siswa' => \Filament\Schemas\Components\Tabs\Tab::make('Siswa')
                ->modifyQueryUsing(fn ($query) => $query->where('role_type', 'student')),
            'tutor' => \Filament\Schemas\Components\Tabs\Tab::make('Tutor')
                ->modifyQueryUsing(fn ($query) => $query->where('role_type', 'tutor')),
            'karyawan' => \Filament\Schemas\Components\Tabs\Tab::make('Karyawan')
                ->modifyQueryUsing(fn ($query) => $query->where('role_type', 'employee')),
        ];
    }
}
