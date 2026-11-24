<x-filament-panels::page>
    <div class="gx-wrap">
        <div class="gx-toolbar">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Cari nama tutor / username / ID buatan (NIP) / email / HP..." class="gx-search" />
            <div class="gx-badges">
                <span class="gx-pill gx-pill-primary">Total Tutor: {{ $this->groupedTutors['total'] }}</span>
                <span class="gx-pill gx-pill-gold">Aktif: {{ $this->groupedTutors['active'] }}</span>
            </div>
        </div>

        @forelse ($this->groupedTutors['groups'] as $gradeLabel => $tutors)
            @php $isOpen = in_array($gradeLabel, $openGroups, true) || trim($search) !== ''; @endphp
            <div class="gx-level-card">
                <button type="button" wire:click="toggleGroup(@js($gradeLabel))" class="gx-level-btn">
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <span class="gx-level-tag">{{ strtoupper($gradeLabel) }}</span>
                        <span>Tutor {{ $gradeLabel }}</span>
                        <span class="gx-glass-pill">{{ count($tutors) }} tutor</span>
                    </div>
                    <svg class="gx-chevron {{ $isOpen ? 'gx-chevron-open' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                @if ($isOpen)
                    <div class="gx-table-wrap">
                        <table class="gx-table">
                            <thead>
                                <tr>
                                    <th style="width:2.8rem;">No</th>
                                    <th>Tutor</th>
                                    <th>ID Buatan / NIP</th>
                                    <th>Username &amp; Email Login</th>
                                    <th>Spesialisasi</th>
                                    <th>No. HP</th>
                                    <th>Status</th>
                                    <th style="text-align:right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tutors as $i => $tutor)
                                    @php
                                        $tutorPhoto = $tutor->photo
                                            ? \Illuminate\Support\Facades\Storage::disk('public')->url($tutor->photo)
                                            : $tutor->user?->photo_url;
                                        $tutorInitials = mb_strtoupper(mb_substr(trim($tutor->name), 0, 2));
                                    @endphp
                                    <tr wire:click="startInlineEdit({{ $tutor->id }})" class="gx-row-click" title="Klik baris untuk edit tutor &amp; akun login">
                                        <td class="gx-mono">{{ $i + 1 }}</td>
                                        <td>
                                            <div style="display:flex;align-items:center;gap:0.65rem;">
                                                @if ($tutorPhoto)
                                                    <img src="{{ $tutorPhoto }}" alt="{{ $tutor->name }}" class="gx-avatar" />
                                                @else
                                                    <span class="gx-avatar">{{ $tutorInitials }}</span>
                                                @endif
                                                <div style="font-weight:700;color:#0a4282;" class="dark:!text-sky-300">{{ $tutor->name }}</div>
                                            </div>
                                        </td>
                                        <td class="gx-mono" style="font-weight:700;">{{ $tutor->nip ?: '-' }}</td>
                                        <td>
                                            <div class="gx-mono" style="font-weight:700;color:#0a4282;" class="dark:!text-amber-300">{{ $tutor->user?->username ? '@' . $tutor->user->username : '-' }}</div>
                                            <div style="font-size:0.74rem;color:#64748b;">{{ $tutor->user?->email ?? '-' }}</div>
                                        </td>
                                        <td><span class="gx-pill gx-pill-primary" style="padding:0.12rem 0.55rem;">{{ $tutor->specialization ?: '-' }}</span></td>
                                        <td class="gx-mono">{{ $tutor->phone ?: '-' }}</td>
                                        <td>
                                            <span class="gx-pill {{ $tutor->is_active ? 'gx-pill-success' : 'gx-pill-danger' }}" style="padding:0.12rem 0.55rem;">
                                                {{ $tutor->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td style="text-align:right;" onclick="event.stopPropagation()">
                                            <button type="button" wire:click.stop="deleteRecord({{ $tutor->id }})" wire:confirm="Hapus tutor {{ $tutor->name }}?" class="gx-btn gx-btn-del">Hapus</button>
                                        </td>
                                    </tr>
                                    @if ($editingId === $tutor->id)
                                        <tr>
                                            <td colspan="8">
                                                @include('filament.admin.pages.partials.tutor-inline-edit', ['tutor' => $tutor])
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="gx-mobile-list">
                        @foreach ($tutors as $i => $tutor)
                            <div class="gx-m-card">
                                <div wire:click="startInlineEdit({{ $tutor->id }})" class="gx-row-click">
                                    <div class="gx-kv"><span class="gx-kv-k">Nama</span><span class="gx-kv-v" style="color:#0a4282;font-weight:700;">{{ $tutor->name }}</span></div>
                                    <div class="gx-kv"><span class="gx-kv-k">ID Buatan / NIP</span><span class="gx-kv-v gx-mono">{{ $tutor->nip ?: '-' }}</span></div>
                                    <div class="gx-kv"><span class="gx-kv-k">Username</span><span class="gx-kv-v gx-mono">{{ $tutor->user?->username ?: '-' }}</span></div>
                                    <div class="gx-kv"><span class="gx-kv-k">Email</span><span class="gx-kv-v">{{ $tutor->user?->email ?: '-' }}</span></div>
                                    <div class="gx-kv"><span class="gx-kv-k">Spesialisasi</span><span class="gx-kv-v">{{ $tutor->specialization ?: '-' }}</span></div>
                                    <div class="gx-kv" style="border-bottom:none;"><span class="gx-kv-k">No. HP</span><span class="gx-kv-v gx-mono">{{ $tutor->phone ?: '-' }}</span></div>
                                </div>
                                <div class="gx-m-actions">
                                    <button type="button" wire:click.stop="deleteRecord({{ $tutor->id }})" wire:confirm="Hapus tutor {{ $tutor->name }}?" class="gx-btn gx-btn-del">Hapus</button>
                                </div>
                                @if ($editingId === $tutor->id)
                                    @include('filament.admin.pages.partials.tutor-inline-edit', ['tutor' => $tutor])
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="gx-toolbar" style="justify-content:center;color:#64748b;">Tidak ada data tutor.</div>
        @endforelse
    </div>
</x-filament-panels::page>
