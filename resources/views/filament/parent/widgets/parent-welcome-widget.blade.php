<x-filament-widgets::widget>
    <style>
        .parent-child-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 0.85rem;
            margin-top: 1.25rem;
            padding-top: 1.15rem;
            border-top: 1px solid rgba(148, 163, 184, 0.2);
        }
        .parent-child-card {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.85rem 1rem;
            border-radius: 1rem;
            background: rgba(248, 250, 252, 0.9);
            border: 1px solid rgba(10, 66, 130, 0.12);
            transition: all 0.2s ease;
        }
        .dark .parent-child-card {
            background: rgba(15, 23, 42, 0.55);
            border-color: rgba(255, 255, 255, 0.08);
        }
        .parent-child-card:hover {
            border-color: #0a4282;
            transform: translateY(-2px);
        }
        .dark .parent-child-card:hover {
            border-color: #fbb900;
        }
        .parent-child-avatar {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, #0a4282, #1a368b);
            color: #fbb900;
            font-weight: 800;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
    </style>

    <x-filament::section class="g-welcome-section">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding: 0.5rem 0;">
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <span class="g-welcome-greeting" style="font-size: 0.825rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">
                    &#x1F44B; WELCOME TO PARENT PORTAL!
                </span>
                <h2 class="g-welcome-title dark:text-white" style="font-size: 1.85rem; font-weight: 800; color: #0a4282; margin: 0; letter-spacing: -0.025em;">
                    {{ auth()->user()->name }}
                </h2>
                <p class="g-welcome-subtitle" style="font-size: 0.925rem; color: #475569; margin: 0;" class="dark:text-slate-300">
                    Pantau kehadiran, jurnal belajar, dan tagihan SPP {{ auth()->user()->children->pluck('name')->join(', ', ' dan ') ?: 'anak Anda' }} secara real-time.
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

        @if(auth()->user()->children->count() > 0)
            <div class="parent-child-grid">
                @foreach(auth()->user()->children as $child)
                    <div class="parent-child-card">
                        <div class="parent-child-avatar">
                            {{ mb_strtoupper(mb_substr($child->name, 0, 1)) }}
                        </div>
                        <div style="min-width: 0; flex: 1;">
                            <div style="display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap;">
                                <span style="font-weight: 800; font-size: 0.95rem; color: #0f172a;" class="dark:text-white">{{ $child->name }}</span>
                                <span style="padding: 0.1rem 0.55rem; border-radius: 9999px; font-size: 0.68rem; font-weight: 800; background: {{ $child->student_type === 'external' ? 'rgba(245, 158, 11, 0.16)' : 'rgba(14, 165, 233, 0.16)' }}; color: {{ $child->student_type === 'external' ? '#d97706' : '#0284c7' }};">
                                    {{ $child->student_type === 'external' ? 'Eksternal' : 'Internal' }}
                                </span>
                            </div>
                            <div style="font-size: 0.78rem; color: #64748b; font-weight: 600; margin-top: 0.1rem;">
                                NIS: {{ $child->nis ?: '-' }} &bull; {{ $child->class_name ?: ($child->school_name ?: 'Siswa Aktif') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
