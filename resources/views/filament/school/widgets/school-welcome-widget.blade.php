<x-filament-widgets::widget>
    @php
        $user = auth()->user();
        $photoUrl = $user?->photo_url;
        $initials = $user?->initials ?: 'SK';
    @endphp

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
                    <span class="gx-welcome-role-pill">Portal Sekolah</span>
                    <span class="gx-welcome-id-pill">ID: {{ $user?->username }}</span>
                </div>
                <h2 class="gx-welcome-title">Selamat Datang, {{ $user?->name }}</h2>
                <p class="gx-welcome-sub">
                    <span>G-English Brain Champion — Kelola delegasi peserta sekolah &amp; cetak kartu resmi.</span>
                </p>
            </div>
        </div>

        <div class="gx-welcome-actions" style="display:flex; flex-wrap:wrap; align-items:center; gap:0.5rem;">
            <a href="{{ asset('juknis/Juknis-Lomba-Brain-Champion-G-English.pdf') }}"
               download="Juknis-Lomba-Brain-Champion-G-English.pdf"
               target="_blank"
               class="gx-welcome-date-pill"
               style="background:#fbb900; color:#0a4282; font-weight:800; text-decoration:none;">
                <x-heroicon-o-arrow-down-tray style="width: 1rem; height: 1rem; color: #0a4282;" />
                <span>Juknis Lomba</span>
            </a>
            <a href="{{ asset('juknis/Juknis-Pendaftaran-Brain-Champion-G-English.pdf') }}"
               download="Juknis-Pendaftaran-Brain-Champion-G-English.pdf"
               target="_blank"
               class="gx-welcome-date-pill"
               style="text-decoration:none;">
                <x-heroicon-o-arrow-down-tray style="width: 1rem; height: 1rem; color: #fbb900;" />
                <span>Juknis Pendaftaran</span>
            </a>
            <a href="{{ asset('juknis/Juknis-Penggunaan-Aplikasi-G-English.pdf') }}"
               download="Juknis-Penggunaan-Aplikasi-G-English.pdf"
               target="_blank"
               class="gx-welcome-date-pill"
               style="text-decoration:none;">
                <x-heroicon-o-arrow-down-tray style="width: 1rem; height: 1rem; color: #fbb900;" />
                <span>Juknis Penggunaan</span>
            </a>
            <div class="gx-welcome-date-pill">
                <x-heroicon-o-calendar style="width: 1.05rem; height: 1.05rem; color: #fbb900;" />
                <span>{{ now('Asia/Makassar')->locale('id')->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
