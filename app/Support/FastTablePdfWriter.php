<?php

namespace App\Support;

use Carbon\Carbon;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Ultra-fast, constant-memory streaming PDF 1.4 writer for large tabular exports (10,000+ rows).
 * Uses native PDF drawing operators + per-page FlateDecode (zlib) compression flushed directly to disk,
 * completely bypassing DomPDF's O(N^2) HTML/CSS table layout engine.
 */
class FastTablePdfWriter
{
    public const ROWS_PER_PAGE_LANDSCAPE = 32;
    public const ROWS_PER_PAGE_PORTRAIT = 46;

    /**
     * One-shot streaming download for medium/large arrays without DomPDF.
     *
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, string|int|float|null>>  $rows
     * @param  array<string, string|int>  $summary
     * @param  array<int, float>|null  $colWeights
     */
    public static function download(
        string $filename,
        string $title,
        string $subtitle,
        array $headers,
        array $rows,
        array $summary = [],
        string $orientation = 'landscape',
        ?array $colWeights = null,
    ): StreamedResponse {
        $jobDir = storage_path('app/pdf-exports/sync-' . bin2hex(random_bytes(8)));
        $pdfPath = $jobDir . DIRECTORY_SEPARATOR . 'export.pdf';

        self::beginJob($jobDir, [
            'title' => $title,
            'subtitle' => $subtitle,
            'headers' => $headers,
            'colWeights' => $colWeights,
            'summary' => $summary,
            'orientation' => $orientation,
            'totalRows' => count($rows),
            'printedAt' => Carbon::now()->format('d M Y, H:i'),
        ]);

        foreach (array_chunk($rows, 500) as $chunk) {
            self::appendRows($jobDir, $chunk);
        }

        self::finalizeJob($jobDir, $pdfPath);

        return response()->streamDownload(function () use ($pdfPath, $jobDir) {
            $fh = fopen($pdfPath, 'rb');
            if ($fh !== false) {
                while (!feof($fh)) {
                    echo fread($fh, 65536);
                }
                fclose($fh);
            }
            self::cleanupDir($jobDir);
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Initialize an incremental chunked PDF export job directory on disk.
     *
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    public static function beginJob(string $jobDir, array $config): array
    {
        self::cleanupExpiredExports();

        if (!is_dir($jobDir) && !mkdir($jobDir, 0775, true) && !is_dir($jobDir)) {
            throw new RuntimeException("Unable to create PDF export directory: {$jobDir}");
        }

        $orientation = ($config['orientation'] ?? 'landscape') === 'portrait' ? 'portrait' : 'landscape';
        $pageW = $orientation === 'portrait' ? 595.28 : 841.89;
        $pageH = $orientation === 'portrait' ? 841.89 : 595.28;
        $margin = 24.0;
        $usableW = $pageW - ($margin * 2);

        $headers = array_values($config['headers'] ?? []);
        $colCount = max(1, count($headers));
        $weights = $config['colWeights'] ?? array_fill(0, $colCount, 1.0);
        if (count($weights) !== $colCount) {
            $weights = array_fill(0, $colCount, 1.0);
        }
        $weightSum = max(0.0001, (float) array_sum($weights));
        $colWidths = array_map(fn ($w) => round(($w / $weightSum) * $usableW, 2), $weights);

        $summary = $config['summary'] ?? [];
        $rowsPerPage = $orientation === 'portrait' ? self::ROWS_PER_PAGE_PORTRAIT : self::ROWS_PER_PAGE_LANDSCAPE;
        $firstPageRows = !empty($summary) ? max(10, $rowsPerPage - 3) : $rowsPerPage;

        $totalRows = max(0, (int) ($config['totalRows'] ?? 0));
        if ($totalRows === 0) {
            $totalPages = 1;
        } elseif ($totalRows <= $firstPageRows) {
            $totalPages = 1;
        } else {
            $totalPages = 1 + (int) ceil(($totalRows - $firstPageRows) / $rowsPerPage);
        }

        $meta = [
            'title' => (string) ($config['title'] ?? 'Laporan Data'),
            'subtitle' => (string) ($config['subtitle'] ?? ''),
            'printedAt' => (string) ($config['printedAt'] ?? Carbon::now()->format('d M Y, H:i')),
            'orientation' => $orientation,
            'pageW' => $pageW,
            'pageH' => $pageH,
            'margin' => $margin,
            'headers' => $headers,
            'colWidths' => $colWidths,
            'summary' => $summary,
            'rowsPerPage' => $rowsPerPage,
            'firstPageRows' => $firstPageRows,
            'totalRows' => $totalRows,
            'totalPages' => $totalPages,
            'pagesWritten' => 0,
            'rowsWritten' => 0,
            'pageLengths' => [],
            'bufferRows' => [],
        ];

        file_put_contents($jobDir . DIRECTORY_SEPARATOR . 'pages.bin', '');
        self::saveMeta($jobDir, $meta);

        return $meta;
    }

    /**
     * Append a chunk of rows to the job; full pages are immediately rendered, compressed, and flushed to disk.
     *
     * @param  array<int, array<int, string|int|float|null>>  $rows
     * @return array<string, mixed>
     */
    public static function appendRows(string $jobDir, array $rows): array
    {
        $meta = self::loadMeta($jobDir);
        $buffer = $meta['bufferRows'];
        foreach ($rows as $row) {
            $buffer[] = $row;
        }

        $pagesFile = $jobDir . DIRECTORY_SEPARATOR . 'pages.bin';
        $fh = fopen($pagesFile, 'ab');
        if ($fh === false) {
            throw new RuntimeException('Unable to open pages.bin for appending');
        }

        try {
            while (true) {
                $nextPageNo = $meta['pagesWritten'] + 1;
                $capacity = $nextPageNo === 1 ? $meta['firstPageRows'] : $meta['rowsPerPage'];

                if (count($buffer) < $capacity) {
                    break;
                }

                $pageRows = array_slice($buffer, 0, $capacity);
                $buffer = array_slice($buffer, $capacity);

                $stream = self::renderPageStream($meta, $nextPageNo, $pageRows);
                $compressed = gzcompress($stream, 6);
                fwrite($fh, $compressed);

                $meta['pagesWritten'] = $nextPageNo;
                $meta['rowsWritten'] += count($pageRows);
                $meta['pageLengths'][] = strlen($compressed);
            }
        } finally {
            fclose($fh);
        }

        $meta['bufferRows'] = $buffer;
        self::saveMeta($jobDir, $meta);

        return $meta;
    }

    /**
     * Flush remaining buffered rows and assemble the final valid PDF 1.4 file with exact xref offsets.
     *
     * @return array{path: string, pages: int, rows: int, bytes: int}
     */
    public static function finalizeJob(string $jobDir, string $outputPdfPath): array
    {
        $meta = self::loadMeta($jobDir);
        $pagesFile = $jobDir . DIRECTORY_SEPARATOR . 'pages.bin';

        // Flush trailing partial page (or empty-state page if 0 rows)
        if (!empty($meta['bufferRows']) || $meta['pagesWritten'] === 0) {
            $nextPageNo = $meta['pagesWritten'] + 1;
            if ($nextPageNo > $meta['totalPages']) {
                $meta['totalPages'] = $nextPageNo;
            }
            $stream = self::renderPageStream($meta, $nextPageNo, $meta['bufferRows']);
            $compressed = gzcompress($stream, 6);

            $fh = fopen($pagesFile, 'ab');
            if ($fh !== false) {
                fwrite($fh, $compressed);
                fclose($fh);
            }

            $meta['pagesWritten'] = $nextPageNo;
            $meta['rowsWritten'] += count($meta['bufferRows']);
            $meta['pageLengths'][] = strlen($compressed);
            $meta['bufferRows'] = [];
        }

        $pageCount = count($meta['pageLengths']);
        $pageW = number_format((float) $meta['pageW'], 2, '.', '');
        $pageH = number_format((float) $meta['pageH'], 2, '.', '');

        $out = fopen($outputPdfPath, 'wb');
        $in = fopen($pagesFile, 'rb');
        if ($out === false || $in === false) {
            if ($out) {
                fclose($out);
            }
            if ($in) {
                fclose($in);
            }
            throw new RuntimeException('Unable to assemble final PDF file');
        }

        $offsets = [];
        $pageObjectIds = [];

        try {
            // PDF 1.4 Header with binary marker
            fwrite($out, "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n");

            // Object 1: Catalog
            $offsets[1] = ftell($out);
            fwrite($out, "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n");

            // Object 3: Regular Font (Helvetica / WinAnsiEncoding)
            $offsets[3] = ftell($out);
            fwrite($out, "3 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>\nendobj\n");

            // Object 4: Bold Font (Helvetica-Bold / WinAnsiEncoding)
            $offsets[4] = ftell($out);
            fwrite($out, "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>\nendobj\n");

            // Stream each compressed page straight from pages.bin -> final PDF
            $nextObjId = 5;
            foreach ($meta['pageLengths'] as $len) {
                $streamObjId = $nextObjId++;
                $pageObjId = $nextObjId++;
                $pageObjectIds[] = $pageObjId;

                $compressed = $len > 0 ? fread($in, $len) : '';

                $offsets[$streamObjId] = ftell($out);
                fwrite($out, "{$streamObjId} 0 obj\n<< /Length {$len} /Filter /FlateDecode >>\nstream\n");
                fwrite($out, $compressed);
                fwrite($out, "\nendstream\nendobj\n");

                $offsets[$pageObjId] = ftell($out);
                fwrite(
                    $out,
                    "{$pageObjId} 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$pageW} {$pageH}] "
                    . "/Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents {$streamObjId} 0 R >>\nendobj\n"
                );
            }

            // Object 2: Pages Tree Root
            $kids = implode(' ', array_map(fn (int $id) => "{$id} 0 R", $pageObjectIds));
            $offsets[2] = ftell($out);
            fwrite($out, "2 0 obj\n<< /Type /Pages /Kids [{$kids}] /Count {$pageCount} >>\nendobj\n");

            // Cross-reference table (objects 1..$maxObjId sorted by ID)
            $maxObjId = $nextObjId - 1;
            $xrefOffset = ftell($out);
            fwrite($out, "xref\n0 " . ($maxObjId + 1) . "\n");
            fwrite($out, "0000000000 65535 f \n");
            for ($id = 1; $id <= $maxObjId; $id++) {
                fwrite($out, sprintf("%010d 00000 n \n", $offsets[$id] ?? 0));
            }

            // Trailer
            fwrite(
                $out,
                "trailer\n<< /Size " . ($maxObjId + 1) . " /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF\n"
            );
        } finally {
            fclose($in);
            fclose($out);
        }

        @unlink($pagesFile);
        @unlink($jobDir . DIRECTORY_SEPARATOR . 'meta.json');

        return [
            'path' => $outputPdfPath,
            'pages' => $pageCount,
            'rows' => (int) $meta['rowsWritten'],
            'bytes' => (int) (@filesize($outputPdfPath) ?: 0),
        ];
    }

    /**
     * Render raw PDF drawing operators for a single page (Genza Blue & Gold layout).
     *
     * @param  array<string, mixed>  $meta
     * @param  array<int, array<int, string|int|float|null>>  $rows
     */
    protected static function renderPageStream(array $meta, int $pageNo, array $rows): string
    {
        $w = (float) $meta['pageW'];
        $h = (float) $meta['pageH'];
        $m = (float) $meta['margin'];
        $usableW = $w - ($m * 2);
        $totalPages = max($pageNo, (int) $meta['totalPages']);

        $ops = [];

        // 1. Top Genza Blue Header Banner (#0a4282) + Gold Bottom Accent (#fbb900)
        $bannerH = 44.0;
        $bannerY = $h - $m - $bannerH;
        $ops[] = self::rectFill($m, $bannerY, $usableW, $bannerH, 0.039, 0.259, 0.510);
        $ops[] = self::rectFill($m, $bannerY, $usableW, 3.0, 0.984, 0.725, 0.0);

        // Brand tag badge inside banner
        $ops[] = self::rectFill($m + 10, $bannerY + 28, 128, 11, 0.984, 0.725, 0.0);
        $ops[] = self::text($m + 14, $bannerY + 30.8, 'GENZA ENGLISH • LAPORAN DATA', 'F2', 6.8, 0.039, 0.259, 0.510);

        // Main Title & Subtitle
        $ops[] = self::text($m + 10, $bannerY + 15.5, self::clipText($meta['title'], 90), 'F2', 11.5, 1.0, 1.0, 1.0);
        $ops[] = self::text($m + 10, $bannerY + 6.2, self::clipText($meta['subtitle'], 135), 'F1', 7.3, 0.86, 0.92, 1.0);

        // Page indicator top-right
        $pageBadge = "Hal {$pageNo} / {$totalPages}";
        $ops[] = self::text($w - $m - 82, $bannerY + 26, $pageBadge, 'F2', 8.5, 0.984, 0.725, 0.0);
        $ops[] = self::text($w - $m - 122, $bannerY + 14, $meta['printedAt'], 'F1', 7.2, 0.86, 0.92, 1.0);

        $cursorY = $bannerY - 8.0;

        // 2. Summary Cards Strip on Page 1 only
        if ($pageNo === 1 && !empty($meta['summary'])) {
            $summaryItems = array_slice($meta['summary'], 0, 5, true);
            $cnt = count($summaryItems);
            $gap = 6.0;
            $cardW = ($usableW - ($gap * ($cnt - 1))) / $cnt;
            $cardH = 30.0;
            $cardY = $cursorY - $cardH;

            $idx = 0;
            foreach ($summaryItems as $label => $value) {
                $cx = $m + ($idx * ($cardW + $gap));
                $ops[] = self::rectFill($cx, $cardY, $cardW, $cardH, 0.973, 0.980, 0.988);
                $ops[] = self::rectStroke($cx, $cardY, $cardW, $cardH, 0.80, 0.84, 0.88, 0.5);
                $ops[] = self::rectFill($cx, $cardY + $cardH - 2.5, $cardW, 2.5, 0.039, 0.259, 0.510);
                $ops[] = self::text($cx + 7, $cardY + 18.5, strtoupper(self::clipText((string) $label, 28)), 'F2', 6.5, 0.39, 0.45, 0.55);
                $ops[] = self::text($cx + 7, $cardY + 7.0, self::clipText((string) $value, 28), 'F2', 9.2, 0.039, 0.259, 0.510);
                $idx++;
            }

            $cursorY = $cardY - 8.0;
        }

        // 3. Table Header Row
        $headerH = 19.0;
        $headerY = $cursorY - $headerH;
        $ops[] = self::rectFill($m, $headerY, $usableW, $headerH, 0.039, 0.259, 0.510);

        $x = $m;
        $colWidths = $meta['colWidths'];
        foreach ($meta['headers'] as $ci => $hdr) {
            $cw = (float) ($colWidths[$ci] ?? 80.0);
            $maxChars = max(4, (int) floor(($cw - 8) / 4.3));
            $ops[] = self::text($x + 4, $headerY + 6.5, strtoupper(self::clipText((string) $hdr, $maxChars)), 'F2', 7.0, 1.0, 1.0, 1.0);
            $x += $cw;
        }

        $cursorY = $headerY;

        // 4. Data Rows
        $rowH = 14.2;
        if (empty($rows)) {
            $emptyY = $cursorY - 26;
            $ops[] = self::rectStroke($m, $emptyY, $usableW, 26, 0.80, 0.84, 0.88, 0.4);
            $ops[] = self::text($m + 10, $emptyY + 10, 'Tidak ada data untuk periode / filter yang dipilih.', 'F1', 8.0, 0.39, 0.45, 0.55);
        } else {
            foreach ($rows as $rIdx => $row) {
                $rowY = $cursorY - $rowH;

                // Zebra striping
                if ($rIdx % 2 === 1) {
                    $ops[] = self::rectFill($m, $rowY, $usableW, $rowH, 0.973, 0.980, 0.988);
                }

                // Bottom hairline divider
                $ops[] = self::line($m, $rowY, $m + $usableW, $rowY, 0.88, 0.90, 0.93, 0.35);

                $cx = $m;
                foreach ($colWidths as $ci => $cw) {
                    $cw = (float) $cw;
                    $val = trim((string) ($row[$ci] ?? '-'));
                    if ($val === '') {
                        $val = '-';
                    }
                    $maxChars = max(4, (int) floor(($cw - 7) / 4.1));
                    $display = self::clipText($val, $maxChars);

                    // Highlight Brain Champion program cells with Genza Gold pill
                    if (strcasecmp($val, 'Brain Champion') === 0) {
                        $ops[] = self::rectFill($cx + 2.5, $rowY + 2.2, min($cw - 5, 68), $rowH - 4.4, 0.984, 0.725, 0.0);
                        $ops[] = self::text($cx + 5.5, $rowY + 4.8, $display, 'F2', 6.8, 0.039, 0.259, 0.510);
                    } else {
                        $font = ($ci === 0) ? 'F2' : 'F1';
                        $rgb = ($ci === 0) ? [0.39, 0.45, 0.55] : [0.06, 0.09, 0.16];
                        $ops[] = self::text($cx + 4, $rowY + 4.8, $display, $font, 7.2, $rgb[0], $rgb[1], $rgb[2]);
                    }

                    $cx += $cw;
                }

                $cursorY = $rowY;
            }

            // Outer table border
            $tableTop = $headerY + $headerH;
            $tableH = $tableTop - $cursorY;
            $ops[] = self::rectStroke($m, $cursorY, $usableW, $tableH, 0.80, 0.84, 0.88, 0.45);
        }

        // 5. Footer
        $footerY = $m - 8.0;
        $ops[] = self::line($m, $footerY + 11, $m + $usableW, $footerY + 11, 0.80, 0.84, 0.88, 0.45);
        $ops[] = self::text(
            $m,
            $footerY + 2,
            "G-English Course & Brain Champion • Dicetak {$meta['printedAt']} • https://genzaenglish.my.id",
            'F1',
            7.0,
            0.39,
            0.45,
            0.55
        );
        $ops[] = self::text($w - $m - 82, $footerY + 2, "Halaman {$pageNo} dari {$totalPages}", 'F2', 7.0, 0.039, 0.259, 0.510);

        return implode("\n", $ops);
    }

    protected static function rectFill(float $x, float $y, float $w, float $h, float $r, float $g, float $b): string
    {
        return sprintf('%.3f %.3f %.3f rg %.2f %.2f %.2f %.2f re f', $r, $g, $b, $x, $y, $w, $h);
    }

    protected static function rectStroke(float $x, float $y, float $w, float $h, float $r, float $g, float $b, float $lw = 0.5): string
    {
        return sprintf('%.2f w %.3f %.3f %.3f RG %.2f %.2f %.2f %.2f re S', $lw, $r, $g, $b, $x, $y, $w, $h);
    }

    protected static function line(float $x1, float $y1, float $x2, float $y2, float $r, float $g, float $b, float $lw = 0.4): string
    {
        return sprintf('%.2f w %.3f %.3f %.3f RG %.2f %.2f m %.2f %.2f l S', $lw, $r, $g, $b, $x1, $y1, $x2, $y2);
    }

    protected static function text(float $x, float $y, string $text, string $font, float $size, float $r, float $g, float $b): string
    {
        $encoded = self::encodePdfString($text);
        return sprintf('BT /%s %.1f Tf %.3f %.3f %.3f rg 1 0 0 1 %.2f %.2f Tm (%s) Tj ET', $font, $size, $r, $g, $b, $x, $y, $encoded);
    }

    protected static function clipText(string $text, int $maxChars): string
    {
        $clean = preg_replace('/\s+/u', ' ', trim($text)) ?? '';
        if (mb_strlen($clean) <= $maxChars) {
            return $clean;
        }
        return mb_substr($clean, 0, max(1, $maxChars - 2)) . '..';
    }

    protected static function encodePdfString(string $text): string
    {
        // Convert UTF-8 to WinAnsi (Windows-1252) so Helvetica renders bullets/accents accurately
        $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
        if ($converted === false) {
            $converted = preg_replace('/[^\x20-\x7E]/', '', $text) ?? '';
        }
        return str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', ' ', ' '], $converted);
    }

    protected static function saveMeta(string $jobDir, array $meta): void
    {
        file_put_contents($jobDir . DIRECTORY_SEPARATOR . 'meta.json', json_encode($meta, JSON_UNESCAPED_UNICODE));
    }

    protected static function loadMeta(string $jobDir): array
    {
        $raw = @file_get_contents($jobDir . DIRECTORY_SEPARATOR . 'meta.json');
        if ($raw === false) {
            throw new RuntimeException('PDF export metadata not found');
        }
        return json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    }

    public static function cleanupDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        foreach (@scandir($dir) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            @unlink($dir . DIRECTORY_SEPARATOR . $entry);
        }
        @rmdir($dir);
    }

    public static function cleanupExpiredExports(int $maxAgeSeconds = 7200): void
    {
        $root = storage_path('app/pdf-exports');
        if (!is_dir($root)) {
            return;
        }
        $cutoff = time() - $maxAgeSeconds;
        foreach (@scandir($root) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $full = $root . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($full) && (@filemtime($full) ?: time()) < $cutoff) {
                self::cleanupDir($full);
            }
        }
    }
}
