<x-filament-widgets::widget>
    <div class="gx-welcome-bar">
        <div class="gx-welcome-main">
            <h2 class="gx-welcome-title">
                {{ __('Welcome back') }}, {{ auth()->user()?->name ?: 'Admin' }}
            </h2>
            <p class="gx-welcome-sub">
                {{ __('Integrated English Course Administration System') }}
                <span class="gx-welcome-dot" aria-hidden="true">•</span>
                <span class="gx-welcome-date">{{ now()->translatedFormat('l, d F Y') }}</span>
            </p>
        </div>

        <div class="gx-welcome-actions">
            <a href="{{ $this->scanUrl }}" class="gx-wbtn gx-wbtn-primary">
                <x-heroicon-o-qr-code style="width: 1rem; height: 1rem;" />
                <span>{{ __('Scan Attendance') }}</span>
            </a>

            @if(auth()->user()?->isAdmin())
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
