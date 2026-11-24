<?php

namespace App\Support;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TablePdfExporter
{
    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, string|int|float|null>>  $rows
     * @param  array<string, string|int>  $summary
     */
    public static function download(
        string $filename,
        string $title,
        string $subtitle,
        array $headers,
        array $rows,
        array $summary = [],
        string $orientation = 'landscape',
    ): StreamedResponse {
        // Automatically switch to zero-overhead streaming native PDF writer for large datasets (> 120 rows)
        if (count($rows) > 120) {
            return FastTablePdfWriter::download(
                filename: $filename,
                title: $title,
                subtitle: $subtitle,
                headers: $headers,
                rows: $rows,
                summary: $summary,
                orientation: $orientation,
            );
        }

        $pdf = Pdf::loadView('reports.table-export-pdf', [
            'title' => $title,
            'subtitle' => $subtitle,
            'headers' => $headers,
            'rows' => $rows,
            'summary' => $summary,
            'printedAt' => Carbon::now()->format('d M Y, H:i'),
        ])->setPaper('a4', $orientation);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public static function formatIdr(float|int|null $amount): string
    {
        return 'Rp ' . number_format((float) ($amount ?? 0), 0, ',', '.');
    }

    public static function periodSubtitle(?string $from, ?string $until, string $fallback = 'Semua Periode'): string
    {
        if ($from && $until) {
            return 'Periode: ' . Carbon::parse($from)->format('d M Y') . ' s/d ' . Carbon::parse($until)->format('d M Y');
        }
        if ($from) {
            return 'Periode: Mulai ' . Carbon::parse($from)->format('d M Y');
        }
        if ($until) {
            return 'Periode: Sampai ' . Carbon::parse($until)->format('d M Y');
        }
        return 'Periode: ' . $fallback;
    }
}
