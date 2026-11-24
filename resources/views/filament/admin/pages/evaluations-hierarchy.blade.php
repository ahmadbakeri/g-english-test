<x-filament-panels::page>
    @php $h = $this->hierarchy; @endphp
    <div class="gx-wrap">
        <div class="gx-toolbar">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Cari siswa / sekolah / kelas / mata pelajaran..." class="gx-search" />
            <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.45rem;">
                <input type="date" wire:model.live="dateFrom" class="gx-date-input" title="Dari Tanggal" />
                <span style="font-size:0.78rem;color:#64748b;font-weight:700;">s/d</span>
                <input type="date" wire:model.live="dateUntil" class="gx-date-input" title="Sampai Tanggal" />
                <span class="gx-pill gx-pill-primary">Total: {{ $h['total'] }}</span>
                <span class="gx-pill gx-pill-gold">Rata-rata: {{ $h['avg'] }}</span>
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
                        <span>Evaluasi — Siswa {{ $type }}</span>
                        <span class="gx-glass-pill">{{ $tData['count'] }} nilai</span>
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
                                        <span class="gx-pill gx-pill-primary" style="padding:0.12rem 0.55rem;">{{ $schoolCount }} nilai</span>
                                    </div>
                                    <svg class="gx-chevron {{ $isSchoolOpen ? 'gx-chevron-open' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                @if ($isSchoolOpen)
                                    <div class="gx-school-body">
                                        @foreach ($classes as $className => $items)
                                            @php
                                                $cKey = $sKey . '::' . $className;
                                                $isClassOpen = in_array($cKey, $openClasses, true) || trim($search) !== '';
                                            @endphp
                                            <div class="gx-class-card">
                                                <button type="button" wire:click="toggleClass(@js($cKey))" class="gx-class-btn">
                                                    <div style="display:flex;align-items:center;gap:0.5rem;">
                                                        <span>{{ $className }}</span>
                                                        <span class="gx-pill gx-pill-gold" style="padding:0.12rem 0.55rem;">{{ count($items) }} data</span>
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
                                                                    <th>Mata Pelajaran</th>
                                                                    <th>Tanggal</th>
                                                                    <th style="text-align:right;">Skor</th>
                                                                    <th style="text-align:right;">Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($items as $ev)
                                                                    <tr wire:click="startInlineEdit({{ $ev->id }})" class="gx-row-click" title="Klik baris untuk edit nilai">
                                                                        <td style="font-weight:700;color:#0a4282;" class="dark:!text-sky-300">
                                                                            <div>{{ $ev->student?->name ?? '-' }}</div>
                                                                            <div style="font-size:0.74rem;color:#64748b;font-weight:500;">{{ $ev->notes ?: '-' }}</div>
                                                                        </td>
                                                                        <td><span class="gx-pill gx-pill-primary" style="padding:0.12rem 0.55rem;">{{ $ev->subject?->name ?? '-' }}</span></td>
                                                                        <td class="gx-mono">{{ $ev->date ? \Carbon\Carbon::parse($ev->date)->format('d M Y') : '-' }}</td>
                                                                        <td style="text-align:right;" class="gx-mono"><span class="gx-pill gx-pill-success" style="padding:0.15rem 0.6rem;">{{ $ev->score }}</span></td>
                                                                        <td style="text-align:right;white-space:nowrap;" onclick="event.stopPropagation()">
                                                                            <a href="{{ route('report.evaluation', $ev->id) }}" target="_blank" class="gx-btn gx-btn-card">Rapor</a>
                                                                            <button type="button" wire:click.stop="deleteRecord({{ $ev->id }})" wire:confirm="Hapus nilai ini?" class="gx-btn gx-btn-del">Hapus</button>
                                                                        </td>
                                                                    </tr>
                                                                    @if ($editingId === $ev->id)
                                                                        <tr>
                                                                            <td colspan="5">
                                                                                <div class="gx-edit-box">
                                                                                    <div class="gx-edit-grid">
                                                                                        <div><label class="gx-edit-label">Skor (0-100)</label><input type="number" min="0" max="100" wire:model="editForm.score" class="gx-edit-input" /></div>
                                                                                        <div><label class="gx-edit-label">Tanggal</label><input type="date" wire:model="editForm.date" class="gx-edit-input" /></div>
                                                                                        <div><label class="gx-edit-label">Catatan</label><input type="text" wire:model="editForm.notes" class="gx-edit-input" /></div>
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
                                                        @foreach ($items as $ev)
                                                            <div class="gx-m-card">
                                                                <div wire:click="startInlineEdit({{ $ev->id }})" class="gx-row-click">
                                                                    <div class="gx-kv"><span class="gx-kv-k">Siswa</span><span class="gx-kv-v" style="color:#0a4282;font-weight:700;">{{ $ev->student?->name ?? '-' }}</span></div>
                                                                    <div class="gx-kv"><span class="gx-kv-k">Mapel</span><span class="gx-kv-v">{{ $ev->subject?->name ?? '-' }}</span></div>
                                                                    <div class="gx-kv"><span class="gx-kv-k">Tanggal</span><span class="gx-kv-v gx-mono">{{ $ev->date ? \Carbon\Carbon::parse($ev->date)->format('d M Y') : '-' }}</span></div>
                                                                    <div class="gx-kv" style="border-bottom:none;"><span class="gx-kv-k">Skor</span><span class="gx-kv-v gx-mono" style="color:#10b981;font-weight:800;">{{ $ev->score }}</span></div>
                                                                </div>
                                                                <div class="gx-m-actions">
                                                                    <a href="{{ route('report.evaluation', $ev->id) }}" target="_blank" class="gx-btn gx-btn-card">Rapor</a>
                                                                    <button type="button" wire:click.stop="deleteRecord({{ $ev->id }})" wire:confirm="Hapus nilai ini?" class="gx-btn gx-btn-del">Hapus</button>
                                                                </div>
                                                                @if ($editingId === $ev->id)
                                                                    <div class="gx-edit-box">
                                                                        <div class="gx-edit-grid">
                                                                            <div><label class="gx-edit-label">Skor</label><input type="number" wire:model="editForm.score" class="gx-edit-input" /></div>
                                                                            <div><label class="gx-edit-label">Catatan</label><input type="text" wire:model="editForm.notes" class="gx-edit-input" /></div>
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
                            <div style="font-size:0.84rem;color:#64748b;padding:0.25rem 0.5rem;">Belum ada evaluasi siswa {{ strtolower($type) }}.</div>
                        @endforelse
                    </div>
                @endif
            </div>
        @endforeach
    </div>
    <x-filament-actions::modals />
</x-filament-panels::page>
