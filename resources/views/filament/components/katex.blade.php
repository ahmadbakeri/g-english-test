<link rel="stylesheet" href="{{ asset('vendor/katex/katex.min.css') }}">
<script defer src="{{ asset('vendor/katex/katex.min.js') }}"></script>
<script defer src="{{ asset('vendor/katex/contrib/auto-render.min.js') }}"></script>
<style>
    /* Editorial UTBK-SNBT CBT Typography (Shared across Student CBT & Admin Staging Preview) */
    .cbt-rich-content {
        font-family: "Plus Jakarta Sans", "Outfit", ui-sans-serif, system-ui, -apple-system, sans-serif;
        font-size: 1.03rem;
        font-weight: 400;
        line-height: 1.82;
        letter-spacing: 0.005em;
        color: #1e293b;
        text-align: justify;
        text-justify: inter-word;
        text-align-last: left;
        overflow-wrap: break-word;
        word-break: normal;
        hyphens: auto;
    }
    .dark .cbt-rich-content {
        color: #e2e8f0;
    }
    .cbt-rich-content p {
        margin: 0 0 0.85rem 0;
        text-align: justify;
        text-justify: inter-word;
        text-align-last: left;
    }
    .cbt-rich-content p:last-child {
        margin-bottom: 0;
    }
    .cbt-rich-content strong,
    .cbt-rich-content b {
        font-weight: 700;
        color: #0f172a;
    }
    .dark .cbt-rich-content strong,
    .dark .cbt-rich-content b {
        color: #ffffff;
    }
    .cbt-rich-content ul,
    .cbt-rich-content ol {
        margin: 0.55rem 0 0.95rem 0;
        padding-left: 1.55rem;
        text-align: justify;
        text-justify: inter-word;
        text-align-last: left;
    }
    .cbt-rich-content ul {
        list-style-type: disc;
    }
    .cbt-rich-content ol {
        list-style-type: decimal;
    }
    .cbt-rich-content li {
        margin-bottom: 0.38rem;
        padding-left: 0.2rem;
    }
    .cbt-rich-content li::marker {
        color: #0a4282;
        font-weight: 700;
    }
    .dark .cbt-rich-content li::marker {
        color: #fbb900;
    }
    .cbt-rich-content img {
        max-width: 100%;
        max-height: 400px;
        width: auto;
        height: auto;
        object-fit: contain;
        border-radius: 0.85rem;
        margin: 0.9rem auto;
        padding: 0.55rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 6px 18px -6px rgba(15, 23, 42, 0.12);
        display: block;
    }
    .dark .cbt-rich-content img {
        border-color: #475569;
        box-shadow: 0 8px 22px -6px rgba(0, 0, 0, 0.45);
    }
    .cbt-rich-content p:has(> img:only-child) {
        text-align: center;
        text-align-last: center;
    }
    .cbt-rich-content table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 1rem 0 1.15rem 0;
        font-size: 0.93rem;
        line-height: 1.55;
        text-align: left;
        text-align-last: left;
        border: 1.5px solid #cbd5e1;
        border-radius: 0.75rem;
        overflow: hidden;
    }
    .dark .cbt-rich-content table {
        border-color: #475569;
    }
    .cbt-rich-content th,
    .cbt-rich-content td {
        border-right: 1px solid #cbd5e1;
        border-bottom: 1px solid #cbd5e1;
        padding: 0.62rem 0.85rem;
        vertical-align: middle;
    }
    .cbt-rich-content tr > *:last-child {
        border-right: none;
    }
    .cbt-rich-content tr:last-child > * {
        border-bottom: none;
    }
    .cbt-rich-content th {
        background: rgba(10, 66, 130, 0.09);
        color: #0a4282;
        font-weight: 800;
        text-align: center;
        text-align-last: center;
    }
    .cbt-rich-content tbody tr:nth-child(even) td {
        background: rgba(248, 250, 252, 0.65);
    }
    .dark .cbt-rich-content th {
        background: rgba(251, 185, 0, 0.16);
        color: #fbb900;
        border-color: #475569;
    }
    .dark .cbt-rich-content td {
        border-color: #475569;
    }
    .dark .cbt-rich-content tbody tr:nth-child(even) td {
        background: rgba(15, 23, 42, 0.45);
    }
    .cbt-rich-content .cbt-data-table td {
        text-align: center;
        text-align-last: center;
    }
    .cbt-rich-content .cbt-tf-stmt {
        text-align: justify;
        text-justify: inter-word;
        text-align-last: left;
    }
    .cbt-rich-content .katex {
        font-size: 1.08em;
    }
    .cbt-rich-content .katex-display {
        margin: 0.85rem 0;
        padding: 0.45rem 0;
        overflow-x: auto;
        overflow-y: hidden;
        text-align: center;
        text-align-last: center;
    }
    /* Compact option styling inside MCQ choices */
    .cbt-option-content {
        font-size: 0.98rem;
        font-weight: 500;
        line-height: 1.65;
        text-align: justify;
        text-justify: inter-word;
        text-align-last: left;
    }
    .cbt-option-content p {
        margin: 0;
    }
    .cbt-option-content img {
        margin: 0.35rem 0;
        max-height: 180px;
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
