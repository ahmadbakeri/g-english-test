<x-filament-widgets::widget>
    @php
        $user = auth()->user();
        $photoUrl = $user?->photo_url;
        $initials = $user?->initials ?: 'AD';
        $roleLabel = $user?->role_label ?: 'Administrator';
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
            <a
                href="{{ $profileUrl ?: '#' }}"
                class="gx-welcome-avatar-link"
                title="Klik untuk ubah profil &amp; foto"
            >
                @if ($photoUrl)
                    <img src="{{ $photoUrl }}" alt="{{ $user?->name }}" class="gx-welcome-avatar-img" />
                @else
                    <div class="gx-welcome-avatar-fallback">{{ $initials }}</div>
                @endif
                <span class="gx-welcome-avatar-dot" aria-hidden="true"></span>
            </a>

            <div class="gx-welcome-main">
                <div class="gx-welcome-meta-row">
                    <span class="gx-welcome-role-pill">{{ $roleLabel }}</span>
                    @if ($user?->username)
                        <span class="gx-welcome-id-pill">{{ '@' . $user->username }}</span>
                    @endif
                </div>
                <h2 class="gx-welcome-title">
                    {{ $greeting }}, {{ $user?->name ?: 'Admin' }}
                </h2>
                <p class="gx-welcome-sub">
                    <span>{{ __('Integrated English Course Administration System') }}</span>
                    <span class="gx-welcome-dot" aria-hidden="true">•</span>
                    <span class="gx-welcome-date">{{ now('Asia/Makassar')->locale('id')->translatedFormat('l, d F Y') }}</span>
                </p>
            </div>
        </div>

        <div class="gx-welcome-actions">
            <a href="{{ $this->scanUrl }}" class="gx-wbtn gx-wbtn-primary">
                <x-heroicon-o-qr-code style="width: 1rem; height: 1rem;" />
                <span>{{ __('Scan Attendance') }}</span>
            </a>

            @if($user?->isAdmin())
                <a href="{{ $this->scheduleRequestUrl }}" class="gx-wbtn gx-wbtn-gold">
                    <x-heroicon-o-calendar-date-range style="width: 1rem; height: 1rem;" />
                    <span>{{ __('Test Schedule Requests') }}</span>
                    @if($this->pendingScheduleRequests > 0)
                        <span class="gx-wbtn-badge">{{ $this->pendingScheduleRequests }}</span>
                    @endif
                </a>

                <a href="{{ $this->accessLogsUrl }}" class="gx-wbtn gx-wbtn-ghost">
                    <x-heroicon-o-finger-print style="width: 1rem; height: 1rem;" />
                    <span>{{ __('User Access Logs') }}</span>
                </a>
            @endif
        </div>
    </div>
</x-filament-widgets::widget>
