<?php

namespace App\Services\CbtDocx;

use App\Models\CbtImportBatch;
use App\Models\CbtQuestionDraft;
use DOMDocument;
use DOMElement;
use DOMNode;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use ZipArchive;

class DocxImportService
{
    public const W_NS = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    public const R_NS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    public const A_NS = 'http://schemas.openxmlformats.org/drawingml/2006/main';
    public const WP_NS = 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing';
    public const WPS_NS = 'http://schemas.microsoft.com/office/word/2010/wordprocessingShape';
    public const WPG_NS = 'http://schemas.microsoft.com/office/word/2010/wordprocessingGroup';
    public const V_NS = 'urn:schemas-microsoft-com:vml';
    public const M_NS = OmmlToLatexConverter::OMML_NS;

    protected OmmlToLatexConverter $ommlConverter;
    protected HtmlSanitizer $sanitizer;

    /** @var array<string, string> rId => public URL (/storage/...) */
    protected array $imageMap = [];

    /** @var array<string, string> rId => unsupported extension */
    protected array $unsupportedImageMap = [];

    /** @var array<string, array<int, array{numFmt: string, lvlText: string}>> numId => ilvl => info */
    protected array $numberingMap = [];

    /** @var array<int, string> */
    protected array $globalWarnings = [];

    public function __construct(?OmmlToLatexConverter $ommlConverter = null, ?HtmlSanitizer $sanitizer = null)
    {
        $this->ommlConverter = $ommlConverter ?? new OmmlToLatexConverter();
        $this->sanitizer = $sanitizer ?? new HtmlSanitizer();
    }

    public function import(
        string $storedLocalPath,
        string $originalFilename,
        ?int $cbtExamId = null,
        ?int $uploadedBy = null,
        ?string $customTitle = null
    ): CbtImportBatch {
        $batch = CbtImportBatch::create([
            'cbt_exam_id' => $cbtExamId,
            'uploaded_by' => $uploadedBy,
            'title' => $customTitle ?: pathinfo($originalFilename, PATHINFO_FILENAME),
            'original_filename' => $originalFilename,
            'stored_path' => $storedLocalPath,
            'status' => 'pending',
        ]);

        $mediaDir = "cbt_media/batch_{$batch->id}";
        $batch->update(['media_dir' => $mediaDir]);

        try {
            $fullPath = Storage::disk('local')->path($storedLocalPath);
            if (!is_file($fullPath)) {
                throw new RuntimeException("File DOCX tidak ditemukan di: {$storedLocalPath}");
            }

            $zip = new ZipArchive();
            if ($zip->open($fullPath) !== true) {
                throw new RuntimeException('File bukan arsip .docx yang valid atau rusak.');
            }

            try {
                $this->resetState();
                $this->extractMediaAndRelationships($zip, $mediaDir);
                $this->parseNumberingXml($zip);

                $documentXml = $zip->getFromName('word/document.xml');
                if ($documentXml === false) {
                    throw new RuntimeException('Struktur word/document.xml tidak ditemukan di file .docx.');
                }

                $blocks = $this->extractBodyBlocks($documentXml);
                [$detectedTitle, $questions] = $this->assembleQuestions($blocks);

                if (!$customTitle && $detectedTitle !== '') {
                    $batch->title = mb_substr($detectedTitle, 0, 220);
                }

                foreach ($questions as $idx => $q) {
                    $draft = $this->createDraft($batch, $idx + 1, $q);
                    $draft->revalidate();
                }

                $batch->update([
                    'title' => $batch->title,
                    'status' => 'parsed',
                    'parse_summary' => [
                        'detected_title' => $detectedTitle,
                        'media_extracted' => count(array_unique(array_values($this->imageMap))),
                        'global_warnings' => array_values(array_unique($this->globalWarnings)),
                    ],
                ]);
                $batch->recalculateCounts();
            } finally {
                $zip->close();
            }
        } catch (\Throwable $e) {
            $batch->update([
                'status' => 'failed',
                'parse_summary' => [
                    'error' => $e->getMessage(),
                ],
            ]);
            throw $e;
        }

        return $batch->fresh(['drafts']);
    }

    protected function resetState(): void
    {
        $this->imageMap = [];
        $this->unsupportedImageMap = [];
        $this->numberingMap = [];
        $this->globalWarnings = [];
    }

    protected function extractMediaAndRelationships(ZipArchive $zip, string $mediaDir): void
    {
        $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
        if ($relsXml === false) {
            return;
        }

        $dom = new DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $dom->loadXML($relsXml);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $allowedRaster = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'];
        $seenHashes = [];

        foreach ($dom->getElementsByTagName('Relationship') as $rel) {
            if (!($rel instanceof DOMElement)) {
                continue;
            }
            $type = $rel->getAttribute('Type');
            if (!str_ends_with($type, '/image')) {
                continue;
            }
            $rId = $rel->getAttribute('Id');
            $target = ltrim($rel->getAttribute('Target'), '/');
            if (!str_starts_with($target, 'word/')) {
                $target = 'word/' . $target;
            }

            $binary = $zip->getFromName($target);
            if ($binary === false) {
                continue;
            }

            $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedRaster, true)) {
                $this->unsupportedImageMap[$rId] = $ext ?: 'unknown';
                $this->globalWarnings[] = "Gambar format .{$ext} tidak didukung browser (perlu unggah ulang PNG/JPG di preview).";
                continue;
            }

            $hash = substr(sha1($binary), 0, 16);
            $filename = "{$hash}.{$ext}";
            $publicPath = "{$mediaDir}/{$filename}";

            if (!isset($seenHashes[$hash])) {
                Storage::disk('public')->put($publicPath, $binary);
                $seenHashes[$hash] = '/storage/' . $publicPath;
            }

            $this->imageMap[$rId] = $seenHashes[$hash];
        }
    }

    protected function parseNumberingXml(ZipArchive $zip): void
    {
        $numXml = $zip->getFromName('word/numbering.xml');
        if ($numXml === false) {
            return;
        }

        $dom = new DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $dom->loadXML($numXml);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $abstracts = [];
        foreach ($dom->getElementsByTagNameNS(self::W_NS, 'abstractNum') as $abs) {
            if (!($abs instanceof DOMElement)) {
                continue;
            }
            $absId = $abs->getAttributeNS(self::W_NS, 'abstractNumId') ?: $abs->getAttribute('w:abstractNumId');
            $levels = [];
            foreach ($abs->getElementsByTagNameNS(self::W_NS, 'lvl') as $lvl) {
                if (!($lvl instanceof DOMElement)) {
                    continue;
                }
                $ilvl = (int) ($lvl->getAttributeNS(self::W_NS, 'ilvl') ?: $lvl->getAttribute('w:ilvl'));
                $numFmt = '';
                $lvlText = '';
                foreach ($lvl->childNodes as $child) {
                    if ($child instanceof DOMElement) {
                        if ($child->localName === 'numFmt') {
                            $numFmt = $child->getAttributeNS(self::W_NS, 'val') ?: $child->getAttribute('w:val');
                        } elseif ($child->localName === 'lvlText') {
                            $lvlText = $child->getAttributeNS(self::W_NS, 'val') ?: $child->getAttribute('w:val');
                        }
                    }
                }
                $levels[$ilvl] = ['numFmt' => $numFmt, 'lvlText' => $lvlText];
            }
            $abstracts[$absId] = $levels;
        }

        foreach ($dom->getElementsByTagNameNS(self::W_NS, 'num') as $num) {
            if (!($num instanceof DOMElement)) {
                continue;
            }
            $numId = $num->getAttributeNS(self::W_NS, 'numId') ?: $num->getAttribute('w:numId');
            $absRef = '';
            foreach ($num->childNodes as $child) {
                if ($child instanceof DOMElement && $child->localName === 'abstractNumId') {
                    $absRef = $child->getAttributeNS(self::W_NS, 'val') ?: $child->getAttribute('w:val');
                }
            }
            if ($numId !== '' && isset($abstracts[$absRef])) {
                $this->numberingMap[$numId] = $abstracts[$absRef];
            }
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function extractBodyBlocks(string $documentXml): array
    {
        $dom = new DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $dom->loadXML($documentXml);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $body = $dom->getElementsByTagNameNS(self::W_NS, 'body')->item(0);
        if (!$body) {
            return [];
        }

        $blocks = [];
        foreach ($body->childNodes as $child) {
            if (!($child instanceof DOMElement)) {
                continue;
            }
            if ($child->localName === 'p') {
                $parsed = $this->parseParagraph($child);
                if ($parsed !== null) {
                    $blocks[] = $parsed;
                }
            } elseif ($child->localName === 'tbl') {
                $parsed = $this->parseTable($child);
                if ($parsed !== null) {
                    $blocks[] = $parsed;
                }
            }
        }

        return $blocks;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function parseParagraph(DOMElement $p): ?array
    {
        $numFmt = null;
        $numId = null;
        $ilvl = 0;

        foreach ($p->childNodes as $child) {
            if ($child instanceof DOMElement && $child->localName === 'pPr') {
                foreach ($child->childNodes as $pChild) {
                    if ($pChild instanceof DOMElement && $pChild->localName === 'numPr') {
                        foreach ($pChild->childNodes as $nChild) {
                            if ($nChild instanceof DOMElement) {
                                if ($nChild->localName === 'ilvl') {
                                    $ilvl = (int) ($nChild->getAttributeNS(self::W_NS, 'val') ?: $nChild->getAttribute('w:val'));
                                } elseif ($nChild->localName === 'numId') {
                                    $numId = $nChild->getAttributeNS(self::W_NS, 'val') ?: $nChild->getAttribute('w:val');
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($numId !== null && $numId !== '0' && isset($this->numberingMap[$numId][$ilvl])) {
            $numFmt = $this->numberingMap[$numId][$ilvl]['numFmt'];
        }

        $htmlParts = [];
        $plainParts = [];
        $media = [];
        $warnings = [];
        $hasShapeWarning = false;
        $hasAnchoredImage = false;

        foreach ($p->childNodes as $child) {
            if (!($child instanceof DOMElement)) {
                continue;
            }

            if ($child->localName === 'r') {
                $res = $this->parseRun($child);
                $htmlParts[] = $res['html'];
                $plainParts[] = $res['plain'];
                array_push($media, ...$res['media']);
                array_push($warnings, ...$res['warnings']);
                if ($res['has_shape']) {
                    $hasShapeWarning = true;
                }
                if ($res['has_anchor_image']) {
                    $hasAnchoredImage = true;
                }
            } elseif ($child->localName === 'oMath') {
                $conv = $this->ommlConverter->convert($child, false);
                if ($conv['wrapped'] !== '') {
                    $htmlParts[] = e($conv['wrapped']);
                    $plainParts[] = $conv['wrapped'];
                }
                array_push($warnings, ...$conv['warnings']);
            } elseif ($child->localName === 'oMathPara') {
                $conv = $this->ommlConverter->convert($child, true);
                if ($conv['wrapped'] !== '') {
                    $htmlParts[] = '<div class="cbt-math-display">' . e($conv['wrapped']) . '</div>';
                    $plainParts[] = $conv['wrapped'];
                }
                array_push($warnings, ...$conv['warnings']);
            } elseif ($child->localName === 'hyperlink') {
                foreach ($child->childNodes as $hChild) {
                    if ($hChild instanceof DOMElement && $hChild->localName === 'r') {
                        $res = $this->parseRun($hChild);
                        $htmlParts[] = $res['html'];
                        $plainParts[] = $res['plain'];
                        array_push($media, ...$res['media']);
                        array_push($warnings, ...$res['warnings']);
                    }
                }
            } elseif ($child->localName === 'ins') {
                // Track-changes insertion: unwrap runs inside
                foreach ($child->childNodes as $iChild) {
                    if ($iChild instanceof DOMElement && $iChild->localName === 'r') {
                        $res = $this->parseRun($iChild);
                        $htmlParts[] = $res['html'];
                        $plainParts[] = $res['plain'];
                        array_push($media, ...$res['media']);
                        array_push($warnings, ...$res['warnings']);
                    }
                }
            }
        }

        $html = trim(implode('', $htmlParts));
        $plain = trim(preg_replace('/\s+/u', ' ', implode('', $plainParts)) ?? '');

        if ($html === '' && $plain === '' && !$hasShapeWarning && empty($warnings)) {
            return null;
        }

        return [
            'type' => 'paragraph',
            'html' => $html,
            'plain' => $plain,
            'num_fmt' => $numFmt,
            'num_id' => $numId,
            'ilvl' => $ilvl,
            'media' => array_values(array_unique($media)),
            'warnings' => array_values(array_unique($warnings)),
            'has_shape' => $hasShapeWarning,
            'has_anchor_image' => $hasAnchoredImage,
        ];
    }

    /**
     * @return array{html: string, plain: string, media: array<int, string>, warnings: array<int, string>, has_shape: bool, has_anchor_image: bool}
     */
    protected function parseRun(DOMElement $r): array
    {
        $bold = false;
        $italic = false;
        $underline = false;
        $vertAlign = null;

        foreach ($r->childNodes as $child) {
            if ($child instanceof DOMElement && $child->localName === 'rPr') {
                foreach ($child->childNodes as $prop) {
                    if (!($prop instanceof DOMElement)) {
                        continue;
                    }
                    $val = $prop->getAttributeNS(self::W_NS, 'val') ?: $prop->getAttribute('w:val');
                    if ($prop->localName === 'b' && $val !== '0' && $val !== 'false') {
                        $bold = true;
                    } elseif ($prop->localName === 'i' && $val !== '0' && $val !== 'false') {
                        $italic = true;
                    } elseif ($prop->localName === 'u' && $val !== 'none' && $val !== '0') {
                        $underline = true;
                    } elseif ($prop->localName === 'vertAlign') {
                        $vertAlign = $val;
                    }
                }
            }
        }

        $html = '';
        $plain = '';
        $media = [];
        $warnings = [];
        $hasShape = false;
        $hasAnchorImg = false;

        foreach ($r->childNodes as $child) {
            if (!($child instanceof DOMElement)) {
                continue;
            }

            if ($child->localName === 't') {
                $txt = $child->textContent ?? '';
                $plain .= $txt;
                $formatted = e($txt);
                if ($vertAlign === 'superscript') {
                    $formatted = "<sup>{$formatted}</sup>";
                } elseif ($vertAlign === 'subscript') {
                    $formatted = "<sub>{$formatted}</sub>";
                }
                if ($bold) {
                    $formatted = "<strong>{$formatted}</strong>";
                }
                if ($italic) {
                    $formatted = "<em>{$formatted}</em>";
                }
                if ($underline) {
                    $formatted = "<u>{$formatted}</u>";
                }
                $html .= $formatted;
            } elseif ($child->localName === 'tab') {
                $html .= ' ';
                $plain .= ' ';
            } elseif ($child->localName === 'br') {
                $html .= '<br>';
                $plain .= "\n";
            } elseif ($child->localName === 'drawing') {
                $drawRes = $this->parseDrawing($child);
                $html .= $drawRes['html'];
                array_push($media, ...$drawRes['media']);
                array_push($warnings, ...$drawRes['warnings']);
                if ($drawRes['has_shape']) {
                    $hasShape = true;
                }
                if ($drawRes['has_anchor_image']) {
                    $hasAnchorImg = true;
                }
            } elseif ($child->localName === 'pict') {
                $pictRes = $this->parsePict($child);
                $html .= $pictRes['html'];
                array_push($media, ...$pictRes['media']);
                array_push($warnings, ...$pictRes['warnings']);
                if ($pictRes['has_shape']) {
                    $hasShape = true;
                }
            } elseif ($child->localName === 'AlternateContent') {
                // Inspect mc:Choice first (modern DrawingML); avoid duplicating mc:Fallback
                $handled = false;
                foreach ($child->childNodes as $acChild) {
                    if ($acChild instanceof DOMElement && $acChild->localName === 'Choice') {
                        foreach ($acChild->getElementsByTagNameNS(self::W_NS, 'drawing') as $d) {
                            if ($d instanceof DOMElement) {
                                $drawRes = $this->parseDrawing($d);
                                $html .= $drawRes['html'];
                                array_push($media, ...$drawRes['media']);
                                array_push($warnings, ...$drawRes['warnings']);
                                if ($drawRes['has_shape']) {
                                    $hasShape = true;
                                }
                                if ($drawRes['has_anchor_image']) {
                                    $hasAnchorImg = true;
                                }
                                $handled = true;
                            }
                        }
                    }
                }
                if (!$handled) {
                    $hasShape = true;
                }
            }
        }

        return [
            'html' => $html,
            'plain' => $plain,
            'media' => $media,
            'warnings' => $warnings,
            'has_shape' => $hasShape,
            'has_anchor_image' => $hasAnchorImg,
        ];
    }

    /**
     * @return array{html: string, media: array<int, string>, warnings: array<int, string>, has_shape: bool, has_anchor_image: bool}
     */
    protected function parseDrawing(DOMElement $drawing): array
    {
        $html = '';
        $media = [];
        $warnings = [];
        $hasShape = false;
        $isAnchor = $drawing->getElementsByTagNameNS(self::WP_NS, 'anchor')->length > 0;
        $hasAnchorImg = false;

        $blips = $drawing->getElementsByTagNameNS(self::A_NS, 'blip');
        if ($blips->length > 0) {
            foreach ($blips as $blip) {
                if (!($blip instanceof DOMElement)) {
                    continue;
                }
                $rId = $blip->getAttributeNS(self::R_NS, 'embed') ?: $blip->getAttribute('r:embed');
                if ($rId && isset($this->imageMap[$rId])) {
                    $url = $this->imageMap[$rId];
                    $media[] = $url;
                    $html .= '<img src="' . e($url) . '" alt="Gambar Soal" class="cbt-inline-img">';
                    if ($isAnchor) {
                        $hasAnchorImg = true;
                    }
                } elseif ($rId && isset($this->unsupportedImageMap[$rId])) {
                    $ext = $this->unsupportedImageMap[$rId];
                    $warnings[] = "[Parser] Gambar format .{$ext} tidak didukung browser — unggah ulang sebagai PNG/JPG.";
                }
            }
        } else {
            // No raster blip -> likely a Word Shape / Flowchart / TextBox / Canvas
            $hasWps = $drawing->getElementsByTagNameNS(self::WPS_NS, 'wsp')->length > 0
                || $drawing->getElementsByTagNameNS(self::WPG_NS, 'wgp')->length > 0
                || str_contains($drawing->ownerDocument?->saveXML($drawing) ?? '', 'wgp:')
                || str_contains($drawing->ownerDocument?->saveXML($drawing) ?? '', 'wps:');
            if ($hasWps) {
                $hasShape = true;
            }
        }

        return [
            'html' => $html,
            'media' => $media,
            'warnings' => $warnings,
            'has_shape' => $hasShape,
            'has_anchor_image' => $hasAnchorImg,
        ];
    }

    /**
     * @return array{html: string, media: array<int, string>, warnings: array<int, string>, has_shape: bool}
     */
    protected function parsePict(DOMElement $pict): array
    {
        $html = '';
        $media = [];
        $warnings = [];
        $hasShape = false;

        $imgDatas = $pict->getElementsByTagNameNS(self::V_NS, 'imagedata');
        if ($imgDatas->length > 0) {
            foreach ($imgDatas as $imgData) {
                if (!($imgData instanceof DOMElement)) {
                    continue;
                }
                $rId = $imgData->getAttributeNS(self::R_NS, 'id') ?: $imgData->getAttribute('r:id');
                if ($rId && isset($this->imageMap[$rId])) {
                    $url = $this->imageMap[$rId];
                    $media[] = $url;
                    $html .= '<img src="' . e($url) . '" alt="Gambar Soal" class="cbt-inline-img">';
                } elseif ($rId && isset($this->unsupportedImageMap[$rId])) {
                    $ext = $this->unsupportedImageMap[$rId];
                    $warnings[] = "[Parser] Gambar VML/OLE format .{$ext} tidak didukung browser — unggah ulang PNG/JPG.";
                }
            }
        } else {
            $hasShape = true;
        }

        return [
            'html' => $html,
            'media' => $media,
            'warnings' => $warnings,
            'has_shape' => $hasShape,
        ];
    }

    /**
     * Parse Word <w:tbl> with full colspan (gridSpan) and rowspan (vMerge) resolution.
     * Also detects Complex True/False statement tables (Pernyataan | Benar | Salah).
     *
     * @return array<string, mixed>|null
     */
    protected function parseTable(DOMElement $tbl): ?array
    {
        $rawRows = [];
        $allMedia = [];
        $allWarnings = [];
        $hasShape = false;

        foreach ($tbl->childNodes as $tr) {
            if (!($tr instanceof DOMElement) || $tr->localName !== 'tr') {
                continue;
            }
            $rowCells = [];
            $colIndex = 0;

            foreach ($tr->childNodes as $tc) {
                if (!($tc instanceof DOMElement) || $tc->localName !== 'tc') {
                    continue;
                }

                $colspan = 1;
                $vMerge = null; // null | 'restart' | 'continue'

                foreach ($tc->childNodes as $tcChild) {
                    if ($tcChild instanceof DOMElement && $tcChild->localName === 'tcPr') {
                        foreach ($tcChild->childNodes as $prop) {
                            if (!($prop instanceof DOMElement)) {
                                continue;
                            }
                            if ($prop->localName === 'gridSpan') {
                                $span = (int) ($prop->getAttributeNS(self::W_NS, 'val') ?: $prop->getAttribute('w:val'));
                                if ($span > 1) {
                                    $colspan = $span;
                                }
                            } elseif ($prop->localName === 'vMerge') {
                                $val = $prop->getAttributeNS(self::W_NS, 'val') ?: $prop->getAttribute('w:val');
                                $vMerge = $val === 'restart' ? 'restart' : 'continue';
                            }
                        }
                    }
                }

                $cellHtmlParts = [];
                $cellPlainParts = [];
                foreach ($tc->childNodes as $tcChild) {
                    if ($tcChild instanceof DOMElement && $tcChild->localName === 'p') {
                        $p = $this->parseParagraph($tcChild);
                        if ($p !== null) {
                            $cellHtmlParts[] = $p['html'];
                            $cellPlainParts[] = $p['plain'];
                            array_push($allMedia, ...$p['media']);
                            array_push($allWarnings, ...$p['warnings']);
                            if ($p['has_shape']) {
                                $hasShape = true;
                            }
                        }
                    }
                }

                $rowCells[] = [
                    'col' => $colIndex,
                    'colspan' => $colspan,
                    'rowspan' => 1,
                    'vmerge' => $vMerge,
                    'skip' => false,
                    'html' => implode('<br>', $cellHtmlParts),
                    'plain' => trim(implode(' ', $cellPlainParts)),
                ];
                $colIndex += $colspan;
            }

            if (!empty($rowCells)) {
                $rawRows[] = $rowCells;
            }
        }

        if (empty($rawRows)) {
            return null;
        }

        // Resolve vertical merges (vMerge -> rowspan)
        $activeRestart = []; // col => [rowIdx, cellIdx]
        foreach ($rawRows as $rIdx => &$row) {
            foreach ($row as $cIdx => &$cell) {
                $col = $cell['col'];
                if ($cell['vmerge'] === 'restart') {
                    $activeRestart[$col] = [$rIdx, $cIdx];
                } elseif ($cell['vmerge'] === 'continue' && isset($activeRestart[$col])) {
                    [$originR, $originC] = $activeRestart[$col];
                    $rawRows[$originR][$originC]['rowspan']++;
                    $cell['skip'] = true;
                } else {
                    unset($activeRestart[$col]);
                }
            }
        }
        unset($row, $cell);

        // Detect Complex True/False Table: Header row contains "Pernyataan" + "Benar" + "Salah"
        $headerPlain = array_map(fn ($c) => mb_strtolower(trim($c['plain'])), $rawRows[0]);
        $hasPernyataan = false;
        $hasBenar = false;
        $hasSalah = false;
        $stmtColIdx = null;
        foreach ($headerPlain as $idx => $hText) {
            if (str_contains($hText, 'pernyataan')) {
                $hasPernyataan = true;
                $stmtColIdx = $idx;
            }
            if ($hText === 'benar' || str_contains($hText, 'benar')) {
                $hasBenar = true;
            }
            if ($hText === 'salah' || str_contains($hText, 'salah')) {
                $hasSalah = true;
            }
        }

        if ($hasPernyataan && $hasBenar && $hasSalah && count($rawRows) > 1 && $stmtColIdx !== null) {
            $statements = [];
            $seq = 1;
            for ($i = 1; $i < count($rawRows); $i++) {
                $cell = $rawRows[$i][$stmtColIdx] ?? null;
                if (!$cell) {
                    continue;
                }
                $textHtml = trim($cell['html']);
                $textPlain = trim($cell['plain']);
                if ($textPlain === '' && $textHtml === '') {
                    continue;
                }
                $statements[] = [
                    'no' => $seq++,
                    'text' => $this->sanitizer->clean($textHtml ?: e($textPlain)),
                    'answer' => null,
                ];
            }

            if (!empty($statements)) {
                return [
                    'type' => 'complex_tf_table',
                    'statements' => $statements,
                    'media' => array_values(array_unique($allMedia)),
                    'warnings' => array_values(array_unique($allWarnings)),
                    'has_shape' => $hasShape,
                ];
            }
        }

        // Standard HTML table
        $html = '<table class="cbt-data-table">';
        foreach ($rawRows as $rIdx => $row) {
            $tag = $rIdx === 0 ? 'th' : 'td';
            $html .= '<tr>';
            foreach ($row as $cell) {
                if ($cell['skip']) {
                    continue;
                }
                $attrs = '';
                if ($cell['colspan'] > 1) {
                    $attrs .= ' colspan="' . $cell['colspan'] . '"';
                }
                if ($cell['rowspan'] > 1) {
                    $attrs .= ' rowspan="' . $cell['rowspan'] . '"';
                }
                $html .= "<{$tag}{$attrs}>" . ($cell['html'] !== '' ? $cell['html'] : '&nbsp;') . "</{$tag}>";
            }
            $html .= '</tr>';
        }
        $html .= '</table>';

        return [
            'type' => 'table',
            'html' => $html,
            'media' => array_values(array_unique($allMedia)),
            'warnings' => array_values(array_unique($allWarnings)),
            'has_shape' => $hasShape,
        ];
    }

    /**
     * Assemble extracted blocks into structured question arrays.
     * Supports BOTH explicit tags ([SOAL], [A]..[E], [KUNCI], [POIN], [PEMBAHASAN], [TIPE])
     * AND Genza Education auto-numbering Word documents (decimal = question, upperLetter = options, bullet = <ul>).
     *
     * @param  array<int, array<string, mixed>>  $blocks
     * @return array{0: string, 1: array<int, array<string, mixed>>}
     */
    protected function assembleQuestions(array $blocks): array
    {
        $usesExplicitTags = false;
        foreach ($blocks as $b) {
            if (($b['type'] ?? '') === 'paragraph' && preg_match('/^\[SOAL\]/iu', $b['plain'])) {
                $usesExplicitTags = true;
                break;
            }
        }

        return $usesExplicitTags
            ? $this->assembleByExplicitTags($blocks)
            : $this->assembleByAutoNumbering($blocks);
    }

    /**
     * @param  array<int, array<string, mixed>>  $blocks
     * @return array{0: string, 1: array<int, array<string, mixed>>}
     */
    protected function assembleByExplicitTags(array $blocks): array
    {
        $headerLines = [];
        $questions = [];
        $current = null;
        $section = 'question'; // question | option_a..e | explanation

        $flush = function () use (&$current, &$questions) {
            if ($current !== null) {
                $questions[] = $this->finalizeQuestion($current);
                $current = null;
            }
        };

        foreach ($blocks as $block) {
            if ($block['type'] === 'complex_tf_table') {
                if ($current === null) {
                    $current = $this->emptyQuestion();
                }
                $current['question_type'] = 'complex_tf';
                $current['statements'] = $block['statements'];
                $this->mergeBlockMeta($current, $block);
                continue;
            }

            if ($block['type'] === 'table') {
                if ($current === null) {
                    continue;
                }
                $current['body_parts'][] = ['kind' => 'raw', 'html' => $block['html']];
                $this->mergeBlockMeta($current, $block);
                continue;
            }

            $plain = $block['plain'];
            $html = $block['html'];

            if (preg_match('/^\[SOAL\]\s*(.*)$/iu', $plain)) {
                $flush();
                $current = $this->emptyQuestion();
                $section = 'question';
                $stripped = $this->stripLeadingTagFromHtml($html, '\[SOAL\]');
                if (trim(strip_tags($stripped)) !== '' || str_contains($stripped, '<img')) {
                    $current['body_parts'][] = ['kind' => 'p', 'html' => $stripped];
                }
                $this->mergeBlockMeta($current, $block);
                continue;
            }

            if ($current === null) {
                if ($plain !== '') {
                    $headerLines[] = $plain;
                }
                continue;
            }

            if (preg_match('/^\[([A-E])\]\s*(.*)$/iu', $plain, $m)) {
                $letter = strtolower($m[1]);
                $section = 'option_' . $letter;
                $stripped = $this->stripLeadingTagFromHtml($html, '\[' . $m[1] . '\]');
                $current['options'][$letter] = trim($stripped);
                $this->mergeBlockMeta($current, $block);
                continue;
            }

            if (preg_match('/^\[KUNCI\]\s*(.*)$/iu', $plain, $m)) {
                $val = trim($m[1]);
                if ($val !== '') {
                    $current['correct_answer'] = strtoupper($val);
                }
                continue;
            }

            if (preg_match('/^\[POIN\]\s*(\d+)/iu', $plain, $m)) {
                $current['points'] = max(1, (int) $m[1]);
                continue;
            }

            if (preg_match('/^\[TIPE\]\s*(.+)$/iu', $plain, $m)) {
                $t = strtolower(trim($m[1]));
                if (in_array($t, ['mcq', 'complex_tf', 'short_answer'], true)) {
                    $current['question_type'] = $t;
                }
                continue;
            }

            if (preg_match('/^\[PEMBAHASAN\]\s*(.*)$/iu', $plain)) {
                $section = 'explanation';
                $stripped = $this->stripLeadingTagFromHtml($html, '\[PEMBAHASAN\]');
                if (trim(strip_tags($stripped)) !== '' || str_contains($stripped, '<img')) {
                    $current['explanation_parts'][] = $stripped;
                }
                $this->mergeBlockMeta($current, $block);
                continue;
            }

            // Append continuation paragraph to current active section
            $this->mergeBlockMeta($current, $block);
            if ($section === 'question') {
                $kind = ($block['num_fmt'] === 'bullet') ? 'li' : 'p';
                $current['body_parts'][] = ['kind' => $kind, 'html' => $html];
            } elseif (str_starts_with($section, 'option_')) {
                $letter = substr($section, -1);
                $current['options'][$letter] = trim(($current['options'][$letter] ?? '') . '<br>' . $html);
            } elseif ($section === 'explanation') {
                $current['explanation_parts'][] = $html;
            }
        }

        $flush();

        return [implode(' — ', array_slice($headerLines, 0, 2)), $questions];
    }

    /**
     * Auto-detect Genza Education Word layout:
     * - Top title lines before Question #1 -> batch title
     * - Word decimal numbering (ilvl=0) OR manual "1. ..." -> new question
     * - Word upperLetter numbering OR manual "A. ...".. "E. ..." -> option A..E
     * - Word bullet numbering (e.g. ✓) -> <ul><li> inside question body
     * - Complex True/False table -> question_type = complex_tf
     *
     * @param  array<int, array<string, mixed>>  $blocks
     * @return array{0: string, 1: array<int, array<string, mixed>>}
     */
    protected function assembleByAutoNumbering(array $blocks): array
    {
        $headerLines = [];
        $questions = [];
        $current = null;
        $optionOrder = ['a', 'b', 'c', 'd', 'e'];
        $nextAutoOptIdx = 0;
        $inOptions = false;

        $flush = function () use (&$current, &$questions, &$nextAutoOptIdx, &$inOptions) {
            if ($current !== null) {
                $questions[] = $this->finalizeQuestion($current);
                $current = null;
            }
            $nextAutoOptIdx = 0;
            $inOptions = false;
        };

        foreach ($blocks as $block) {
            if ($block['type'] === 'complex_tf_table') {
                if ($current === null) {
                    $current = $this->emptyQuestion();
                }
                $current['question_type'] = 'complex_tf';
                $current['statements'] = $block['statements'];
                $this->mergeBlockMeta($current, $block);
                continue;
            }

            if ($block['type'] === 'table') {
                if ($current === null) {
                    continue;
                }
                $current['body_parts'][] = ['kind' => 'raw', 'html' => $block['html']];
                $this->mergeBlockMeta($current, $block);
                continue;
            }

            $plain = $block['plain'];
            $html = $block['html'];
            $numFmt = $block['num_fmt'];
            $ilvl = $block['ilvl'];

            // Detect inline explicit [KUNCI] / [PEMBAHASAN] even inside auto-numbered files
            if ($current !== null && preg_match('/^(?:\[KUNCI\]|Kunci(?:\s+Jawaban)?\s*:)\s*([A-E])/iu', $plain, $km)) {
                $current['correct_answer'] = strtoupper($km[1]);
                continue;
            }

            // 1. Word Auto-Numbered Option (upperLetter, or lowerLetter at nested level)
            if ($current !== null && ($numFmt === 'upperLetter' || ($numFmt === 'lowerLetter' && $ilvl > 0))) {
                $letter = $optionOrder[$nextAutoOptIdx] ?? null;
                if ($letter !== null) {
                    $current['options'][$letter] = $html;
                    $nextAutoOptIdx++;
                    $inOptions = true;
                    $this->mergeBlockMeta($current, $block);
                    continue;
                }
            }

            // 2. Manual Typed Option ("A.  3", "B) 4", etc. — e.g. Question #8!)
            if ($current !== null && preg_match('/^([A-E])[\.\)]\s+(.+)$/u', $plain, $om)) {
                $letter = strtolower($om[1]);
                $stripped = $this->stripLeadingRegexFromHtml($html, '/^\s*[A-E][\.\)]\s+/u');
                $current['options'][$letter] = trim($stripped);
                $idx = array_search($letter, $optionOrder, true);
                if ($idx !== false) {
                    $nextAutoOptIdx = $idx + 1;
                }
                $inOptions = true;
                $this->mergeBlockMeta($current, $block);
                continue;
            }

            // 3. Word Auto-Numbered Question (decimal at level 0)
            $isWordDecimalQuestion = ($numFmt === 'decimal' && $ilvl === 0);

            // 4. Manual Typed Question ("1. Sebuah dealer...", "13. Sebuah toko...")
            $isManualNumberQuestion = false;
            if (!$isWordDecimalQuestion && $numFmt === null && preg_match('/^(\d{1,3})\.\s+\S/u', $plain, $nm)) {
                $expectedNo = count($questions) + ($current ? 2 : 1);
                $numVal = (int) $nm[1];
                if ($current === null || $inOptions || $current['question_type'] === 'complex_tf' || $numVal === $expectedNo) {
                    $isManualNumberQuestion = true;
                }
            }

            if ($isWordDecimalQuestion || $isManualNumberQuestion) {
                $flush();
                $current = $this->emptyQuestion();
                $cleanedHtml = $isManualNumberQuestion
                    ? $this->stripLeadingRegexFromHtml($html, '/^\s*\d{1,3}\.\s+/u')
                    : $html;
                $current['body_parts'][] = ['kind' => 'p', 'html' => $cleanedHtml];
                $this->mergeBlockMeta($current, $block);
                continue;
            }

            // 5. Before first question -> capture document title / header lines
            if ($current === null) {
                if ($plain !== '' && mb_strlen($plain) <= 180) {
                    $headerLines[] = $plain;
                }
                continue;
            }

            // 6. Body continuation (bullet list item or regular paragraph / floating image / diagram shape)
            $this->mergeBlockMeta($current, $block);
            if ($numFmt === 'bullet') {
                $current['body_parts'][] = ['kind' => 'li', 'html' => $html];
            } elseif ($html !== '') {
                $current['body_parts'][] = ['kind' => 'p', 'html' => $html];
            }
        }

        $flush();

        return [implode(' — ', array_slice($headerLines, 0, 2)), $questions];
    }

    /**
     * @return array<string, mixed>
     */
    protected function emptyQuestion(): array
    {
        return [
            'question_type' => 'mcq',
            'body_parts' => [],
            'options' => ['a' => null, 'b' => null, 'c' => null, 'd' => null, 'e' => null],
            'statements' => null,
            'correct_answer' => null,
            'points' => 1,
            'explanation_parts' => [],
            'media' => [],
            'warnings' => [],
            'has_shape' => false,
        ];
    }

    protected function mergeBlockMeta(array &$q, array $block): void
    {
        if (!empty($block['media'])) {
            array_push($q['media'], ...$block['media']);
        }
        if (!empty($block['warnings'])) {
            array_push($q['warnings'], ...$block['warnings']);
        }
        if (!empty($block['has_shape'])) {
            $q['has_shape'] = true;
        }
        if (!empty($block['has_anchor_image'])) {
            $q['warnings'][] = '[Parser] Gambar menggunakan mode Floating/Wrap di Word — periksa posisi gambar di preview.';
        }
    }

    protected function finalizeQuestion(array $q): array
    {
        $htmlChunks = [];
        $listBuffer = [];

        $flushList = function () use (&$listBuffer, &$htmlChunks) {
            if (!empty($listBuffer)) {
                $items = array_map(fn ($li) => "<li>{$li}</li>", $listBuffer);
                $htmlChunks[] = '<ul>' . implode('', $items) . '</ul>';
                $listBuffer = [];
            }
        };

        foreach ($q['body_parts'] as $part) {
            if ($part['kind'] === 'li') {
                $listBuffer[] = $part['html'];
            } elseif ($part['kind'] === 'raw') {
                $flushList();
                $htmlChunks[] = $part['html'];
            } else {
                $flushList();
                $htmlChunks[] = '<p>' . $part['html'] . '</p>';
            }
        }
        $flushList();

        $questionHtml = $this->sanitizer->clean(implode("\n", $htmlChunks));
        $explanationHtml = !empty($q['explanation_parts'])
            ? $this->sanitizer->clean('<p>' . implode('</p><p>', $q['explanation_parts']) . '</p>')
            : null;

        // If question text explicitly mentions "diagram alir" / "diagram" and no image was extracted, flag helpful shape warning
        $plainBody = mb_strtolower(strip_tags($questionHtml));
        if (
            !$q['has_shape']
            && !str_contains($questionHtml, '<img')
            && (str_contains($plainBody, 'diagram alir') || str_contains($plainBody, 'lihat gambar') || str_contains($plainBody, 'pada gambar'))
        ) {
            $q['has_shape'] = true;
        }

        foreach (['a', 'b', 'c', 'd', 'e'] as $k) {
            $val = $q['options'][$k];
            $q['options'][$k] = ($val !== null && trim($val) !== '')
                ? $this->sanitizer->clean($val)
                : null;
        }

        return [
            'question_type' => $q['question_type'],
            'question_html' => $questionHtml,
            'option_a' => $q['options']['a'],
            'option_b' => $q['options']['b'],
            'option_c' => $q['options']['c'],
            'option_d' => $q['options']['d'],
            'option_e' => $q['options']['e'],
            'statements' => $q['statements'],
            'correct_answer' => $q['correct_answer'],
            'points' => $q['points'],
            'explanation_html' => $explanationHtml,
            'extracted_media' => array_values(array_unique($q['media'])),
            'validation_issues' => array_values(array_unique($q['warnings'])),
            'has_shape_warning' => $q['has_shape'],
        ];
    }

    protected function createDraft(CbtImportBatch $batch, int $seq, array $q): CbtQuestionDraft
    {
        return CbtQuestionDraft::create([
            'batch_id' => $batch->id,
            'sequence_no' => $seq,
            'question_type' => $q['question_type'],
            'question_html' => $q['question_html'],
            'option_a' => $q['option_a'],
            'option_b' => $q['option_b'],
            'option_c' => $q['option_c'],
            'option_d' => $q['option_d'],
            'option_e' => $q['option_e'],
            'statements' => $q['statements'],
            'correct_answer' => $q['correct_answer'],
            'points' => $q['points'],
            'explanation_html' => $q['explanation_html'],
            'extracted_media' => $q['extracted_media'],
            'validation_status' => 'valid',
            'validation_issues' => $q['validation_issues'],
            'has_shape_warning' => $q['has_shape_warning'],
        ]);
    }

    protected function stripLeadingTagFromHtml(string $html, string $escapedTagPattern): string
    {
        return preg_replace('/^(?:<(?:strong|b|em|i|u|span)[^>]*>)*\s*' . $escapedTagPattern . '\s*(?:<\/(?:strong|b|em|i|u|span)>)*\s*/iu', '', $html, 1) ?? $html;
    }

    protected function stripLeadingRegexFromHtml(string $html, string $pattern): string
    {
        // Strip leading prefix even if wrapped in <strong>/<span>
        $stripped = preg_replace($pattern, '', $html, 1);
        if ($stripped !== $html) {
            return $stripped ?? $html;
        }

        return preg_replace('/^(<(?:strong|b|em|i|u|span)[^>]*>)\s*(?:[A-E]|\d{1,3})[\.\)]\s*/u', '$1', $html, 1) ?? $html;
    }
}
