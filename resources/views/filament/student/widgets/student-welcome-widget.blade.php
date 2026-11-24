<x-filament-widgets::widget>
    @php
        $user = auth()->user();
        $photoUrl = $user?->photo_url;
        $initials = $user?->initials ?: 'SW';
        $nis = $user?->student?->nis;
        $profileUrl = null;
        try {
            $profileUrl = filament()->getProfileUrl();
        } catch (\Throwable) {
        }
        $hour = now('Asia/Makassar')->hour;
        $greeting = match (true) {
            $hour < 11 => 'Selamat Pagi',
            $hour < 15 => 'Selamat Siang',
            $hour < 18 => 'Selamat Sore',
            default => 'Selamat Malam',
        };
    @endphp

    <div class="gx-welcome-hero">
        <div class="gx-welcome-identity">
            <a href="{{ $profileUrl ?: '#' }}" class="gx-welcome-avatar-link" title="Profil Siswa">
                @if ($photoUrl)
                    <img src="{{ $photoUrl }}" alt="{{ $user?->name }}" class="gx-welcome-avatar-img" />
                @else
                    <div class="gx-welcome-avatar-fallback">{{ $initials }}</div>
                @endif
                <span class="gx-welcome-avatar-dot" aria-hidden="true"></span>
            </a>

            <div class="gx-welcome-main">
                <div class="gx-welcome-meta-row">
                    <span class="gx-welcome-role-pill">Portal Siswa</span>
                    @if ($nis)
                        <span class="gx-welcome-id-pill">NIS: {{ $nis }}</span>
                    @elseif ($user?->username)
                        <span class="gx-welcome-id-pill">{{ '@' . $user->username }}</span>
                    @endif
                </div>
                <h2 class="gx-welcome-title">{{ $greeting }}, {{ $user?->name }}</h2>
                <p class="gx-welcome-sub">
                    <span>{{ __('View your class schedule, grades, and attendance history') }}</span>
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
