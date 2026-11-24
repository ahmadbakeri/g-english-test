<link rel="stylesheet" href="{{ asset('vendor/katex/katex.min.css') }}">
<script defer src="{{ asset('vendor/katex/katex.min.js') }}"></script>
<script defer src="{{ asset('vendor/katex/contrib/auto-render.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/cbt-math.css') }}?v=3">
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
