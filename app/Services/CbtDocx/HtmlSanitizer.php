<?php

namespace App\Services\CbtDocx;

use DOMDocument;
use DOMElement;
use DOMNode;

class HtmlSanitizer
{
    /** @var array<string, array<int, string>> */
    protected array $allowedTags = [
        'p' => ['class'],
        'br' => [],
        'strong' => [],
        'b' => [],
        'em' => [],
        'i' => [],
        'u' => [],
        'sup' => [],
        'sub' => [],
        'ul' => ['class'],
        'ol' => ['class'],
        'li' => [],
        'table' => ['class'],
        'thead' => [],
        'tbody' => [],
        'tr' => [],
        'th' => ['colspan', 'rowspan', 'class'],
        'td' => ['colspan', 'rowspan', 'class'],
        'img' => ['src', 'alt', 'class'],
        'span' => ['class'],
        'div' => ['class'],
        'blockquote' => ['class'],
    ];

    public function clean(?string $html): string
    {
        $html = trim((string) $html);
        if ($html === '') {
            return '';
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $prev = libxml_use_internal_errors(true);
        $wrapped = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>' . $html . '</body></html>';
        $dom->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $body = $dom->getElementsByTagName('body')->item(0);
        if (!$body) {
            return e(strip_tags($html));
        }

        $this->sanitizeNode($body);

        $result = '';
        foreach ($body->childNodes as $child) {
            $result .= $dom->saveHTML($child);
        }

        return trim($result);
    }

    protected function sanitizeNode(DOMNode $node): void
    {
        $children = [];
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            if ($child->nodeType === XML_COMMENT_NODE) {
                $node->removeChild($child);
                continue;
            }

            if ($child instanceof DOMElement) {
                $tag = strtolower($child->tagName);

                if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'svg', 'form'], true)) {
                    $node->removeChild($child);
                    continue;
                }

                if (!array_key_exists($tag, $this->allowedTags)) {
                    // Unwrap disallowed tag but keep its sanitized children
                    $this->sanitizeNode($child);
                    while ($child->firstChild) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $node->removeChild($child);
                    continue;
                }

                $allowedAttrs = $this->allowedTags[$tag];
                $attrsToRemove = [];
                foreach ($child->attributes as $attr) {
                    $attrName = strtolower($attr->name);
                    if (!in_array($attrName, $allowedAttrs, true)) {
                        $attrsToRemove[] = $attr->name;
                        continue;
                    }
                    if ($attrName === 'src' && !$this->isSafeImageSrc($attr->value)) {
                        $attrsToRemove[] = $attr->name;
                    }
                }

                foreach ($attrsToRemove as $remove) {
                    $child->removeAttribute($remove);
                }

                $this->sanitizeNode($child);
            }
        }
    }

    protected function isSafeImageSrc(string $src): bool
    {
        $src = trim($src);

        return str_starts_with($src, '/storage/')
            || str_starts_with($src, 'https://')
            || str_starts_with($src, 'http://')
            || str_starts_with($src, 'data:image/png;base64,')
            || str_starts_with($src, 'data:image/jpeg;base64,')
            || str_starts_with($src, 'data:image/webp;base64,');
    }
}
