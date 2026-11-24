<x-filament-widgets::widget>
    @php
        $user = auth()->user();
        $photoUrl = $user?->photo_url;
        $initials = $user?->initials ?: 'OT';
    @endphp

    <div style="display:flex;flex-direction:column;gap:0.9rem;">
        <div class="gx-welcome-hero">
            <div class="gx-welcome-identity">
                <div class="gx-welcome-avatar-link">
                    @if ($photoUrl)
                        <img src="{{ $photoUrl }}" alt="{{ $user?->name }}" class="gx-welcome-avatar-img" />
                    @else
                        <div class="gx-welcome-avatar-fallback">{{ $initials }}</div>
                    @endif
                    <span class="gx-welcome-avatar-dot" aria-hidden="true"></span>
                </div>

                <div class="gx-welcome-main">
                    <div class="gx-welcome-meta-row">
                        <span class="gx-welcome-role-pill">Portal Orang Tua</span>
                        @if ($user?->username)
                            <span class="gx-welcome-id-pill">{{ $user->username }}</span>
                        @endif
                    </div>
                    <h2 class="gx-welcome-title">Selamat Datang, {{ $user?->name }}</h2>
                    <p class="gx-welcome-sub">
                        <span>Pantau kehadiran, jurnal belajar, dan pembayaran {{ $user?->children->pluck('name')->join(', ', ' dan ') ?: 'anak Anda' }}.</span>
                    </p>
                </div>
            </div>

            <div class="gx-welcome-actions">
                <div class="gx-welcome-date-pill">
                    <x-heroicon-o-calendar style="width: 1.05rem; height: 1.05rem; color: #fbb900;" />
                    <span>{{ now('Asia/Makassar')->locale('id')->translatedFormat('l, d F Y') }}</span>
                </div>
            </div>
        </div>

        @if($user && $user->children->count() > 0)
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:0.75rem;">
                @foreach($user->children as $child)
                    <div class="gx-toolbar" style="justify-content:flex-start;gap:0.85rem;padding:0.8rem 1rem;">
                        @if ($child->photo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($child->photo) }}" alt="{{ $child->name }}" class="gx-avatar" style="width:2.75rem;height:2.75rem;" />
                        @else
                            <div class="gx-avatar" style="width:2.75rem;height:2.75rem;background:#0a4282;color:#fbb900;">
                                {{ mb_strtoupper(mb_substr($child->name, 0, 1)) }}
                            </div>
                        @endif
                        <div style="min-width:0;flex:1;">
                            <div style="display:flex;align-items:center;gap:0.4rem;flex-wrap:wrap;">
                                <span style="font-weight:800;font-size:0.92rem;color:#0a4282;" class="dark:!text-white">{{ $child->name }}</span>
                                <span class="gx-pill {{ $child->student_type === 'external' ? 'gx-pill-gold' : 'gx-pill-primary' }}" style="padding:0.08rem 0.5rem;font-size:0.68rem;">
                                    {{ $child->student_type === 'external' ? 'Eksternal' : 'Internal' }}
                                </span>
                            </div>
                            <div style="font-size:0.76rem;color:#64748b;font-weight:600;margin-top:0.1rem;">
                                NIS: {{ $child->nis ?: '-' }} • {{ $child->class_name ?: ($child->school_name ?: 'Siswa Aktif') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
