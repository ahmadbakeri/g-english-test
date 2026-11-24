<link rel="stylesheet" href="{{ asset('css/genza-panel.css') }}?v=3">

<div id="lang-switch-overlay">
    <div class="spinner-elegant"></div>
    <div class="lang-text-elegant" id="lang-switch-text">Translating interface...</div>
</div>

<script>
    function hydrateFilamentTableLabels() {
        document.querySelectorAll('.fi-ta-table').forEach((table) => {
            const headers = Array.from(table.querySelectorAll('thead tr th')).map((th) =>
                (th.innerText || '').replace(/\s+/g, ' ').trim()
            );
            if (!headers.length) return;
            table.querySelectorAll('tbody tr.fi-ta-row').forEach((row) => {
                const cells = Array.from(row.children).filter((el) => el.tagName === 'TD');
                cells.forEach((td, idx) => {
                    if (td.classList.contains('fi-ta-selection-cell') || td.classList.contains('fi-ta-actions-cell')) {
                        return;
                    }
                    const label = headers[idx] || '';
                    if (label) {
                        td.setAttribute('data-label', label);
                    }
                });
            });
            if (!table.dataset.gxObserved) {
                table.dataset.gxObserved = '1';
                const tbody = table.querySelector('tbody');
                if (tbody) {
                    new MutationObserver(() => hydrateFilamentTableLabels()).observe(tbody, { childList: true });
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', hydrateFilamentTableLabels);
    document.addEventListener('livewire:navigated', hydrateFilamentTableLabels);

    document.addEventListener('livewire:init', () => {
        hydrateFilamentTableLabels();
        if (window.Livewire && typeof Livewire.hook === 'function') {
            Livewire.hook('commit', ({ succeed }) => {
                succeed(() => queueMicrotask(hydrateFilamentTableLabels));
            });
        }
        Livewire.on('language-changed', (event) => {
            const locale = event[0]?.locale || event.locale || 'en';
            const overlay = document.getElementById('lang-switch-overlay');
            const text = document.getElementById('lang-switch-text');
            
            // Set text based on target language
            if (locale === 'id') {
                text.innerText = 'Menyiapkan Bahasa Indonesia...';
            } else if (locale === 'zh_CN' || locale === 'zh-CN') {
                text.innerText = '切换到中文...';
            } else if (locale === 'zh_PY' || locale === 'zh-PY') {
                text.innerText = 'Qiēhuàn dào Zhōngwén...';
            } else if (locale === 'ja') {
                text.innerText = '日本語に切り替えています...';
            } else if (locale === 'ja_RM' || locale === 'ja-RM') {
                text.innerText = 'Nihongo ni kirikaete imasu...';
            } else if (locale === 'ar') {
                text.innerText = 'جاري التحضير للغة العربية...';
            } else if (locale === 'ar_RM' || locale === 'ar-RM') {
                text.innerText = 'Jari al-tahdir lil-lughah al-Arabiyyah...';
            } else {
                text.innerText = 'Switching to English...';
            }
            
            // Activate animation
            overlay.classList.add('active');
            
            // Wait for animation to finish, then hard reload to ensure perfect translation
            setTimeout(() => {
                window.location.reload();
            }, 600);
        });
    });
</script>
