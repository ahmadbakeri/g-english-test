<x-filament-panels::page>
    @php $data = $this->groupedClasses; @endphp
    <div class="gx-wrap">
        <div class="gx-toolbar">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Cari nama kelas / mata pelajaran / tutor / jadwal..." class="gx-search" />
            <div class="gx-badges">
                <span class="gx-pill gx-pill-primary">Total Kelas: {{ $data['total'] }}</span>
            </div>
        </div>

        @forelse ($data['groups'] as $yearLabel => $classes)
            @php $isOpen = in_array($yearLabel, $openGroups, true) || trim($search) !== ''; @endphp
            <div class="gx-level-card">
                <button type="button" wire:click="toggleGroup(@js($yearLabel))" class="gx-level-btn">
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <span class="gx-level-tag">TA</span>
                        <span>Tahun Ajaran {{ $yearLabel }}</span>
                        <span class="gx-glass-pill">{{ count($classes) }} kelas</span>
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
                                    <th>Nama Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Wali Kelas / Tutor</th>
                                    <th>Siswa / Kapasitas</th>
                                    <th>Status</th>
                                    <th style="text-align:right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($classes as $i => $cls)
                                    <tr wire:click="startInlineEdit({{ $cls->id }})" class="gx-row-click" title="Klik baris untuk edit kelas">
                                        <td class="gx-mono">{{ $i + 1 }}</td>
                                        <td style="font-weight:700;color:#0a4282;" class="dark:!text-sky-300">
                                            <div>{{ $cls->name }}</div>
                                            <div style="font-size:0.74rem;color:#64748b;font-weight:500;">{{ $cls->schedule ?: '-' }}</div>
                                        </td>
                                        <td><span class="gx-pill gx-pill-primary" style="padding:0.12rem 0.55rem;">{{ $cls->subject?->name ?? '-' }}</span></td>
                                        <td>{{ $cls->tutor?->name ?? '-' }}</td>
                                        <td class="gx-mono"><span class="gx-pill gx-pill-gold" style="padding:0.12rem 0.55rem;">{{ $cls->students_count }} / {{ $cls->capacity }}</span></td>
                                        <td>
                                            <span class="gx-pill {{ $cls->is_active ? 'gx-pill-success' : 'gx-pill-danger' }}" style="padding:0.12rem 0.55rem;">
                                                {{ $cls->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td style="text-align:right;" onclick="event.stopPropagation()">
                                            <button type="button" wire:click.stop="deleteRecord({{ $cls->id }})" wire:confirm="Hapus kelas {{ $cls->name }}?" class="gx-btn gx-btn-del">Hapus</button>
                                        </td>
                                    </tr>
                                    @if ($editingId === $cls->id)
                                        <tr>
                                            <td colspan="7">
                                                <div class="gx-edit-box">
                                                    <div class="gx-edit-grid">
                                                        <div><label class="gx-edit-label">Nama Kelas</label><input type="text" wire:model="editForm.name" class="gx-edit-input" /></div>
                                                        <div>
                                                            <label class="gx-edit-label">Mata Pelajaran</label>
                                                            <select wire:model="editForm.subject_id" class="gx-edit-input">
                                                                <option value="">-- Pilih --</option>
                                                                @foreach ($data['subjects'] as $sid => $sname)<option value="{{ $sid }}">{{ $sname }}</option>@endforeach
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="gx-edit-label">Wali Kelas / Tutor</label>
                                                            <select wire:model="editForm.tutor_id" class="gx-edit-input">
                                                                <option value="">-- Pilih --</option>
                                                                @foreach ($data['tutors'] as $tid => $tname)<option value="{{ $tid }}">{{ $tname }}</option>@endforeach
                                                            </select>
                                                        </div>
                                                        <div><label class="gx-edit-label">Jadwal Ringkas</label><input type="text" wire:model="editForm.schedule" class="gx-edit-input" /></div>
                                                        <div><label class="gx-edit-label">Kapasitas</label><input type="number" wire:model="editForm.capacity" class="gx-edit-input" /></div>
                                                        <div style="display:flex;align-items:flex-end;">
                                                            <label style="display:inline-flex;align-items:center;gap:0.4rem;cursor:pointer;padding:0.4rem 0;font-weight:700;font-size:0.82rem;">
                                                                <input type="checkbox" wire:model="editForm.is_active" /><span>Aktif</span>
                                                            </label>
                                                        </div>
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
                        @foreach ($classes as $cls)
                            <div class="gx-m-card">
                                <div wire:click="startInlineEdit({{ $cls->id }})" class="gx-row-click">
                                    <div class="gx-kv"><span class="gx-kv-k">Kelas</span><span class="gx-kv-v" style="color:#0a4282;font-weight:700;">{{ $cls->name }}</span></div>
                                    <div class="gx-kv"><span class="gx-kv-k">Mapel</span><span class="gx-kv-v">{{ $cls->subject?->name ?? '-' }}</span></div>
                                    <div class="gx-kv"><span class="gx-kv-k">Tutor</span><span class="gx-kv-v">{{ $cls->tutor?->name ?? '-' }}</span></div>
                                    <div class="gx-kv" style="border-bottom:none;"><span class="gx-kv-k">Siswa</span><span class="gx-kv-v gx-mono">{{ $cls->students_count }} / {{ $cls->capacity }}</span></div>
                                </div>
                                <div class="gx-m-actions">
                                    <button type="button" wire:click.stop="deleteRecord({{ $cls->id }})" wire:confirm="Hapus kelas {{ $cls->name }}?" class="gx-btn gx-btn-del">Hapus</button>
                                </div>
                                @if ($editingId === $cls->id)
                                    <div class="gx-edit-box">
                                        <div class="gx-edit-grid">
                                            <div><label class="gx-edit-label">Nama Kelas</label><input type="text" wire:model="editForm.name" class="gx-edit-input" /></div>
                                            <div><label class="gx-edit-label">Jadwal</label><input type="text" wire:model="editForm.schedule" class="gx-edit-input" /></div>
                                            <div><label class="gx-edit-label">Kapasitas</label><input type="number" wire:model="editForm.capacity" class="gx-edit-input" /></div>
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
            <div class="gx-toolbar" style="justify-content:center;color:#64748b;">Tidak ada data kelas.</div>
        @endforelse
    </div>
</x-filament-panels::page>
