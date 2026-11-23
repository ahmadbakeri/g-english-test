<x-filament-panels::page>
    <style>
        .gx-wrap { display: flex; flex-direction: column; gap: 1rem; }
        .gx-toolbar {
            display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem;
            background: #ffffff; border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 1rem;
            padding: 0.85rem 1rem; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        }
        .dark .gx-toolbar { background: #0f172a; border-color: rgba(255, 255, 255, 0.1); }
        .gx-search {
            flex: 1 1 260px; min-width: 200px; padding: 0.55rem 0.9rem; border-radius: 0.75rem;
            border: 1px solid #cbd5e1; background: #f8fafc; color: #0f172a; font-size: 0.875rem;
        }
        .dark .gx-search { background: #1e293b; border-color: #334155; color: #f8fafc; }
        .gx-badges { display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem; }
        .gx-pill {
            display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem;
            border-radius: 9999px; font-size: 0.75rem; font-weight: 700;
        }
        .gx-pill-blue { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
        .gx-pill-amber { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .gx-level-card {
            border-radius: 1rem; overflow: hidden; border: 1px solid rgba(15, 23, 42, 0.08);
            background: #ffffff; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }
        .dark .gx-level-card { background: #0f172a; border-color: rgba(255, 255, 255, 0.1); }
        .gx-level-btn {
            width: 100%; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
            gap: 0.5rem; padding: 0.8rem 1rem; color: #ffffff; font-weight: 700; font-size: 0.95rem;
            border: none; cursor: pointer; text-align: left;
        }
        .gx-lvl-sd { background: linear-gradient(90deg, #dc2626, #e11d48); }
        .gx-lvl-mi { background: linear-gradient(90deg, #059669, #0d9488); }
        .gx-lvl-smp { background: linear-gradient(90deg, #1d4ed8, #4338ca); }
        .gx-lvl-mts { background: linear-gradient(90deg, #d97706, #ea580c); }
        .gx-glass-pill {
            display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 9999px;
            background: rgba(255, 255, 255, 0.22); font-size: 0.75rem; font-weight: 600; color: #ffffff;
        }
        .gx-toggle-pill {
            padding: 0.25rem 0.65rem; border-radius: 0.5rem; background: rgba(0, 0, 0, 0.18);
            font-size: 0.75rem; font-weight: 600; color: #ffffff;
        }
        .gx-level-body { padding: 0.85rem; display: flex; flex-direction: column; gap: 0.75rem; background: #f8fafc; }
        .dark .gx-level-body { background: #020617; }
        .gx-school-card {
            border-radius: 0.85rem; border: 1px solid #e2e8f0; background: #ffffff; overflow: hidden;
        }
        .dark .gx-school-card { background: #0f172a; border-color: #1e293b; }
        .gx-school-bar {
            width: 100%; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
            gap: 0.5rem; padding: 0.65rem 0.9rem; background: linear-gradient(90deg, #0a4282, #1e3a8a);
            color: #ffffff; border: none; cursor: pointer; text-align: left;
        }
        .gx-school-body { padding: 0.75rem; display: flex; flex-direction: column; gap: 0.65rem; }
        .gx-class-card { border-radius: 0.75rem; border: 1px solid #e2e8f0; overflow: hidden; }
        .dark .gx-class-card { border-color: #334155; }
        .gx-class-btn {
            width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;
            padding: 0.55rem 0.85rem; background: #eff6ff; color: #0a4282; font-weight: 700;
            font-size: 0.85rem; border: none; cursor: pointer; text-align: left;
        }
        .dark .gx-class-btn { background: #1e293b; color: #93c5fd; }
        .gx-table-wrap { overflow-x: auto; }
        .gx-table { width: 100%; border-collapse: collapse; font-size: 0.82rem; text-align: left; }
        .gx-table th {
            background: #f1f5f9; color: #475569; font-weight: 700; text-transform: uppercase;
            font-size: 0.7rem; padding: 0.55rem 0.65rem; border-bottom: 1px solid #e2e8f0;
        }
        .dark .gx-table th { background: #1e293b; color: #94a3b8; border-bottom-color: #334155; }
        .gx-table td { padding: 0.55rem 0.65rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .dark .gx-table td { border-bottom-color: #1e293b; }
        .gx-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem;
            padding: 0.3rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;
            border: none; cursor: pointer; text-decoration: none; color: #ffffff;
        }
        .gx-btn-edit { background: #f59e0b; }
        .gx-btn-card { background: #0a4282; }
        .gx-btn-del { background: #e11d48; }
        .gx-btn-save { background: #059669; padding: 0.45rem 1rem; font-size: 0.8rem; }
        .gx-btn-cancel { background: #64748b; padding: 0.45rem 0.9rem; font-size: 0.8rem; }
        .gx-mobile-list { display: none; padding: 0.65rem; flex-direction: column; gap: 0.65rem; background: #f8fafc; }
        .dark .gx-mobile-list { background: #020617; }
        .gx-m-card {
            background: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.85rem; padding: 0.75rem;
            font-size: 0.8rem;
        }
        .dark .gx-m-card { background: #0f172a; border-color: #334155; }
        .gx-kv {
            display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;
            padding: 0.35rem 0; border-bottom: 1px dashed rgba(15, 23, 42, 0.08);
        }
        .dark .gx-kv { border-bottom-color: rgba(255, 255, 255, 0.08); }
        .gx-kv-k { color: #64748b; font-weight: 600; font-size: 0.75rem; }
        .gx-kv-v { font-weight: 600; text-align: right; }
        .gx-m-actions { display: flex; justify-content: flex-end; gap: 0.4rem; padding-top: 0.55rem; margin-top: 0.25rem; }
        .gx-edit-box {
            background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 0.75rem; padding: 0.85rem; margin-top: 0.5rem;
        }
        .dark .gx-edit-box { background: #1e293b; border-color: #475569; }
        .gx-edit-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.65rem; }
        .gx-edit-label { display: block; font-size: 0.72rem; font-weight: 700; color: #475569; margin-bottom: 0.2rem; }
        .dark .gx-edit-label { color: #cbd5e1; }
        .gx-edit-input {
            width: 100%; padding: 0.42rem 0.65rem; border-radius: 0.5rem; border: 1px solid #cbd5e1;
            background: #ffffff; color: #0f172a; font-size: 0.82rem;
        }
        .dark .gx-edit-input { background: #0f172a; border-color: #475569; color: #f8fafc; }
        @media (max-width: 767px) {
            .gx-table-wrap { display: none; }
            .gx-mobile-list { display: flex; }
            .gx-edit-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="gx-wrap">
        {{-- Search & Summary Bar --}}
        <div class="gx-toolbar">
            <input
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari username / nama / NISN / kelas / sekolah..."
                class="gx-search"
            />
            <div class="gx-badges">
                <span class="gx-pill gx-pill-blue">
                    Total Siswa Eksternal: {{ $this->hierarchy['totalStudents'] }}
                </span>
                <span class="gx-pill gx-pill-amber">
                    {{ $this->hierarchy['totalSchools'] }} Sekolah
                </span>
            </div>
        </div>

        {{-- Level 1: Jenjang (SD, MI, SMP, MTs) --}}
        @foreach (['SD', 'MI', 'SMP', 'MTs'] as $level)
            @php
                $levelData = $this->hierarchy['levels'][$level] ?? ['schools' => [], 'schoolCount' => 0, 'studentCount' => 0];
                $isLevelOpen = in_array($level, $openLevels, true) || trim($search) !== '';
                $levelClass = match ($level) {
                    'SD' => 'gx-lvl-sd',
                    'MI' => 'gx-lvl-mi',
                    'SMP' => 'gx-lvl-smp',
                    'MTs' => 'gx-lvl-mts',
                };
            @endphp

            <div class="gx-level-card">
                <button type="button" wire:click="toggleLevel('{{ $level }}')" class="gx-level-btn {{ $levelClass }}">
                    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.5rem;">
                        <span>Jenjang {{ $level }}</span>
                        <span class="gx-glass-pill">{{ $levelData['schoolCount'] }} sekolah</span>
                        <span class="gx-glass-pill">{{ $levelData['studentCount'] }} siswa</span>
                    </div>
                    <span class="gx-toggle-pill">{{ $isLevelOpen ? 'Tutup ▲' : 'Buka ▼' }}</span>
                </button>

                @if ($isLevelOpen)
                    <div class="gx-level-body">
                        @if (empty($levelData['schools']))
                            <div style="font-size:0.85rem;color:#64748b;padding:0.25rem 0.5rem;">
                                Belum ada peserta terdaftar di jenjang {{ $level }}.
                            </div>
                        @else
                            @foreach ($levelData['schools'] as $sIdx => $school)
                                @php
                                    $schoolKey = $level . '::' . $school['key'];
                                    $isSchoolOpen = in_array($schoolKey, $openSchools, true) || trim($search) !== '';
                                @endphp
                                <div class="gx-school-card">
                                    <button type="button" wire:click="toggleSchool(@js($schoolKey))" class="gx-school-bar">
                                        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.45rem;">
                                            <span>{{ $sIdx + 1 }}. {{ $school['name'] }}</span>
                                            <span class="gx-glass-pill">{{ $school['studentCount'] }} siswa</span>
                                            @if ($school['username'])
                                                <span class="gx-glass-pill">Username Sekolah: {{ $school['username'] }}</span>
                                            @endif
                                        </div>
                                        <span class="gx-toggle-pill">{{ $isSchoolOpen ? 'Tutup ▲' : 'Buka ▼' }}</span>
                                    </button>

                                    @if ($isSchoolOpen)
                                        <div class="gx-school-body">
                                            @foreach ($school['classes'] as $className => $students)
                                                @php
                                                    $classKey = $schoolKey . '::' . $className;
                                                    $isClassOpen = in_array($classKey, $openClasses, true) || trim($search) !== '';
                                                @endphp
                                                <div class="gx-class-card">
                                                    <button type="button" wire:click="toggleClass(@js($classKey))" class="gx-class-btn">
                                                        <div style="display:flex;align-items:center;gap:0.5rem;">
                                                            <span>{{ $className }}</span>
                                                            <span class="gx-pill gx-pill-blue" style="padding:0.15rem 0.55rem;">{{ count($students) }} siswa</span>
                                                        </div>
                                                        <span>{{ $isClassOpen ? '▲' : '▼' }}</span>
                                                    </button>

                                                    @if ($isClassOpen)
                                                        {{-- Desktop Table --}}
                                                        <div class="gx-table-wrap">
                                                            <table class="gx-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>No</th>
                                                                        <th>Kode / User</th>
                                                                        <th>NISN</th>
                                                                        <th>Nama Siswa</th>
                                                                        <th>No HP</th>
                                                                        <th>Orang Tua</th>
                                                                        <th>HP Ortu</th>
                                                                        <th style="text-align:right;">Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($students as $i => $student)
                                                                        <tr>
                                                                            <td>{{ $i + 1 }}</td>
                                                                            <td style="font-family:monospace;">{{ $student->user?->username ?: ($student->competition_code ?: $student->nis) }}</td>
                                                                            <td>{{ $student->nisn ?: '-' }}</td>
                                                                            <td style="font-weight:700;">{{ $student->name }}</td>
                                                                            <td style="color:#059669;font-weight:600;">{{ $student->phone ?: '-' }}</td>
                                                                            <td>{{ $student->parent_name ?: ($student->father_name ?: ($student->mother_name ?: '-')) }}</td>
                                                                            <td style="color:#059669;font-weight:600;">{{ $student->parent_phone ?: ($student->father_phone ?: ($student->mother_phone ?: '-')) }}</td>
                                                                            <td style="text-align:right;white-space:nowrap;">
                                                                                <button type="button" wire:click="startInlineEdit({{ $student->id }})" class="gx-btn gx-btn-edit">
                                                                                    {{ $editingStudentId === $student->id ? 'Tutup' : 'Edit' }}
                                                                                </button>
                                                                                <a href="{{ route('report.brain-champion-card', $student->id) }}" target="_blank" class="gx-btn gx-btn-card">Kartu</a>
                                                                                <button type="button" wire:click="deleteStudent({{ $student->id }})" wire:confirm="Hapus siswa {{ $student->name }}?" class="gx-btn gx-btn-del">Hapus</button>
                                                                            </td>
                                                                        </tr>
                                                                        @if ($editingStudentId === $student->id)
                                                                            <tr>
                                                                                <td colspan="8">
                                                                                    @include('filament.admin.pages.partials.external-student-inline-edit', ['student' => $student])
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        {{-- Mobile Key-Value Cards --}}
                                                        <div class="gx-mobile-list">
                                                            @foreach ($students as $i => $student)
                                                                <div class="gx-m-card">
                                                                    <div class="gx-kv"><span class="gx-kv-k">No</span><span class="gx-kv-v">{{ $i + 1 }}</span></div>
                                                                    <div class="gx-kv"><span class="gx-kv-k">Username / Kode</span><span class="gx-kv-v" style="font-family:monospace;">{{ $student->user?->username ?: ($student->competition_code ?: $student->nis) }}</span></div>
                                                                    <div class="gx-kv"><span class="gx-kv-k">Nama</span><span class="gx-kv-v">{{ $student->name }}</span></div>
                                                                    <div class="gx-kv"><span class="gx-kv-k">NISN</span><span class="gx-kv-v">{{ $student->nisn ?: '-' }}</span></div>
                                                                    <div class="gx-kv"><span class="gx-kv-k">No HP</span><span class="gx-kv-v" style="color:#059669;">{{ $student->phone ?: '-' }}</span></div>
                                                                    <div class="gx-kv"><span class="gx-kv-k">Orang Tua</span><span class="gx-kv-v">{{ $student->parent_name ?: ($student->father_name ?: ($student->mother_name ?: '-')) }}</span></div>
                                                                    <div class="gx-kv" style="border-bottom:none;"><span class="gx-kv-k">HP Ortu</span><span class="gx-kv-v" style="color:#059669;">{{ $student->parent_phone ?: ($student->father_phone ?: ($student->mother_phone ?: '-')) }}</span></div>

                                                                    <div class="gx-m-actions">
                                                                        <button type="button" wire:click="startInlineEdit({{ $student->id }})" class="gx-btn gx-btn-edit">
                                                                            {{ $editingStudentId === $student->id ? 'Tutup' : 'Edit' }}
                                                                        </button>
                                                                        <a href="{{ route('report.brain-champion-card', $student->id) }}" target="_blank" class="gx-btn gx-btn-card">Kartu</a>
                                                                        <button type="button" wire:click="deleteStudent({{ $student->id }})" wire:confirm="Hapus siswa {{ $student->name }}?" class="gx-btn gx-btn-del">Hapus</button>
                                                                    </div>

                                                                    @if ($editingStudentId === $student->id)
                                                                        @include('filament.admin.pages.partials.external-student-inline-edit', ['student' => $student])
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
                            @endforeach
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
