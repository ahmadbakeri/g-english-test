<x-filament-panels::page>
    @php $h = $this->hierarchy; @endphp
    <div class="gx-wrap">
        <div class="gx-toolbar">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Cari ujian / siswa / sekolah / kelas..." class="gx-search" />
            <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.45rem;">
                <input type="date" wire:model.live="dateFrom" class="gx-date-input" title="Dari Tanggal" />
                <span style="font-size:0.78rem;color:#64748b;font-weight:700;">s/d</span>
                <input type="date" wire:model.live="dateUntil" class="gx-date-input" title="Sampai Tanggal" />
                <span class="gx-pill gx-pill-primary">Total: {{ $h['total'] }}</span>
                @if ($h['locked'] > 0)
                    <span class="gx-pill gx-pill-danger">Terkunci: {{ $h['locked'] }}</span>
                @endif
            </div>
        </div>

        @foreach (['Internal', 'Eksternal'] as $type)
            @php
                $tData = $h['types'][$type];
                $isTypeOpen = in_array($type, $openTypes, true) || trim($search) !== '' || $dateFrom || $dateUntil;
            @endphp
            <div class="gx-level-card">
                <button type="button" wire:click="toggleType('{{ $type }}')" class="gx-level-btn">
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <span class="gx-level-tag">{{ strtoupper($type) }}</span>
                        <span>Exam Monitoring — Siswa {{ $type }}</span>
                        <span class="gx-glass-pill">{{ $tData['count'] }} attempt</span>
                    </div>
                    <svg class="gx-chevron {{ $isTypeOpen ? 'gx-chevron-open' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                @if ($isTypeOpen)
                    <div class="gx-level-body">
                        @forelse ($tData['schools'] as $schoolName => $classes)
                            @php
                                $sKey = $type . '::' . $schoolName;
                                $isSchoolOpen = in_array($sKey, $openSchools, true) || $type === 'Internal' || trim($search) !== '';
                                $schoolCount = collect($classes)->flatten()->count();
                            @endphp
                            <div class="gx-school-card">
                                <button type="button" wire:click="toggleSchool(@js($sKey))" class="gx-school-bar">
                                    <div style="display:flex;align-items:center;gap:0.5rem;">
                                        <span>{{ $schoolName }}</span>
                                        <span class="gx-pill gx-pill-primary" style="padding:0.12rem 0.55rem;">{{ $schoolCount }} attempt</span>
                                    </div>
                                    <svg class="gx-chevron {{ $isSchoolOpen ? 'gx-chevron-open' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                @if ($isSchoolOpen)
                                    <div class="gx-school-body">
                                        @foreach ($classes as $className => $attempts)
                                            @php
                                                $cKey = $sKey . '::' . $className;
                                                $isClassOpen = in_array($cKey, $openClasses, true) || trim($search) !== '';
                                            @endphp
                                            <div class="gx-class-card">
                                                <button type="button" wire:click="toggleClass(@js($cKey))" class="gx-class-btn">
                                                    <div style="display:flex;align-items:center;gap:0.5rem;">
                                                        <span>{{ $className }}</span>
                                                        <span class="gx-pill gx-pill-gold" style="padding:0.12rem 0.55rem;">{{ count($attempts) }} siswa</span>
                                                    </div>
                                                    <svg class="gx-chevron {{ $isClassOpen ? 'gx-chevron-open' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>

                                                @if ($isClassOpen)
                                                    <div class="gx-table-wrap">
                                                        <table class="gx-table">
                                                            <thead>
                                                                <tr>
                                                                    <th>Siswa</th>
                                                                    <th>Ujian</th>
                                                                    <th>Status / Pelanggaran</th>
                                                                    <th style="text-align:right;">Skor</th>
                                                                    <th style="text-align:right;">Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($attempts as $a)
                                                                    <tr wire:click="startInlineEdit({{ $a->id }})" class="gx-row-click" title="Klik baris untuk edit / buka kunci">
                                                                        <td style="font-weight:700;color:#0a4282;" class="dark:!text-sky-300">
                                                                            <div>{{ $a->student?->name ?? '-' }}</div>
                                                                            <div class="gx-mono" style="color:#64748b;font-weight:500;">{{ $a->student?->nis ?? '-' }}</div>
                                                                        </td>
                                                                        <td>
                                                                            <div style="font-weight:600;">{{ $a->exam?->title ?? '-' }}</div>
                                                                            <div style="font-size:0.74rem;color:#64748b;">{{ $a->start_time?->format('d M Y H:i') ?? '-' }}</div>
                                                                        </td>
                                                                        <td>
                                                                            <span class="gx-pill {{ $a->is_force_submitted ? 'gx-pill-danger' : ($a->status === 'finished' ? 'gx-pill-success' : 'gx-pill-gold') }}" style="padding:0.12rem 0.55rem;">
                                                                                {{ $a->is_force_submitted ? 'Terkunci' : $a->status }}
                                                                            </span>
                                                                            <span class="gx-mono" style="margin-left:0.3rem;font-size:0.75rem;">Pelanggaran: {{ $a->violation_count ?? 0 }}/{{ $a->max_violations ?? 3 }}</span>
                                                                        </td>
                                                                        <td style="text-align:right;font-weight:800;" class="gx-mono">{{ $a->score ?? '-' }}</td>
                                                                        <td style="text-align:right;white-space:nowrap;" onclick="event.stopPropagation()">
                                                                            <button type="button" wire:click.stop="unlockAttempt({{ $a->id }})" class="gx-btn gx-btn-edit">Buka Kunci</button>
                                                                            <button type="button" wire:click.stop="deleteRecord({{ $a->id }})" wire:confirm="Reset/hapus attempt ini?" class="gx-btn gx-btn-del">Hapus</button>
                                                                        </td>
                                                                    </tr>
                                                                    @if ($editingId === $a->id)
                                                                        <tr>
                                                                            <td colspan="5">
                                                                                <div class="gx-edit-box">
                                                                                    <div class="gx-edit-grid">
                                                                                        <div><label class="gx-edit-label">Skor</label><input type="number" step="0.01" wire:model="editForm.score" class="gx-edit-input" /></div>
                                                                                        <div>
                                                                                            <label class="gx-edit-label">Status</label>
                                                                                            <select wire:model="editForm.status" class="gx-edit-input">
                                                                                                <option value="in_progress">Sedang Mengerjakan</option>
                                                                                                <option value="finished">Selesai</option>
                                                                                                <option value="locked">Terkunci</option>
                                                                                            </select>
                                                                                        </div>
                                                                                        <div><label class="gx-edit-label">Pelanggaran</label><input type="number" wire:model="editForm.violation_count" class="gx-edit-input" /></div>
                                                                                        <div>
                                                                                            <label class="gx-edit-label">Opsi Buka Kunci</label>
                                                                                            <select wire:model="editForm.unlock_mode" class="gx-edit-input">
                                                                                                <option value="lanjut">Lanjutkan Sisa Waktu</option>
                                                                                                <option value="reset">Reset Waktu dari Awal</option>
                                                                                                <option value="tambah">Tambah Ekstra Menit</option>
                                                                                            </select>
                                                                                        </div>
                                                                                        <div><label class="gx-edit-label">Ekstra Menit</label><input type="number" wire:model="editForm.extra_minutes" class="gx-edit-input" /></div>
                                                                                    </div>
                                                                                    <div style="display:flex;flex-wrap:wrap;justify-content:flex-end;gap:0.5rem;margin-top:0.75rem;">
                                                                                        <button type="button" wire:click="cancelInlineEdit" class="gx-btn gx-btn-cancel">Batal</button>
                                                                                        <button type="button" wire:click="unlockAttempt({{ $a->id }})" class="gx-btn gx-btn-edit">Terapkan Buka Kunci</button>
                                                                                        <button type="button" wire:click="saveInlineEdit" class="gx-btn gx-btn-save">Simpan Skor/Status</button>
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
                                                        @foreach ($attempts as $a)
                                                            <div class="gx-m-card">
                                                                <div wire:click="startInlineEdit({{ $a->id }})" class="gx-row-click">
                                                                    <div class="gx-kv"><span class="gx-kv-k">Siswa</span><span class="gx-kv-v" style="color:#0a4282;font-weight:700;">{{ $a->student?->name ?? '-' }}</span></div>
                                                                    <div class="gx-kv"><span class="gx-kv-k">Ujian</span><span class="gx-kv-v">{{ $a->exam?->title ?? '-' }}</span></div>
                                                                    <div class="gx-kv"><span class="gx-kv-k">Status</span><span class="gx-kv-v">{{ $a->is_force_submitted ? 'Terkunci' : $a->status }} ({{ $a->violation_count ?? 0 }}/3)</span></div>
                                                                    <div class="gx-kv" style="border-bottom:none;"><span class="gx-kv-k">Skor</span><span class="gx-kv-v gx-mono">{{ $a->score ?? '-' }}</span></div>
                                                                </div>
                                                                <div class="gx-m-actions">
                                                                    <button type="button" wire:click.stop="unlockAttempt({{ $a->id }})" class="gx-btn gx-btn-edit">Buka Kunci</button>
                                                                    <button type="button" wire:click.stop="deleteRecord({{ $a->id }})" wire:confirm="Reset/hapus attempt ini?" class="gx-btn gx-btn-del">Hapus</button>
                                                                </div>
                                                                @if ($editingId === $a->id)
                                                                    <div class="gx-edit-box">
                                                                        <div class="gx-edit-grid">
                                                                            <div><label class="gx-edit-label">Skor</label><input type="number" step="0.01" wire:model="editForm.score" class="gx-edit-input" /></div>
                                                                            <div><label class="gx-edit-label">Opsi Unlock</label><select wire:model="editForm.unlock_mode" class="gx-edit-input"><option value="lanjut">Lanjut</option><option value="reset">Reset</option><option value="tambah">+15 Menit</option></select></div>
                                                                        </div>
                                                                        <div style="display:flex;justify-content:flex-end;gap:0.4rem;margin-top:0.6rem;">
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
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div style="font-size:0.84rem;color:#64748b;padding:0.25rem 0.5rem;">Belum ada data CBT attempt {{ strtolower($type) }}.</div>
                        @endforelse
                    </div>
                @endif
            </div>
        @endforeach
    </div>
    <x-filament-actions::modals />
</x-filament-panels::page>
