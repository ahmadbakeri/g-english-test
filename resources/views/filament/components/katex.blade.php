<link rel="stylesheet" href="{{ asset('vendor/katex/katex.min.css') }}">
<script defer src="{{ asset('vendor/katex/katex.min.js') }}"></script>
<script defer src="{{ asset('vendor/katex/contrib/auto-render.min.js') }}"></script>
<style>
    .cbt-rich-content p {
        margin: 0 0 0.6rem 0;
    }
    .cbt-rich-content p:last-child {
        margin-bottom: 0;
    }
    .cbt-rich-content ul,
    .cbt-rich-content ol {
        margin: 0.45rem 0 0.75rem 1.35rem;
        padding-left: 0.35rem;
    }
    .cbt-rich-content ul {
        list-style-type: disc;
    }
    .cbt-rich-content ol {
        list-style-type: decimal;
    }
    .cbt-rich-content li {
        margin-bottom: 0.3rem;
    }
    .cbt-rich-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.65rem;
        margin: 0.65rem auto;
        display: block;
    }
    .cbt-rich-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 0.75rem 0;
        font-size: 0.92rem;
    }
    .cbt-rich-content th,
    .cbt-rich-content td {
        border: 1.5px solid #94a3b8;
        padding: 0.5rem 0.75rem;
        vertical-align: middle;
    }
    .cbt-rich-content th {
        background: rgba(10, 66, 130, 0.08);
        font-weight: 700;
        text-align: center;
    }
    .dark .cbt-rich-content th {
        background: rgba(251, 185, 0, 0.14);
        border-color: #475569;
    }
    .dark .cbt-rich-content td {
        border-color: #475569;
    }
    .cbt-rich-content .katex {
        font-size: 1.06em;
    }
    .cbt-rich-content .katex-display {
        margin: 0.75rem 0;
        overflow-x: auto;
        overflow-y: hidden;
    }
</style>
<script>
    window.renderCbtMath = function (rootEl) {
        const target = rootEl || document.body;
        if (!target) return;
        if (typeof window.renderMathInElement !== 'function') {
            setTimeout(() => window.renderCbtMath(target), 120);
            return;
        }
        try {
            window.renderMathInElement(target, {
                delimiters: [
                    { left: '\\[', right: '\\]', display: true },
                    { left: '\\(', right: '\\)', display: false },
                    { left: '$$', right: '$$', display: true },
                ],
                throwOnError: false,
                strict: false,
                ignoredTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code'],
            });
        } catch (e) {}
    };

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-cbt-math]').forEach((el) => window.renderCbtMath(el));
    });
    document.addEventListener('livewire:navigated', () => {
        setTimeout(() => {
            document.querySelectorAll('[data-cbt-math]').forEach((el) => window.renderCbtMath(el));
        }, 60);
    });
</script>
