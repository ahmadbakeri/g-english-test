<x-filament-widgets::widget>
    <x-filament::section class="g-welcome-section">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding: 0.5rem 0;">
            <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                <span class="g-welcome-greeting" style="font-size: 0.825rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">
                    &#x1F3C6; G-ENGLISH BRAIN CHAMPION &bull; "ONE MIND, ONE CHAMPION"
                </span>
                <h2 class="g-welcome-title" style="font-size: 1.875rem; font-weight: 800; background: linear-gradient(to right, #0a4282, #2563eb); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin: 0;">
                    {{ auth()->user()->name }}
                </h2>
                <p class="g-welcome-subtitle" style="font-size: 0.925rem; color: #475569; margin: 0;" class="dark:text-slate-300">
                    Portal Resmi Koordinator Sekolah (ID Login: <strong>{{ auth()->user()->username }}</strong>). Kelola biodata delegasi peserta sekolah Anda dan pantau sisa kuota kompetisi secara real-time.
                </p>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div class="g-welcome-date-badge" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #eff6ff, #dbeafe); border: 1px solid #bfdbfe; padding: 0.65rem 1.15rem; border-radius: 9999px;">
                    <x-heroicon-o-calendar style="color: #0a4282; width: 1.2rem; height: 1.2rem; flex-shrink: 0;" />
                    <span style="font-size: 0.85rem; font-weight: 700; color: #0a4282;">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
