<x-filament-panels::page>
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
                                                                        <tr wire:click="startInlineEdit({{ $student->id }})" style="cursor:pointer;" title="Klik baris untuk edit siswa">
                                                                            <td class="gx-mono">{{ $i + 1 }}</td>
                                                                            <td class="gx-mono">{{ $student->user?->username ?: ($student->competition_code ?: $student->nis) }}</td>
                                                                            <td class="gx-mono">{{ $student->nisn ?: '-' }}</td>
                                                                            <td style="font-weight:700;color:#0a4282;" class="dark:!text-sky-300">{{ $student->name }}</td>
                                                                            <td>
                                                                                <div>{{ $student->parent_name ?: ($student->father_name ?: ($student->mother_name ?: '-')) }}</div>
                                                                                <div class="gx-mono" style="color:#10b981;font-weight:600;">{{ $student->parent_phone ?: ($student->phone ?: '-') }}</div>
                                                                            </td>
                                                                            <td style="text-align:right;white-space:nowrap;" onclick="event.stopPropagation()">
                                                                                <a href="{{ route('report.brain-champion-card', $student->id) }}" target="_blank" class="gx-btn gx-btn-card">Kartu</a>
                                                                                <button type="button" wire:click.stop="deleteStudent({{ $student->id }})" wire:confirm="Hapus siswa {{ $student->name }}?" class="gx-btn gx-btn-del">Hapus</button>
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
                                                                    <div wire:click="startInlineEdit({{ $student->id }})" style="cursor:pointer;">
                                                                        <div class="gx-kv"><span class="gx-kv-k">No</span><span class="gx-kv-v gx-mono">{{ $i + 1 }}</span></div>
                                                                        <div class="gx-kv"><span class="gx-kv-k">Username / Kode</span><span class="gx-kv-v gx-mono">{{ $student->user?->username ?: ($student->competition_code ?: $student->nis) }}</span></div>
                                                                        <div class="gx-kv"><span class="gx-kv-k">Nama</span><span class="gx-kv-v" style="color:#0a4282;font-weight:700;">{{ $student->name }}</span></div>
                                                                        <div class="gx-kv"><span class="gx-kv-k">NISN</span><span class="gx-kv-v gx-mono">{{ $student->nisn ?: '-' }}</span></div>
                                                                        <div class="gx-kv"><span class="gx-kv-k">Orang Tua</span><span class="gx-kv-v">{{ $student->parent_name ?: ($student->father_name ?: ($student->mother_name ?: '-')) }}</span></div>
                                                                        <div class="gx-kv" style="border-bottom:none;"><span class="gx-kv-k">No. HP / WA</span><span class="gx-kv-v gx-mono" style="color:#10b981;">{{ $student->parent_phone ?: ($student->phone ?: '-') }}</span></div>
                                                                    </div>

                                                                    <div class="gx-m-actions">
                                                                        <a href="{{ route('report.brain-champion-card', $student->id) }}" target="_blank" class="gx-btn gx-btn-card">Kartu</a>
                                                                        <button type="button" wire:click.stop="deleteStudent({{ $student->id }})" wire:confirm="Hapus siswa {{ $student->name }}?" class="gx-btn gx-btn-del">Hapus</button>
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
