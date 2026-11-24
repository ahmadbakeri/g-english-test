<?php

namespace App\Filament\Resources\Evaluation\TestScheduleRequestResource\Pages;

use App\Filament\Resources\Evaluation\TestScheduleRequestResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;

class ManageTestScheduleRequests extends ManageRecords
{
    protected static string $resource = TestScheduleRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $records = $this->getFilteredTableQuery()->with(['session', 'requester', 'student'])->get();
                    $filter = $this->tableFilters['date_range'] ?? [];
                    $rows = [];
                    foreach ($records as $i => $r) {
                        $s = $r->session;
                        $rows[] = [
                            $i + 1,
                            $s?->title ?? '-',
                            $s ? ($s->session_date?->format('d M Y') . ' ' . substr((string) $s->start_time, 0, 5)) : '-',
                            $s?->server_name ?? '-',
                            $r->requester?->name ?? '-',
                            $r->school_name ?: ($r->student?->name ?? '-'),
                            $r->requested_seats . ' kursi',
                            strtoupper($r->status),
                        ];
                    }

                    return \App\Support\TablePdfExporter::download(
                        filename: 'jadwal-tes-server-' . now()->format('Y-m-d') . '.pdf',
                        title: 'Laporan Request & Approval Jadwal Tes',
                        subtitle: \App\Support\TablePdfExporter::periodSubtitle($filter['from'] ?? null, $filter['until'] ?? null),
                        headers: ['No', 'Sesi Tes', 'Waktu', 'Server', 'Pemohon', 'Sekolah / Siswa', 'Kuota', 'Status'],
                        rows: $rows,
                        summary: [
                            'Total Request' => $records->count(),
                            'Disetujui' => $records->where('status', 'approved')->sum('requested_seats') . ' kursi',
                            'Pending' => $records->where('status', 'pending')->sum('requested_seats') . ' kursi',
                        ],
                    );
                }),
            CreateAction::make()
                ->label('Buat Request Jadwal')
                ->slideOver()
                ->modalWidth('2xl')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['requester_user_id'] = Auth::id();
                    $data['requester_role'] = Auth::user()?->role ?? 'admin';
                    return $data;
                }),
        ];
    }
}
