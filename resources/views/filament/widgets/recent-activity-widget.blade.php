<x-filament-widgets::widget>
    <div wire:poll.15s class="gx-level-card" style="overflow:hidden;">
        {{-- Header Bar --}}
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:0.75rem;padding:0.9rem 1.15rem;border-bottom:1px solid rgba(15,23,42,0.08);" class="dark:!border-slate-800">
            <div>
                <div style="display:flex;align-items:center;gap:0.5rem;">
                    <svg style="width:1.15rem;height:1.15rem;color:#0a4282;" class="dark:!text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 style="margin:0;font-size:1.02rem;font-weight:800;color:#0a4282;" class="dark:!text-white">
                        Aktivitas Akses Terakhir
                    </h3>
                </div>
                <div style="margin-top:0.2rem;font-size:0.8rem;color:#64748b;display:flex;align-items:center;gap:0.35rem;" class="dark:!text-slate-400">
                    <span>Total User Aktif Hari Ini:</span>
                    <span style="font-weight:800;color:#10b981;font-variant-numeric:tabular-nums;">{{ $this->todayActiveUsers }}</span>
                    <span>User</span>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <span class="gx-pill gx-pill-muted" style="font-size:0.7rem;">Live • {{ now()->format('d/m/Y H:i') }}</span>
                <a href="{{ $this->detailUrl }}" class="gx-btn gx-btn-card">
                    Detail
                </a>
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="gx-table-wrap">
            <table class="gx-table">
                <thead>
                    <tr>
                        <th style="width:3rem;">No</th>
                        <th>Username</th>
                        <th>Nama Pengguna</th>
                        <th>Role</th>
                        <th>Halaman</th>
                        <th>IP Address</th>
                        <th style="text-align:right;">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->logs as $idx => $log)
                        @php
                            $u = $log->user;
                            $roleMeta = \App\Filament\Widgets\RecentActivityWidget::roleMeta($u?->role);
                            $sub = $u?->student?->school_name ?: ($u?->student?->class_name ?: null);
                        @endphp
                        <tr>
                            <td class="gx-mono" style="color:#64748b;">{{ $idx + 1 }}</td>
                            <td class="gx-mono" style="color:#0a4282;font-weight:700;" class="dark:!text-sky-400">
                                {{ $u?->username ?: ($u?->email ?: '-') }}
                            </td>
                            <td>
                                <div style="font-weight:700;text-transform:uppercase;">{{ $u?->name ?: 'Guest' }}</div>
                                @if ($sub)
                                    <div style="font-size:0.72rem;color:#64748b;text-transform:uppercase;">{{ $sub }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="gx-pill {{ $roleMeta['class'] }}" style="padding:0.12rem 0.6rem;">
                                    {{ $roleMeta['label'] }}
                                </span>
                            </td>
                            <td style="font-weight:600;">{{ $log->page ?: '-' }}</td>
                            <td class="gx-mono" style="color:#64748b;font-size:0.75rem;">{{ $log->ip ?: '-' }}</td>
                            <td class="gx-mono" style="text-align:right;font-weight:700;">{{ $log->created_at?->format('H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:1.5rem;color:#64748b;">
                                Belum ada aktivitas akses tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Key-Value Cards --}}
        <div class="gx-mobile-list">
            @forelse ($this->logs as $idx => $log)
                @php
                    $u = $log->user;
                    $roleMeta = \App\Filament\Widgets\RecentActivityWidget::roleMeta($u?->role);
                    $sub = $u?->student?->school_name ?: ($u?->student?->class_name ?: null);
                @endphp
                <div class="gx-m-card">
                    <div class="gx-kv"><span class="gx-kv-k">Username</span><span class="gx-kv-v gx-mono" style="color:#0a4282;">{{ $u?->username ?: ($u?->email ?: '-') }}</span></div>
                    <div class="gx-kv"><span class="gx-kv-k">Pengguna</span><span class="gx-kv-v" style="font-weight:700;">{{ $u?->name ?: 'Guest' }}@if($sub) ({{ $sub }})@endif</span></div>
                    <div class="gx-kv"><span class="gx-kv-k">Role</span><span class="gx-kv-v"><span class="gx-pill {{ $roleMeta['class'] }}" style="padding:0.08rem 0.55rem;">{{ $roleMeta['label'] }}</span></span></div>
                    <div class="gx-kv"><span class="gx-kv-k">Halaman</span><span class="gx-kv-v">{{ $log->page ?: '-' }}</span></div>
                    <div class="gx-kv"><span class="gx-kv-k">IP Address</span><span class="gx-kv-v gx-mono" style="font-size:0.74rem;">{{ $log->ip ?: '-' }}</span></div>
                    <div class="gx-kv" style="border-bottom:none;"><span class="gx-kv-k">Waktu</span><span class="gx-kv-v gx-mono">{{ $log->created_at?->format('H:i:s') }}</span></div>
                </div>
            @empty
                <div style="text-align:center;padding:1rem;color:#64748b;font-size:0.84rem;">Belum ada aktivitas akses tercatat.</div>
            @endforelse
        </div>
    </div>
</x-filament-widgets::widget>
