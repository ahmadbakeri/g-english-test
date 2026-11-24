<x-filament-panels::page>
    <div class="gx-wrap">
        <div class="gx-toolbar">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Cari nama tutor / NIP / spesialisasi / HP..." class="gx-search" />
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
                                    <th>No</th>
                                    <th>NIP</th>
                                    <th>Nama Tutor</th>
                                    <th>Spesialisasi</th>
                                    <th>No. HP</th>
                                    <th>Status</th>
                                    <th style="text-align:right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tutors as $i => $tutor)
                                    <tr wire:click="startInlineEdit({{ $tutor->id }})" class="gx-row-click" title="Klik baris untuk edit tutor">
                                        <td class="gx-mono">{{ $i + 1 }}</td>
                                        <td class="gx-mono">{{ $tutor->nip ?: '-' }}</td>
                                        <td style="font-weight:700;color:#0a4282;" class="dark:!text-sky-300">
                                            <div>{{ $tutor->name }}</div>
                                            <div style="font-size:0.74rem;color:#64748b;font-weight:500;">{{ $tutor->user?->email ?? '-' }}</div>
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
                                            <td colspan="7">
                                                <div class="gx-edit-box">
                                                    <div class="gx-edit-grid">
                                                        <div><label class="gx-edit-label">Nama Tutor</label><input type="text" wire:model="editForm.name" class="gx-edit-input" /></div>
                                                        <div><label class="gx-edit-label">NIP</label><input type="text" wire:model="editForm.nip" class="gx-edit-input" /></div>
                                                        <div><label class="gx-edit-label">Spesialisasi</label><input type="text" wire:model="editForm.specialization" class="gx-edit-input" /></div>
                                                        <div>
                                                            <label class="gx-edit-label">Grade</label>
                                                            <select wire:model="editForm.grade" class="gx-edit-input">
                                                                <option value="A">Grade A (Rp 50.000/JP)</option>
                                                                <option value="B">Grade B (Rp 35.000/JP)</option>
                                                                <option value="C">Grade C (Rp 25.000/JP)</option>
                                                            </select>
                                                        </div>
                                                        <div><label class="gx-edit-label">No. HP</label><input type="text" wire:model="editForm.phone" class="gx-edit-input" /></div>
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
                        @foreach ($tutors as $i => $tutor)
                            <div class="gx-m-card">
                                <div wire:click="startInlineEdit({{ $tutor->id }})" class="gx-row-click">
                                    <div class="gx-kv"><span class="gx-kv-k">NIP</span><span class="gx-kv-v gx-mono">{{ $tutor->nip ?: '-' }}</span></div>
                                    <div class="gx-kv"><span class="gx-kv-k">Nama</span><span class="gx-kv-v" style="color:#0a4282;font-weight:700;">{{ $tutor->name }}</span></div>
                                    <div class="gx-kv"><span class="gx-kv-k">Spesialisasi</span><span class="gx-kv-v">{{ $tutor->specialization ?: '-' }}</span></div>
                                    <div class="gx-kv" style="border-bottom:none;"><span class="gx-kv-k">No. HP</span><span class="gx-kv-v gx-mono">{{ $tutor->phone ?: '-' }}</span></div>
                                </div>
                                <div class="gx-m-actions">
                                    <button type="button" wire:click.stop="deleteRecord({{ $tutor->id }})" wire:confirm="Hapus tutor {{ $tutor->name }}?" class="gx-btn gx-btn-del">Hapus</button>
                                </div>
                                @if ($editingId === $tutor->id)
                                    <div class="gx-edit-box">
                                        <div class="gx-edit-grid">
                                            <div><label class="gx-edit-label">Nama Tutor</label><input type="text" wire:model="editForm.name" class="gx-edit-input" /></div>
                                            <div><label class="gx-edit-label">NIP</label><input type="text" wire:model="editForm.nip" class="gx-edit-input" /></div>
                                            <div><label class="gx-edit-label">Spesialisasi</label><input type="text" wire:model="editForm.specialization" class="gx-edit-input" /></div>
                                            <div>
                                                <label class="gx-edit-label">Grade</label>
                                                <select wire:model="editForm.grade" class="gx-edit-input">
                                                    <option value="A">Grade A</option><option value="B">Grade B</option><option value="C">Grade C</option>
                                                </select>
                                            </div>
                                            <div><label class="gx-edit-label">No. HP</label><input type="text" wire:model="editForm.phone" class="gx-edit-input" /></div>
                                            <label style="display:inline-flex;align-items:center;gap:0.4rem;font-weight:700;font-size:0.82rem;"><input type="checkbox" wire:model="editForm.is_active" /><span>Aktif</span></label>
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
            <div class="gx-toolbar" style="justify-content:center;color:#64748b;">Tidak ada data tutor.</div>
        @endforelse
    </div>
</x-filament-panels::page>
