<x-filament-panels::page>
    @php $data = $this->groupedSchedules; @endphp
    <div class="gx-wrap">
        <div class="gx-toolbar">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Cari kelas / tutor..." class="gx-search" />
            <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.45rem;">
                <input type="date" wire:model.live="dateFrom" class="gx-date-input" title="Dari Tanggal" />
                <span style="font-size:0.78rem;color:#64748b;font-weight:700;">s/d</span>
                <input type="date" wire:model.live="dateUntil" class="gx-date-input" title="Sampai Tanggal" />
                <span class="gx-pill gx-pill-primary">{{ $data['total'] }} jadwal</span>
            </div>
        </div>

        @forelse ($data['groups'] as $className => $schedules)
            @php $isOpen = in_array($className, $openGroups, true) || trim($search) !== '' || $dateFrom || $dateUntil; @endphp
            <div class="gx-level-card">
                <button type="button" wire:click="toggleGroup(@js($className))" class="gx-level-btn">
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <span class="gx-level-tag">JADWAL</span>
                        <span>{{ $className }}</span>
                        <span class="gx-glass-pill">{{ count($schedules) }} sesi</span>
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
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Tutor</th>
                                    <th style="text-align:right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($schedules as $i => $sch)
                                    <tr wire:click="startInlineEdit({{ $sch->id }})" class="gx-row-click" title="Klik baris untuk edit jadwal">
                                        <td class="gx-mono">{{ $i + 1 }}</td>
                                        <td style="font-weight:700;color:#0a4282;" class="dark:!text-sky-300">{{ $sch->date?->format('d M Y') ?? '-' }}</td>
                                        <td class="gx-mono"><span class="gx-pill gx-pill-gold" style="padding:0.12rem 0.55rem;">{{ substr((string) $sch->start_time, 0, 5) }} - {{ substr((string) $sch->end_time, 0, 5) }}</span></td>
                                        <td>{{ $sch->user?->name ?? '-' }}</td>
                                        <td style="text-align:right;" onclick="event.stopPropagation()">
                                            <button type="button" wire:click.stop="deleteRecord({{ $sch->id }})" wire:confirm="Hapus jadwal ini?" class="gx-btn gx-btn-del">Hapus</button>
                                        </td>
                                    </tr>
                                    @if ($editingId === $sch->id)
                                        <tr>
                                            <td colspan="5">
                                                <div class="gx-edit-box">
                                                    <div class="gx-edit-grid">
                                                        <div>
                                                            <label class="gx-edit-label">Kelas</label>
                                                            <select wire:model="editForm.class_room_id" class="gx-edit-input">
                                                                @foreach ($data['classes'] as $cid => $cname)<option value="{{ $cid }}">{{ $cname }}</option>@endforeach
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="gx-edit-label">Tutor</label>
                                                            <select wire:model="editForm.user_id" class="gx-edit-input">
                                                                @foreach ($data['tutors'] as $uid => $uname)<option value="{{ $uid }}">{{ $uname }}</option>@endforeach
                                                            </select>
                                                        </div>
                                                        <div><label class="gx-edit-label">Tanggal</label><input type="date" wire:model="editForm.date" class="gx-edit-input" /></div>
                                                        <div><label class="gx-edit-label">Jam Mulai</label><input type="time" wire:model="editForm.start_time" class="gx-edit-input" /></div>
                                                        <div><label class="gx-edit-label">Jam Selesai</label><input type="time" wire:model="editForm.end_time" class="gx-edit-input" /></div>
                                                    </div>
                                                    <div style="display:flex;justify-content:flex-end;gap:0.5rem;margin-top:0.75rem;">
                                                        <button type="button" wire:click="cancelInlineEdit" class="gx-btn gx-btn-cancel">Batal</button>
                                                        <button type="button" wire:click="saveInlineEdit" class="gx-btn gx-btn-save">Simpan Perubahan</button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="gx-mobile-list">
                        @foreach ($schedules as $sch)
                            <div class="gx-m-card">
                                <div wire:click="startInlineEdit({{ $sch->id }})" class="gx-row-click">
                                    <div class="gx-kv"><span class="gx-kv-k">Tanggal</span><span class="gx-kv-v" style="color:#0a4282;font-weight:700;">{{ $sch->date?->format('d M Y') ?? '-' }}</span></div>
                                    <div class="gx-kv"><span class="gx-kv-k">Jam</span><span class="gx-kv-v gx-mono">{{ substr((string) $sch->start_time, 0, 5) }} - {{ substr((string) $sch->end_time, 0, 5) }}</span></div>
                                    <div class="gx-kv" style="border-bottom:none;"><span class="gx-kv-k">Tutor</span><span class="gx-kv-v">{{ $sch->user?->name ?? '-' }}</span></div>
                                </div>
                                <div class="gx-m-actions">
                                    <button type="button" wire:click.stop="deleteRecord({{ $sch->id }})" wire:confirm="Hapus jadwal ini?" class="gx-btn gx-btn-del">Hapus</button>
                                </div>
                                @if ($editingId === $sch->id)
                                    <div class="gx-edit-box">
                                        <div class="gx-edit-grid">
                                            <div><label class="gx-edit-label">Tanggal</label><input type="date" wire:model="editForm.date" class="gx-edit-input" /></div>
                                            <div><label class="gx-edit-label">Jam Mulai</label><input type="time" wire:model="editForm.start_time" class="gx-edit-input" /></div>
                                            <div><label class="gx-edit-label">Jam Selesai</label><input type="time" wire:model="editForm.end_time" class="gx-edit-input" /></div>
                                        </div>
                                        <div style="display:flex;justify-content:flex-end;gap:0.5rem;margin-top:0.75rem;">
                                            <button type="button" wire:click="cancelInlineEdit" class="gx-btn gx-btn-cancel">Batal</button>
                                            <button type="button" wire:click="saveInlineEdit" class="gx-btn gx-btn-save">Simpan</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="gx-toolbar" style="justify-content:center;color:#64748b;">Tidak ada jadwal pada rentang tanggal ini.</div>
        @endforelse
    </div>
</x-filament-panels::page>
