<?php

namespace App\Services\CbtDocx;

use DOMElement;
use DOMNode;

class OmmlToLatexConverter
{
    public const OMML_NS = 'http://schemas.openxmlformats.org/officeDocument/2006/math';

    /** @var array<int, string> */
    protected array $unrecognizedNodes = [];

    /**
     * Convert an <m:oMath> or <m:oMathPara> node into LaTeX wrapped in \(...\) or \[...\].
     *
     * @return array{latex: string, wrapped: string, warnings: array<int, string>}
     */
    public function convert(DOMNode $node, bool $display = false): array
    {
        $this->unrecognizedNodes = [];
        $latex = trim($this->walk($node));
        $latex = preg_replace('/\s{2,}/', ' ', $latex) ?? $latex;

        if ($latex === '') {
            return [
                'latex' => '',
                'wrapped' => '',
                'warnings' => $this->unrecognizedNodes,
            ];
        }

        $wrapped = $display ? "\\[{$latex}\\]" : "\\({$latex}\\)";

        return [
            'latex' => $latex,
            'wrapped' => $wrapped,
            'warnings' => array_values(array_unique($this->unrecognizedNodes)),
        ];
    }

    protected function walk(DOMNode $node): string
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            return $this->normalizeMathText($node->nodeValue ?? '');
        }

        if (!($node instanceof DOMElement)) {
            return '';
        }

        $local = $node->localName;

        return match ($local) {
            'oMathPara', 'oMath', 'e', 'num', 'den', 'sub', 'sup', 'deg', 'lim', 'fName', 'mr', 'box', 'borderBox', 'phant' => $this->walkChildren($node),
            'r' => $this->handleRun($node),
            't' => $this->normalizeMathText($node->textContent ?? ''),
            'f' => $this->handleFraction($node),
            'rad' => $this->handleRadical($node),
            'sSup' => $this->handleSup($node),
            'sSub' => $this->handleSub($node),
            'sSubSup' => $this->handleSubSup($node),
            'sPre' => $this->handlePreScript($node),
            'd' => $this->handleDelimiter($node),
            'nary' => $this->handleNary($node),
            'm' => $this->handleMatrix($node),
            'func' => $this->handleFunction($node),
            'limLow' => $this->handleLimLow($node),
            'limUpp' => $this->handleLimUpp($node),
            'bar' => $this->handleBar($node),
            'acc' => $this->handleAccent($node),
            'eqArr' => $this->handleEqArr($node),
            'groupChr' => $this->handleGroupChr($node),
            // Properties / metadata nodes ignored in output
            'oMathParaPr', 'rPr', 'ctrlPr', 'fPr', 'radPr', 'sSupPr', 'sSubPr', 'sSubSupPr',
            'sPrePr', 'dPr', 'naryPr', 'mPr', 'funcPr', 'limLowPr', 'limUppPr', 'barPr',
            'accPr', 'eqArrPr', 'boxPr', 'borderBoxPr', 'groupChrPr', 'phantPr', 'argPr',
            'mcs', 'mc', 'mcPr', 'jc', 'chr', 'begChr', 'endChr', 'sepChr', 'type', 'pos', 'degHide' => '',
            default => $this->handleUnknown($node),
        };
    }

    protected function walkChildren(DOMNode $node): string
    {
        $out = '';
        foreach ($node->childNodes as $child) {
            $out .= $this->walk($child);
        }

        return $out;
    }

    protected function handleRun(DOMElement $node): string
    {
        $text = '';
        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMElement && $child->localName === 't') {
                $text .= $this->normalizeMathText($child->textContent ?? '');
            }
        }

        return $text;
    }

    protected function handleFraction(DOMElement $node): string
    {
        $num = trim($this->childContent($node, 'num'));
        $den = trim($this->childContent($node, 'den'));
        $type = $this->propVal($node, 'fPr', 'type');

        if ($type === 'noBar') {
            return "\\binom{{$num}}{{$den}}";
        }
        if ($type === 'lin') {
            return "{{$num}}/{{$den}}";
        }

        return "\\frac{{$num}}{{$den}}";
    }

    protected function handleRadical(DOMElement $node): string
    {
        $deg = trim($this->childContent($node, 'deg'));
        $base = trim($this->childContent($node, 'e'));

        if ($deg !== '') {
            return "\\sqrt[{$deg}]{{$base}}";
        }

        return "\\sqrt{{$base}}";
    }

    protected function handleSup(DOMElement $node): string
    {
        $base = trim($this->childContent($node, 'e'));
        $sup = trim($this->childContent($node, 'sup'));

        return "{{$base}}^{{$sup}}";
    }

    protected function handleSub(DOMElement $node): string
    {
        $base = trim($this->childContent($node, 'e'));
        $sub = trim($this->childContent($node, 'sub'));

        return "{{$base}}_{{$sub}}";
    }

    protected function handleSubSup(DOMElement $node): string
    {
        $base = trim($this->childContent($node, 'e'));
        $sub = trim($this->childContent($node, 'sub'));
        $sup = trim($this->childContent($node, 'sup'));

        return "{{$base}}_{{$sub}}^{{$sup}}";
    }

    protected function handlePreScript(DOMElement $node): string
    {
        $base = trim($this->childContent($node, 'e'));
        $sub = trim($this->childContent($node, 'sub'));
        $sup = trim($this->childContent($node, 'sup'));

        return "{}_{{$sub}}^{{$sup}}{{$base}}";
    }

    protected function handleDelimiter(DOMElement $node): string
    {
        $beg = $this->propVal($node, 'dPr', 'begChr');
        $end = $this->propVal($node, 'dPr', 'endChr');
        $sep = $this->propVal($node, 'dPr', 'sepChr');

        $beg = $beg === null ? '(' : $beg;
        $end = $end === null ? ')' : $end;
        $sep = $sep === null ? ',' : $sep;

        $parts = [];
        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMElement && $child->localName === 'e') {
                $parts[] = trim($this->walk($child));
            }
        }

        $body = implode($sep . ' ', $parts);
        $left = $this->escapeDelimiter($beg);
        $right = $this->escapeDelimiter($end);

        return "\\left{$left}{$body}\\right{$right}";
    }

    protected function escapeDelimiter(string $ch): string
    {
        return match ($ch) {
            '' => '.',
            '{' => '\\{',
            '}' => '\\}',
            '|', '∣' => '|',
            '‖' => '\\|',
            '⟨', '〈' => '\\langle',
            '⟩', '〉' => '\\rangle',
            '⌈' => '\\lceil',
            '⌉' => '\\rceil',
            '⌊' => '\\lfloor',
            '⌋' => '\\rfloor',
            default => $ch,
        };
    }

    protected function handleNary(DOMElement $node): string
    {
        $chr = $this->propVal($node, 'naryPr', 'chr') ?? '∫';
        $op = match ($chr) {
            '∑' => '\\sum',
            '∏' => '\\prod',
            '∐' => '\\coprod',
            '∫' => '\\int',
            '∬' => '\\iint',
            '∭' => '\\iiint',
            '∮' => '\\oint',
            '⋃' => '\\bigcup',
            '⋂' => '\\bigcap',
            default => '\\int',
        };

        $sub = trim($this->childContent($node, 'sub'));
        $sup = trim($this->childContent($node, 'sup'));
        $body = trim($this->childContent($node, 'e'));

        $out = $op;
        if ($sub !== '') {
            $out .= "_{{$sub}}";
        }
        if ($sup !== '') {
            $out .= "^{{$sup}}";
        }

        return trim("{$out} {$body}");
    }

    protected function handleMatrix(DOMElement $node): string
    {
        $rows = [];
        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMElement && $child->localName === 'mr') {
                $cells = [];
                foreach ($child->childNodes as $cell) {
                    if ($cell instanceof DOMElement && $cell->localName === 'e') {
                        $cells[] = trim($this->walk($cell));
                    }
                }
                $rows[] = implode(' & ', $cells);
            }
        }

        return '\\begin{matrix} ' . implode(' \\\\ ', $rows) . ' \\end{matrix}';
    }

    protected function handleFunction(DOMElement $node): string
    {
        $name = trim($this->childContent($node, 'fName'));
        $arg = trim($this->childContent($node, 'e'));

        $known = ['sin', 'cos', 'tan', 'cot', 'sec', 'csc', 'log', 'ln', 'lim', 'max', 'min', 'det', 'gcd', 'exp'];
        $clean = strtolower($name);
        $prefix = in_array($clean, $known, true) ? "\\{$clean}" : $name;

        return trim("{$prefix} {$arg}");
    }

    protected function handleLimLow(DOMElement $node): string
    {
        $base = trim($this->childContent($node, 'e'));
        $lim = trim($this->childContent($node, 'lim'));

        if (strtolower($base) === 'lim') {
            return "\\lim_{{$lim}}";
        }

        return "\\underset{{$lim}}{{$base}}";
    }

    protected function handleLimUpp(DOMElement $node): string
    {
        $base = trim($this->childContent($node, 'e'));
        $lim = trim($this->childContent($node, 'lim'));

        return "\\overset{{$lim}}{{$base}}";
    }

    protected function handleBar(DOMElement $node): string
    {
        $pos = $this->propVal($node, 'barPr', 'pos') ?? 'top';
        $base = trim($this->childContent($node, 'e'));

        return $pos === 'bot' ? "\\underline{{$base}}" : "\\overline{{$base}}";
    }

    protected function handleAccent(DOMElement $node): string
    {
        $chr = $this->propVal($node, 'accPr', 'chr') ?? '^';
        $base = trim($this->childContent($node, 'e'));

        $cmd = match ($chr) {
            "\u{0302}", '^' => '\\hat',
            "\u{0304}", "\u{00AF}", '¯' => '\\bar',
            "\u{20D7}", '→' => '\\vec',
            "\u{0303}", '~' => '\\tilde',
            "\u{0307}", '˙' => '\\dot',
            "\u{0308}", '¨' => '\\ddot',
            default => '\\hat',
        };

        return "{$cmd}{{$base}}";
    }

    protected function handleEqArr(DOMElement $node): string
    {
        $lines = [];
        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMElement && $child->localName === 'e') {
                $lines[] = trim($this->walk($child));
            }
        }

        return '\\begin{aligned} ' . implode(' \\\\ ', $lines) . ' \\end{aligned}';
    }

    protected function handleGroupChr(DOMElement $node): string
    {
        $chr = $this->propVal($node, 'groupChrPr', 'chr') ?? '⏟';
        $pos = $this->propVal($node, 'groupChrPr', 'pos') ?? 'bot';
        $base = trim($this->childContent($node, 'e'));

        if ($chr === '⏞' || $pos === 'top') {
            return "\\overbrace{{$base}}";
        }

        return "\\underbrace{{$base}}";
    }

    protected function handleUnknown(DOMElement $node): string
    {
        if ($node->namespaceURI === self::OMML_NS) {
            $this->unrecognizedNodes[] = "[Parser] Node OMML tidak dikenal: <m:{$node->localName}>";
        }

        return $this->walkChildren($node);
    }

    protected function childContent(DOMElement $parent, string $localName): string
    {
        foreach ($parent->childNodes as $child) {
            if ($child instanceof DOMElement && $child->localName === $localName) {
                return $this->walk($child);
            }
        }

        return '';
    }

    protected function propVal(DOMElement $parent, string $prName, string $propTag): ?string
    {
        foreach ($parent->childNodes as $child) {
            if ($child instanceof DOMElement && $child->localName === $prName) {
                foreach ($child->childNodes as $prop) {
                    if ($prop instanceof DOMElement && $prop->localName === $propTag) {
                        $val = $prop->getAttributeNS(self::OMML_NS, 'val');
                        if ($val === '' && $prop->hasAttribute('m:val')) {
                            $val = $prop->getAttribute('m:val');
                        }
                        if ($val === '' && $prop->hasAttribute('val')) {
                            $val = $prop->getAttribute('val');
                        }

                        return $val;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Normalize Unicode Mathematical Alphanumeric Symbols (U+1D400-U+1D7FF) and common math glyphs into clean LaTeX.
     */
    public function normalizeMathText(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $symbolMap = [
            'π' => '\pi ',
            '𝜋' => '\pi ',
            '𝝅' => '\pi ',
            'α' => '\alpha ',
            '𝛼' => '\alpha ',
            'β' => '\beta ',
            '𝛽' => '\beta ',
            'γ' => '\gamma ',
            '𝛾' => '\gamma ',
            'δ' => '\delta ',
            '𝛿' => '\delta ',
            'Δ' => '\Delta ',
            '𝜃' => '\theta ',
            'θ' => '\theta ',
            'λ' => '\lambda ',
            '𝜆' => '\lambda ',
            'μ' => '\mu ',
            '𝜇' => '\mu ',
            'σ' => '\sigma ',
            '𝜎' => '\sigma ',
            'Σ' => '\Sigma ',
            'ω' => '\omega ',
            '𝜔' => '\omega ',
            'Ω' => '\Omega ',
            '≤' => '\le ',
            '≥' => '\ge ',
            '≠' => '\neq ',
            '≈' => '\approx ',
            '≡' => '\equiv ',
            '±' => '\pm ',
            '∓' => '\mp ',
            '×' => '\times ',
            '·' => '\cdot ',
            '⋅' => '\cdot ',
            '÷' => '\div ',
            '∞' => '\infty ',
            '°' => '^\circ ',
            '−' => '-',
            '–' => '-',
            '∈' => '\in ',
            '∉' => '\notin ',
            '⊂' => '\subset ',
            '⊆' => '\subseteq ',
            '∪' => '\cup ',
            '∩' => '\cap ',
            '∅' => '\emptyset ',
            '→' => '\to ',
            '⇒' => '\Rightarrow ',
            '⇔' => '\Leftrightarrow ',
            '∠' => '\angle ',
            '⊥' => '\perp ',
            '∥' => '\parallel ',
            '△' => '\triangle ',
            '…' => '\dots ',
            '⋯' => '\cdots ',
            '√' => '\sqrt',
            '′' => "'",
            '’' => "'",
            '‘' => "'",
        ];

        $text = strtr($text, $symbolMap);

        // Normalize Mathematical Alphanumeric Latin letters/digits (e.g., 𝑎-𝑧, 𝐴-𝑍, 𝟎-𝟗) to plain ASCII
        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        if (!$chars) {
            return $text;
        }

        $out = '';
        foreach ($chars as $ch) {
            $cp = mb_ord($ch, 'UTF-8');
            $out .= $this->mapMathCodepoint($cp, $ch);
        }

        return $out;
    }

    protected function mapMathCodepoint(int $cp, string $fallback): string
    {
        // Italic lowercase h special case (Planck constant U+210E)
        if ($cp === 0x210E) {
            return 'h';
        }

        $ranges = [
            // Bold A-Z, a-z
            [0x1D400, 0x1D419, ord('A')],
            [0x1D41A, 0x1D433, ord('a')],
            // Italic A-Z, a-z
            [0x1D434, 0x1D44D, ord('A')],
            [0x1D44E, 0x1D467, ord('a')],
            // Bold Italic A-Z, a-z
            [0x1D468, 0x1D481, ord('A')],
            [0x1D482, 0x1D49B, ord('a')],
            // Sans-serif A-Z, a-z
            [0x1D5A0, 0x1D5B9, ord('A')],
            [0x1D5BA, 0x1D5D3, ord('a')],
            // Bold digits 0-9
            [0x1D7CE, 0x1D7D7, ord('0')],
            // Sans-serif digits 0-9
            [0x1D7E2, 0x1D7EB, ord('0')],
        ];

        foreach ($ranges as [$start, $end, $asciiBase]) {
            if ($cp >= $start && $cp <= $end) {
                return chr($asciiBase + ($cp - $start));
            }
        }

        return $fallback;
    }
}
