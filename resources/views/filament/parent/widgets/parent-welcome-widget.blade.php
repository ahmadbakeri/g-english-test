<x-filament-widgets::widget>
    <x-filament::section>
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding: 1rem 0;">
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <span style="font-size: 0.875rem; font-weight: 500; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">
                    &#x1F44B; WELCOME TO PARENT PORTAL!
                </span>
                <h2 style="font-size: 1.875rem; font-weight: 800; color: #1e3a8a; background: linear-gradient(to right, #1e3a8a, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin: 0;">
                    {{ auth()->user()->name }}
                </h2>
                <p style="font-size: 0.95rem; color: #475569; margin: 0;">
                    Pantau progres belajar dan tagihan {{ auth()->user()->children->pluck('name')->join(', ', ' dan ') ?: 'anak Anda' }} dengan mudah di sini.
                </p>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #eff6ff, #dbeafe); border: 1px solid #bfdbfe; padding: 0.75rem 1.25rem; border-radius: 9999px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                    <x-heroicon-o-calendar style="color: #3b82f6; width: 1.25rem; height: 1.25rem; flex-shrink: 0;" />
                    <span style="font-size: 0.875rem; font-weight: 600; color: #1d4ed8;">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
