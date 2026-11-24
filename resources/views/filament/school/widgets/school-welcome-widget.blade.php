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

        <div class="gx-welcome-actions">
            <div class="gx-welcome-date-pill">
                <x-heroicon-o-calendar style="width: 1.05rem; height: 1.05rem; color: #fbb900;" />
                <span>{{ now('Asia/Makassar')->locale('id')->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
