<style>
    /* 1. Animasi Transisi Tema (Light <-> Dark) */
    html, body, .fi-topbar, .fi-sidebar, .fi-main {
        transition: background-color 0.4s ease-in-out, color 0.4s ease-in-out, border-color 0.4s ease-in-out, box-shadow 0.4s ease-in-out;
    }

    /* 2. Animasi Transisi Halaman (View Transitions API) */
    @keyframes fade-in {
        from { opacity: 0; }
    }

    @keyframes fade-out {
        to { opacity: 0; }
    }

    @keyframes slide-from-right {
        from { transform: translateX(30px); opacity: 0; }
    }

    @keyframes slide-to-left {
        to { transform: translateX(-30px); opacity: 0; }
    }

    ::view-transition-old(root) {
        animation: 200ms cubic-bezier(0.4, 0, 1, 1) both fade-out;
    }

    ::view-transition-new(root) {
        animation: 400ms cubic-bezier(0, 0, 0.2, 1) both fade-in,
                   400ms cubic-bezier(0.4, 0, 0.2, 1) both slide-from-right;
    }

    /* Overlay Fullscreen untuk Ganti Bahasa */
    #lang-switch-overlay {
        position: fixed;
        inset: 0;
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(12px) saturate(150%);
        -webkit-backdrop-filter: blur(12px) saturate(150%);
        z-index: 999999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.4s ease-out;
    }
    
    .dark #lang-switch-overlay {
        background: rgba(15, 23, 42, 0.7);
    }

    #lang-switch-overlay.active {
        opacity: 1;
        pointer-events: all;
    }

    /* Elegant Text */
    .lang-text-elegant {
        font-size: 1.1rem;
        font-weight: 500;
        color: #475569;
        letter-spacing: 0.05em;
        margin-top: 1.5rem;
        animation: pulse-soft 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    .dark .lang-text-elegant {
        color: #cbd5e1;
    }

    @keyframes pulse-soft {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(0.98); }
    }
    
    .spinner-elegant {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 9999px;
        border: 3px solid rgba(var(--primary-500), 0.2);
        border-top-color: rgb(var(--primary-500));
        animation: spin 1s cubic-bezier(0.5, 0, 0.5, 1) infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<div id="lang-switch-overlay">
    <div class="spinner-elegant"></div>
    <div class="lang-text-elegant" id="lang-switch-text">Translating interface...</div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
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
