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
                <div style="margin-top: 0.15rem;">
                    <span style="display: inline-block; padding: 0.2rem 0.75rem; border-radius: 9999px; background: rgba(251, 185, 0, 0.18); color: #0a4282; font-size: 0.75rem; font-weight: 800;" class="dark:text-amber-300">
                        Kompetisi Offline di Gedung &bull; Sistem Gugur (Whiteboard &amp; Kertas — Soal Dibacakan Langsung)
                    </span>
                </div>
                <p class="g-welcome-subtitle" style="font-size: 0.925rem; color: #475569; margin: 0.25rem 0 0 0;" class="dark:text-slate-300">
                    Portal Resmi Koordinator Sekolah (ID Login: <strong>{{ auth()->user()->username }}</strong>). Kelola biodata delegasi peserta sekolah Anda dan cetak Kartu Peserta resmi untuk check-in di gedung kompetisi.
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
