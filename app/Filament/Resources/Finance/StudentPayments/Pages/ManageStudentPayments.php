<?php

namespace App\Filament\Resources\Finance\StudentPayments\Pages;

use App\Filament\Resources\Finance\StudentPayments\StudentPaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageStudentPayments extends ManageRecords
{
    protected static string $resource = StudentPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('generate_bills')
                ->label(__('Generate Monthly Bill'))
                ->icon('heroicon-o-document-plus')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    $month = now()->month;
                    $year = now()->year;
                    
                    $students = \App\Models\Student::where('is_active', true)->get();
                    $count = 0;

                    foreach ($students as $student) {
                        $exists = \App\Models\StudentPayment::where('student_id', $student->id)
                            ->where('month', $month)
                            ->where('year', $year)
                            ->exists();

                        if (!$exists) {
                            \App\Models\StudentPayment::create([
                                'student_id' => $student->id,
                                'month' => $month,
                                'year' => $year,
                                'amount' => 250000,
                                'status' => 'unpaid',
                            ]);
                            $count++;
                        }
                    }

                    \Filament\Notifications\Notification::make()
                        ->title(__('Successfully created :count new bills', ['count' => $count]))
                        ->success()
                        ->send();
                }),
            \Filament\Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $records = $this->getFilteredTableQuery()->with('student')->get();
                    $filter = $this->tableFilters['date_range'] ?? [];
                    $rows = [];
                    foreach ($records as $i => $p) {
                        $rows[] = [
                            $i + 1,
                            $p->student?->name ?? '-',
                            $p->notes ?: 'Pembayaran Kursus',
                            $p->due_date ? $p->due_date->format('d M Y') : '-',
                            \App\Support\TablePdfExporter::formatIdr($p->amount),
                            $p->status === 'paid' ? 'Lunas' : 'Belum Lunas',
                            $p->paid_at ? $p->paid_at->format('d M Y') : '-',
                        ];
                    }
                    $paidTotal = $records->where('status', 'paid')->sum('amount');
                    $unpaidTotal = $records->where('status', '!=', 'paid')->sum('amount');

                    return \App\Support\TablePdfExporter::download(
                        filename: 'laporan-pembayaran-siswa-' . now()->format('Y-m-d') . '.pdf',
                        title: 'Laporan Pembayaran Siswa',
                        subtitle: \App\Support\TablePdfExporter::periodSubtitle($filter['from'] ?? null, $filter['until'] ?? null),
                        headers: ['No', 'Siswa', 'Keterangan', 'Jatuh Tempo', 'Nominal', 'Status', 'Tgl Bayar'],
                        rows: $rows,
                        summary: [
                            'Total Transaksi' => $records->count() . ' data',
                            'Total Lunas' => \App\Support\TablePdfExporter::formatIdr($paidTotal),
                            'Total Belum Lunas' => \App\Support\TablePdfExporter::formatIdr($unpaidTotal),
                        ],
                    );
                }),
            \Filament\Actions\ExportAction::make()->exporter(\App\Filament\Exports\StudentPaymentExporter::class),
            CreateAction::make(),
        ];
    }
}
