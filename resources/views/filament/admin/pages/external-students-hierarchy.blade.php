<x-filament-panels::page>
    <style>
        .gx-wrap { display: flex; flex-direction: column; gap: 0.9rem; font-family: 'Outfit', sans-serif; }
        .gx-toolbar {
            display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem;
            background: rgba(255, 255, 255, 0.94); border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 1rem;
            padding: 0.8rem 1rem; box-shadow: 0 8px 20px -6px rgba(10, 66, 130, 0.06);
        }
        .dark .gx-toolbar { background: rgba(30, 41, 59, 0.85); border-color: rgba(255, 255, 255, 0.09); }
        .gx-search {
            flex: 1 1 260px; min-width: 200px; padding: 0.55rem 0.95rem; border-radius: 0.75rem;
            border: 1px solid #cbd5e1; background: #f8fafc; color: #0f172a; font-size: 0.88rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .gx-search:focus { outline: none; border-color: #0a4282; box-shadow: 0 0 0 2px rgba(10, 66, 130, 0.16); }
        .dark .gx-search { background: #0f172a; border-color: #334155; color: #f8fafc; }
        .dark .gx-search:focus { border-color: #fbb900; box-shadow: 0 0 0 2px rgba(251, 185, 0, 0.22); }
        .gx-badges { display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem; }
        .gx-pill {
            display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.32rem 0.78rem;
            border-radius: 9999px; font-size: 0.76rem; font-weight: 700; font-variant-numeric: tabular-nums;
        }
        .gx-pill-primary { background: rgba(10, 66, 130, 0.08); color: #0a4282; border: 1px solid rgba(10, 66, 130, 0.18); }
        .dark .gx-pill-primary { background: rgba(14, 165, 233, 0.15); color: #bae6fd; border-color: rgba(14, 165, 233, 0.3); }
        .gx-pill-gold { background: #fbb900; color: #0a4282; }
        .gx-pill-muted { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .dark .gx-pill-muted { background: #1e293b; color: #cbd5e1; border-color: #334155; }
        .gx-level-card {
            border-radius: 1rem; overflow: hidden; border: 1px solid rgba(15, 23, 42, 0.08);
            background: #ffffff; box-shadow: 0 8px 20px -6px rgba(10, 66, 130, 0.06);
        }
        .dark .gx-level-card { background: #1e293b; border-color: rgba(255, 255, 255, 0.09); }
        .gx-level-btn {
            width: 100%; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
            gap: 0.5rem; padding: 0.8rem 1.05rem; background: #0a4282; color: #ffffff; font-weight: 700;
            font-size: 0.94rem; border: none; cursor: pointer; text-align: left;
            transition: background-color 0.2s ease;
        }
        .gx-level-btn:hover { background: #0d52a0; }
        .dark .gx-level-btn { background: #0f294d; border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
        .dark .gx-level-btn:hover { background: #163a6b; }
        .gx-level-tag {
            display: inline-flex; align-items: center; justify-content: center; min-width: 2.75rem;
            padding: 0.2rem 0.65rem; border-radius: 9999px; background: #fbb900; color: #0a4282;
            font-size: 0.76rem; font-weight: 800; letter-spacing: 0.02em;
        }
        .gx-glass-pill {
            display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 9999px;
            background: rgba(255, 255, 255, 0.14); font-size: 0.75rem; font-weight: 600; color: #e2e8f0;
            font-variant-numeric: tabular-nums;
        }
        .gx-chevron { width: 1rem; height: 1rem; transition: transform 0.2s ease; flex-shrink: 0; }
        .gx-chevron-open { transform: rotate(180deg); }
        .gx-level-body { padding: 0.85rem; display: flex; flex-direction: column; gap: 0.7rem; background: #f8fafc; }
        .dark .gx-level-body { background: #0f172a; }
        .gx-school-card {
            border-radius: 0.85rem; border: 1px solid rgba(15, 23, 42, 0.09); background: #ffffff; overflow: hidden;
        }
        .dark .gx-school-card { background: #1e293b; border-color: rgba(255, 255, 255, 0.09); }
        .gx-school-bar {
            width: 100%; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
            gap: 0.5rem; padding: 0.68rem 0.95rem; background: rgba(10, 66, 130, 0.05);
            color: #0a4282; font-weight: 700; font-size: 0.88rem; border: none;
            border-bottom: 1px solid rgba(10, 66, 130, 0.09); cursor: pointer; text-align: left;
            transition: background-color 0.2s ease;
        }
        .gx-school-bar:hover { background: rgba(10, 66, 130, 0.09); }
        .dark .gx-school-bar { background: rgba(15, 23, 42, 0.65); color: #f8fafc; border-bottom-color: rgba(255, 255, 255, 0.08); }
        .gx-school-body { padding: 0.7rem; display: flex; flex-direction: column; gap: 0.6rem; }
        .gx-class-card { border-radius: 0.75rem; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff; }
        .dark .gx-class-card { border-color: rgba(255, 255, 255, 0.08); background: #0f172a; }
        .gx-class-btn {
            width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;
            padding: 0.52rem 0.85rem; background: #f8fafc; color: #0f172a; font-weight: 700;
            font-size: 0.84rem; border: none; border-bottom: 1px solid #e2e8f0; cursor: pointer; text-align: left;
        }
        .gx-class-btn:hover { background: #eff6ff; color: #0a4282; }
        .dark .gx-class-btn { background: rgba(30, 41, 59, 0.75); color: #e2e8f0; border-bottom-color: rgba(255, 255, 255, 0.07); }
        .gx-table-wrap { overflow-x: auto; }
        .gx-table { width: 100%; border-collapse: collapse; font-size: 0.83rem; text-align: left; }
        .gx-table th {
            background: rgba(10, 66, 130, 0.04); color: #0a4282; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.04em; font-size: 0.7rem; padding: 0.55rem 0.65rem; border-bottom: 1px solid rgba(10, 66, 130, 0.12);
        }
        .dark .gx-table th { background: rgba(15, 23, 42, 0.75); color: #93c5fd; border-bottom-color: rgba(251, 185, 0, 0.2); }
        .gx-table td {
            padding: 0.58rem 0.65rem; border-bottom: 1px solid rgba(15, 23, 42, 0.05);
            vertical-align: middle; color: #0f172a;
        }
        .dark .gx-table td { border-bottom-color: rgba(255, 255, 255, 0.06); color: #e2e8f0; }
        .gx-table tbody tr:hover { background: rgba(10, 66, 130, 0.025); }
        .dark .gx-table tbody tr:hover { background: rgba(255, 255, 255, 0.03); }
        .gx-mono { font-family: ui-monospace, monospace; font-variant-numeric: tabular-nums; font-size: 0.78rem; }
        .gx-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem;
            padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;
            border: none; cursor: pointer; text-decoration: none; transition: opacity 0.15s ease;
        }
        .gx-btn:hover { opacity: 0.9; }
        .gx-btn-edit { background: #fbb900; color: #0a4282; }
        .gx-btn-card { background: #0a4282; color: #ffffff; }
        .gx-btn-del { background: #f43f5e; color: #ffffff; }
        .gx-btn-save { background: #10b981; color: #ffffff; padding: 0.45rem 1.05rem; font-size: 0.8rem; }
        .gx-btn-cancel { background: #e2e8f0; color: #0f172a; padding: 0.45rem 0.95rem; font-size: 0.8rem; }
        .dark .gx-btn-cancel { background: #334155; color: #f8fafc; }
        .gx-mobile-list { display: none; padding: 0.65rem; flex-direction: column; gap: 0.6rem; background: #f8fafc; }
        .dark .gx-mobile-list { background: #0f172a; }
        .gx-m-card {
            background: #ffffff; border: 1px solid rgba(15, 23, 42, 0.09); border-radius: 0.9rem; padding: 0.7rem 0.85rem;
            font-size: 0.8rem; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        }
        .dark .gx-m-card { background: #1e293b; border-color: rgba(255, 255, 255, 0.09); }
        .gx-kv {
            display: flex; justify-content: space-between; align-items: center; gap: 0.65rem;
            padding: 0.36rem 0; border-bottom: 1px dashed rgba(15, 23, 42, 0.08);
        }
        .dark .gx-kv { border-bottom-color: rgba(255, 255, 255, 0.08); }
        .gx-kv-k { color: #64748b; font-weight: 600; font-size: 0.75rem; flex-shrink: 0; }
        .dark .gx-kv-k { color: #94a3b8; }
        .gx-kv-v { font-weight: 600; text-align: right; color: #0f172a; word-break: break-word; }
        .dark .gx-kv-v { color: #f8fafc; }
        .gx-m-actions { display: flex; justify-content: flex-end; gap: 0.4rem; padding-top: 0.55rem; margin-top: 0.25rem; }
        .gx-edit-box {
            background: #f8fafc; border: 1px solid rgba(10, 66, 130, 0.16); border-radius: 0.85rem; padding: 0.9rem; margin-top: 0.5rem;
        }
        .dark .gx-edit-box { background: #0f172a; border-color: rgba(251, 185, 0, 0.25); }
        .gx-edit-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.65rem; }
        .gx-edit-label { display: block; font-size: 0.73rem; font-weight: 700; color: #0a4282; margin-bottom: 0.22rem; }
        .dark .gx-edit-label { color: #93c5fd; }
        .gx-edit-input {
            width: 100%; padding: 0.45rem 0.7rem; border-radius: 0.65rem; border: 1px solid #cbd5e1;
            background: #ffffff; color: #0f172a; font-size: 0.83rem;
        }
        .dark .gx-edit-input { background: #1e293b; border-color: #475569; color: #f8fafc; }
        @media (max-width: 767px) {
            .gx-table-wrap { display: none; }
            .gx-mobile-list { display: flex; }
            .gx-edit-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="gx-wrap">
        <div class="gx-toolbar">
            <input
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari username / nama / NISN / kelas / sekolah..."
                class="gx-search"
            />
            <div class="gx-badges">
                <span class="gx-pill gx-pill-primary">
                    Total Siswa Eksternal: {{ $this->hierarchy['totalStudents'] }}
                </span>
                <span class="gx-pill gx-pill-gold">
                    {{ $this->hierarchy['totalSchools'] }} Sekolah
                </span>
            </div>
        </div>

        @foreach (['SD', 'MI', 'SMP', 'MTs'] as $level)
            @php
                $levelData = $this->hierarchy['levels'][$level] ?? ['schools' => [], 'schoolCount' => 0, 'studentCount' => 0];
                $isLevelOpen = in_array($level, $openLevels, true) || trim($search) !== '';
            @endphp

            <div class="gx-level-card">
                <button type="button" wire:click="toggleLevel('{{ $level }}')" class="gx-level-btn">
                    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.5rem;">
                        <span class="gx-level-tag">{{ $level }}</span>
                        <span>Jenjang {{ $level }}</span>
                        <span class="gx-glass-pill">{{ $levelData['schoolCount'] }} sekolah</span>
                        <span class="gx-glass-pill">{{ $levelData['studentCount'] }} siswa</span>
                    </div>
                    <svg class="gx-chevron {{ $isLevelOpen ? 'gx-chevron-open' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                @if ($isLevelOpen)
                    <div class="gx-level-body">
                        @if (empty($levelData['schools']))
                            <div style="font-size:0.84rem;color:#64748b;padding:0.25rem 0.5rem;">
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
                                            <span class="gx-pill gx-pill-primary" style="padding:0.15rem 0.55rem;">{{ $school['studentCount'] }} siswa</span>
                                            @if ($school['username'])
                                                <span class="gx-pill gx-pill-muted gx-mono" style="padding:0.15rem 0.55rem;">Akun: {{ $school['username'] }}</span>
                                            @endif
                                        </div>
                                        <svg class="gx-chevron {{ $isSchoolOpen ? 'gx-chevron-open' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
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
                                                            <span class="gx-pill gx-pill-gold" style="padding:0.12rem 0.55rem;">{{ count($students) }} siswa</span>
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
                                                                        <th>No</th>
                                                                        <th>Username / Kode</th>
                                                                        <th>NISN</th>
                                                                        <th>Nama Siswa</th>
                                                                        <th>Orang Tua / WA</th>
                                                                        <th style="text-align:right;">Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($students as $i => $student)
                                                                        <tr>
                                                                            <td class="gx-mono">{{ $i + 1 }}</td>
                                                                            <td class="gx-mono">{{ $student->user?->username ?: ($student->competition_code ?: $student->nis) }}</td>
                                                                            <td class="gx-mono">{{ $student->nisn ?: '-' }}</td>
                                                                            <td style="font-weight:700;">{{ $student->name }}</td>
                                                                            <td>
                                                                                <div>{{ $student->parent_name ?: ($student->father_name ?: ($student->mother_name ?: '-')) }}</div>
                                                                                <div class="gx-mono" style="color:#10b981;font-weight:600;">{{ $student->parent_phone ?: ($student->phone ?: '-') }}</div>
                                                                            </td>
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
                                                                                <td colspan="6">
                                                                                    @include('filament.admin.pages.partials.external-student-inline-edit', ['student' => $student])
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
                                                                    <div class="gx-kv"><span class="gx-kv-k">No</span><span class="gx-kv-v gx-mono">{{ $i + 1 }}</span></div>
                                                                    <div class="gx-kv"><span class="gx-kv-k">Username / Kode</span><span class="gx-kv-v gx-mono">{{ $student->user?->username ?: ($student->competition_code ?: $student->nis) }}</span></div>
                                                                    <div class="gx-kv"><span class="gx-kv-k">Nama</span><span class="gx-kv-v">{{ $student->name }}</span></div>
                                                                    <div class="gx-kv"><span class="gx-kv-k">NISN</span><span class="gx-kv-v gx-mono">{{ $student->nisn ?: '-' }}</span></div>
                                                                    <div class="gx-kv"><span class="gx-kv-k">Orang Tua</span><span class="gx-kv-v">{{ $student->parent_name ?: ($student->father_name ?: ($student->mother_name ?: '-')) }}</span></div>
                                                                    <div class="gx-kv" style="border-bottom:none;"><span class="gx-kv-k">No. HP / WA</span><span class="gx-kv-v gx-mono" style="color:#10b981;">{{ $student->parent_phone ?: ($student->phone ?: '-') }}</span></div>

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
