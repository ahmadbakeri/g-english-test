<x-filament-widgets::widget>
    @php
        $user = auth()->user();
        $photoUrl = $user?->photo_url;
        $initials = $user?->initials ?: 'DN';
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
                    <span class="gx-welcome-role-pill">Portal Monitoring Dinas</span>
                    <span class="gx-welcome-id-pill">{{ $totalSchools }} Sekolah • {{ $totalParticipants }} Peserta</span>
                </div>
                <h2 class="gx-welcome-title">Selamat Datang, {{ $agencyLabel }}</h2>
                <p class="gx-welcome-sub">
                    <span>{{ __('Official monitoring portal for participating schools and students in your jurisdiction.') }}</span>
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
