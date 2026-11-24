<x-filament-panels::page>
    <div class="gx-wrap">
        <div class="gx-toolbar">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Cari nama / NIS / kelas / HP / orang tua..." class="gx-search" />
            <div class="gx-badges">
                <span class="gx-pill gx-pill-primary">Total Siswa: {{ $this->groupedStudents['total'] }}</span>
                <span class="gx-pill gx-pill-gold">Aktif: {{ $this->groupedStudents['active'] }}</span>
            </div>
        </div>

        @forelse ($this->groupedStudents['groups'] as $className => $students)
            @php $isOpen = in_array($className, $openGroups, true) || trim($search) !== ''; @endphp
            <div class="gx-level-card">
                <button type="button" wire:click="toggleGroup(@js($className))" class="gx-level-btn">
                    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.5rem;">
                        <span class="gx-level-tag">KELAS</span>
                        <span>{{ $className }}</span>
                        <span class="gx-glass-pill">{{ count($students) }} siswa</span>
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
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>No. HP</th>
                                    <th>Orang Tua / WA</th>
                                    <th>Status</th>
                                    <th style="text-align:right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $i => $student)
                                    <tr wire:click="startInlineEdit({{ $student->id }})" class="gx-row-click" title="Klik baris untuk edit siswa">
                                        <td class="gx-mono">{{ $i + 1 }}</td>
                                        <td class="gx-mono">{{ $student->nis ?: '-' }}</td>
                                        <td style="font-weight:700;color:#0a4282;" class="dark:!text-sky-300">
                                            {{ $student->name }}
                                            <span class="gx-pill gx-pill-muted" style="padding:0.08rem 0.45rem;margin-left:0.25rem;">{{ $student->gender ?: '-' }}</span>
                                        </td>
                                        <td class="gx-mono">{{ $student->phone ?: '-' }}</td>
                                        <td>
                                            <div>{{ $student->father_name ?: ($student->mother_name ?: '-') }}</div>
                                            <div class="gx-mono" style="color:#10b981;font-weight:600;">{{ $student->father_phone ?: ($student->mother_phone ?: '-') }}</div>
                                        </td>
                                        <td>
                                            <span class="gx-pill {{ $student->is_active ? 'gx-pill-success' : 'gx-pill-danger' }}" style="padding:0.12rem 0.55rem;">
                                                {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td style="text-align:right;white-space:nowrap;" onclick="event.stopPropagation()">
                                            <a href="{{ route('report.certificate', $student->id) }}" target="_blank" class="gx-btn gx-btn-card">Sertifikat</a>
                                            <button type="button" wire:click.stop="deleteRecord({{ $student->id }})" wire:confirm="Hapus siswa {{ $student->name }}?" class="gx-btn gx-btn-del">Hapus</button>
                                        </td>
                                    </tr>
                                    @if ($editingId === $student->id)
                                        <tr>
                                            <td colspan="7">
                                                @include('filament.admin.pages.partials.internal-student-inline-edit')
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="gx-mobile-list">
                        @foreach ($students as $i => $student)
                            <div class="gx-m-card">
                                <div wire:click="startInlineEdit({{ $student->id }})" class="gx-row-click">
                                    <div class="gx-kv"><span class="gx-kv-k">No</span><span class="gx-kv-v gx-mono">{{ $i + 1 }}</span></div>
                                    <div class="gx-kv"><span class="gx-kv-k">NIS</span><span class="gx-kv-v gx-mono">{{ $student->nis ?: '-' }}</span></div>
                                    <div class="gx-kv"><span class="gx-kv-k">Nama</span><span class="gx-kv-v" style="color:#0a4282;font-weight:700;">{{ $student->name }} ({{ $student->gender ?: '-' }})</span></div>
                                    <div class="gx-kv"><span class="gx-kv-k">No. HP</span><span class="gx-kv-v gx-mono">{{ $student->phone ?: '-' }}</span></div>
                                    <div class="gx-kv"><span class="gx-kv-k">Orang Tua</span><span class="gx-kv-v">{{ $student->father_name ?: ($student->mother_name ?: '-') }}</span></div>
                                    <div class="gx-kv" style="border-bottom:none;"><span class="gx-kv-k">WA Ortu</span><span class="gx-kv-v gx-mono" style="color:#10b981;">{{ $student->father_phone ?: ($student->mother_phone ?: '-') }}</span></div>
                                </div>
                                <div class="gx-m-actions">
                                    <a href="{{ route('report.certificate', $student->id) }}" target="_blank" class="gx-btn gx-btn-card">Sertifikat</a>
                                    <button type="button" wire:click.stop="deleteRecord({{ $student->id }})" wire:confirm="Hapus siswa {{ $student->name }}?" class="gx-btn gx-btn-del">Hapus</button>
                                </div>
                                @if ($editingId === $student->id)
                                    @include('filament.admin.pages.partials.internal-student-inline-edit')
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="gx-toolbar" style="justify-content:center;color:#64748b;">Tidak ada data siswa internal.</div>
        @endforelse
    </div>
</x-filament-panels::page>
