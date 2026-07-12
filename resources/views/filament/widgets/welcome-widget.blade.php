<x-filament-widgets::widget>
    <x-filament::section class="g-welcome-section">
        <style>
            .g-welcome-container {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 1rem;
                padding: 1rem 0;
            }
            .g-welcome-left {
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
            }
            .g-welcome-greeting {
                font-size: 0.875rem;
                font-weight: 500;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            .g-welcome-title {
                font-size: 1.875rem;
                font-weight: 800;
                color: #1e3a8a; /* deep blue */
                background: linear-gradient(to right, #1e3a8a, #3b82f6);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                letter-spacing: -0.025em;
                margin: 0;
            }
            .g-welcome-subtitle {
                font-size: 0.95rem;
                color: #475569;
                margin: 0;
            }
            .g-welcome-right {
                display: flex;
                align-items: center;
            }
            .g-welcome-date-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                background: linear-gradient(135deg, #eff6ff, #dbeafe);
                border: 1px solid #bfdbfe;
                padding: 0.75rem 1.25rem;
                border-radius: 9999px; /* Pill shape */
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .g-welcome-date-icon {
                color: #3b82f6;
                width: 1.25rem;
                height: 1.25rem;
                flex-shrink: 0;
            }
            .g-welcome-date-text {
                font-size: 0.875rem;
                font-weight: 600;
                color: #1d4ed8;
            }
            
            /* Dark mode support */
            .dark .g-welcome-greeting {
                color: #94a3b8;
            }
            .dark .g-welcome-title {
                background: linear-gradient(to right, #60a5fa, #93c5fd);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .dark .g-welcome-subtitle {
                color: #cbd5e1;
            }
            .dark .g-welcome-date-badge {
                background: rgba(59, 130, 246, 0.1);
                border-color: rgba(59, 130, 246, 0.2);
            }
            .dark .g-welcome-date-icon {
                color: #60a5fa;
            }
            .dark .g-welcome-date-text {
                color: #93c5fd;
            }
        </style>

        <div class="g-welcome-container">
            <div class="g-welcome-left">
                <span class="g-welcome-greeting">👋 {{ __('Welcome back') }}</span>
                <h2 class="g-welcome-title">G-ENGLISH</h2>
                <p class="g-welcome-subtitle">{{ __('Integrated English Course Administration System') }}</p>
            </div>
            <div class="g-welcome-right">
                <div class="g-welcome-date-badge">
                    <x-heroicon-o-calendar class="g-welcome-date-icon" />
                    <span class="g-welcome-date-text">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
