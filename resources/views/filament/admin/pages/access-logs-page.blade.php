<x-filament-panels::page>
    <div wire:poll.20s class="gx-wrap">
        <div class="gx-toolbar">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Cari username / nama / sekolah / halaman / IP..." class="gx-search" />
            <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.45rem;">
                <select wire:model.live="roleFilter" class="gx-date-input">
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="tutor">Tutor</option>
                    <option value="school">Sekolah</option>
                    <option value="siswa">Siswa</option>
                    <option value="parent">Orang Tua</option>
                    <option value="kemenag">Kemenag</option>
                    <option value="kemendikdasmen">Kemendikdasmen</option>
                </select>
                <input type="date" wire:model.live="dateFrom" class="gx-date-input" title="Dari Tanggal" />
                <span style="font-size:0.78rem;color:#64748b;font-weight:700;">s/d</span>
                <input type="date" wire:model.live="dateUntil" class="gx-date-input" title="Sampai Tanggal" />
                <span class="gx-pill gx-pill-success">User Aktif: {{ $this->activeUsersCount }}</span>
                <span class="gx-pill gx-pill-primary">Log: {{ $this->logs->count() }}</span>
            </div>
        </div>

        <div class="gx-level-card">
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
                                <td class="gx-mono" style="color:#0a4282;font-weight:700;" class="dark:!text-sky-400">{{ $u?->username ?: ($u?->email ?: '-') }}</td>
                                <td>
                                    <div style="font-weight:700;text-transform:uppercase;">{{ $u?->name ?: 'Guest' }}</div>
                                    @if ($sub)<div style="font-size:0.72rem;color:#64748b;text-transform:uppercase;">{{ $sub }}</div>@endif
                                </td>
                                <td><span class="gx-pill {{ $roleMeta['class'] }}" style="padding:0.12rem 0.6rem;">{{ $roleMeta['label'] }}</span></td>
                                <td style="font-weight:600;">{{ $log->page ?: '-' }}</td>
                                <td class="gx-mono" style="color:#64748b;font-size:0.75rem;">{{ $log->ip ?: '-' }}</td>
                                <td class="gx-mono" style="text-align:right;font-weight:700;">{{ $log->created_at?->format('d/m H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" style="text-align:center;padding:1.5rem;color:#64748b;">Tidak ada log akses pada filter ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="gx-mobile-list">
                @forelse ($this->logs as $log)
                    @php
                        $u = $log->user;
                        $roleMeta = \App\Filament\Widgets\RecentActivityWidget::roleMeta($u?->role);
                    @endphp
                    <div class="gx-m-card">
                        <div class="gx-kv"><span class="gx-kv-k">Username</span><span class="gx-kv-v gx-mono" style="color:#0a4282;">{{ $u?->username ?: '-' }}</span></div>
                        <div class="gx-kv"><span class="gx-kv-k">Nama</span><span class="gx-kv-v" style="font-weight:700;">{{ $u?->name ?: 'Guest' }}</span></div>
                        <div class="gx-kv"><span class="gx-kv-k">Role</span><span class="gx-kv-v"><span class="gx-pill {{ $roleMeta['class'] }}" style="padding:0.08rem 0.55rem;">{{ $roleMeta['label'] }}</span></span></div>
                        <div class="gx-kv"><span class="gx-kv-k">Halaman</span><span class="gx-kv-v">{{ $log->page ?: '-' }}</span></div>
                        <div class="gx-kv"><span class="gx-kv-k">IP</span><span class="gx-kv-v gx-mono" style="font-size:0.74rem;">{{ $log->ip ?: '-' }}</span></div>
                        <div class="gx-kv" style="border-bottom:none;"><span class="gx-kv-k">Waktu</span><span class="gx-kv-v gx-mono">{{ $log->created_at?->format('d/m H:i:s') }}</span></div>
                    </div>
                @empty
                    <div style="text-align:center;padding:1rem;color:#64748b;font-size:0.84rem;">Tidak ada log akses.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>
