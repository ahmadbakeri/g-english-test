<x-filament-panels::page>
    @php
        $hierarchy = $this->hierarchy;
        $counts = $this->globalCounts;
        $hasSearch = trim($search) !== '';
        $autoExpandDeep = $hasSearch && $hierarchy['totalStudents'] > 0 && $hierarchy['totalStudents'] <= 40;
    @endphp

    <div class="gx-wrap" x-data @gx-pdf-ready.window="window.location.href = $event.detail.url">
        {{-- Live Chunked PDF Export Progress Banner --}}
        @if ($pdfStatus !== 'idle')
            <div
                @if ($pdfStatus === 'running') wire:poll.350ms="processPdfChunk" @endif
                class="gx-toolbar"
                style="flex-direction:column;align-items:stretch;gap:0.55rem;border-color:{{ $pdfStatus === 'done' ? 'rgba(16,185,129,0.35)' : ($pdfStatus === 'error' ? 'rgba(244,63,94,0.35)' : 'rgba(10,66,130,0.28)') }};"
            >
                <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:0.5rem;">
                    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.5rem;">
                        @if ($pdfStatus === 'running')
                            <span class="gx-pill gx-pill-gold">Proses PDF {{ $pdfPercent }}%</span>
                            <span style="font-size:0.84rem;font-weight:700;color:#0a4282;" class="dark:!text-white">
                                {{ number_format($pdfProcessed, 0, ',', '.') }} / {{ number_format($pdfTotal, 0, ',', '.') }} siswa
                            </span>
                            <span class="gx-pill gx-pill-muted">
                                Hal {{ number_format($pdfPagesDone, 0, ',', '.') }} / {{ number_format($pdfTotalPages, 0, ',', '.') }} • {{ $pdfElapsed }}
                            </span>
                        @elseif ($pdfStatus === 'done')
                            <span class="gx-pill gx-pill-success">PDF Siap</span>
                            <span style="font-size:0.84rem;font-weight:700;color:#047857;" class="dark:!text-emerald-300">
                                {{ number_format($pdfProcessed, 0, ',', '.') }} siswa • {{ number_format($pdfTotalPages, 0, ',', '.') }} halaman • {{ $pdfFileSize }} ({{ $pdfElapsed }})
                            </span>
                        @else
                            <span class="gx-pill gx-pill-danger">Export Gagal</span>
                            <span style="font-size:0.82rem;color:#be123c;">{{ $pdfError }}</span>
                        @endif
                    </div>

                    <div style="display:flex;align-items:center;gap:0.45rem;">
                        @if ($pdfStatus === 'done' && $pdfJobId)
                            <a href="{{ route('admin.pdf-export.download', $pdfJobId) }}" class="gx-btn gx-btn-card">
                                Download PDF ({{ $pdfFileSize }})
                            </a>
                            <button type="button" wire:click="cancelPdfExport" class="gx-btn gx-btn-cancel" style="padding:0.32rem 0.78rem;font-size:0.75rem;">
                                Tutup
                            </button>
                        @elseif ($pdfStatus === 'running')
                            <button type="button" wire:click="cancelPdfExport" class="gx-btn gx-btn-del">
                                Batalkan
                            </button>
                        @else
                            <button type="button" wire:click="cancelPdfExport" class="gx-btn gx-btn-cancel" style="padding:0.32rem 0.78rem;font-size:0.75rem;">
                                Tutup
                            </button>
                        @endif
                    </div>
                </div>

                @if ($pdfStatus === 'running' || $pdfStatus === 'done')
                    <div style="width:100%;height:0.5rem;border-radius:9999px;background:rgba(15,23,42,0.08);overflow:hidden;" class="dark:!bg-slate-800">
                        <div style="height:100%;border-radius:9999px;width:{{ $pdfPercent }}%;background:{{ $pdfStatus === 'done' ? '#10b981' : '#0a4282' }};transition:width 0.3s ease;"></div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Search & Program Filter Toolbar --}}
        <div class="gx-toolbar">
            <input
                type="search"
                wire:model.live.debounce.350ms="search"
                placeholder="Cari username / nama / NISN / program / kelas / sekolah..."
                class="gx-search"
            />
            <div class="gx-badges">
                <button
                    type="button"
                    wire:click="setProgramFilter('all')"
                    class="gx-pill {{ $programFilter === 'all' ? 'gx-pill-gold' : 'gx-pill-muted' }}"
                    style="cursor:pointer;"
                >
                    Semua: {{ number_format($counts['all'], 0, ',', '.') }}
                </button>
                <button
                    type="button"
                    wire:click="setProgramFilter('brain_champion')"
                    class="gx-pill {{ $programFilter === 'brain_champion' ? 'gx-pill-gold' : 'gx-pill-primary' }}"
                    style="cursor:pointer;"
                >
                    Brain Champion: {{ number_format($counts['brain_champion'], 0, ',', '.') }}
                </button>
                <button
                    type="button"
                    wire:click="setProgramFilter('non_bc')"
                    class="gx-pill {{ $programFilter === 'non_bc' ? 'gx-pill-gold' : 'gx-pill-muted' }}"
                    style="cursor:pointer;"
                >
                    Non-BC / Umum: {{ number_format($counts['non_bc'], 0, ',', '.') }}
                </button>
                <span class="gx-pill gx-pill-success">
                    {{ number_format($hierarchy['totalSchools'], 0, ',', '.') }} Sekolah
                </span>
            </div>
        </div>

        @if ($hasSearch && $hierarchy['totalStudents'] > 40)
            <div style="font-size:0.8rem;color:#475569;padding:0.45rem 0.85rem;border-radius:0.75rem;background:rgba(10,66,130,0.06);border:1px solid rgba(10,66,130,0.12);" class="dark:!text-slate-300 dark:!bg-slate-800/60 dark:!border-slate-700">
                Ditemukan <strong>{{ number_format($hierarchy['totalStudents'], 0, ',', '.') }}</strong> siswa cocok di <strong>{{ $hierarchy['totalSchools'] }}</strong> sekolah. Klik sekolah &amp; kelas yang diinginkan (atau persempit kata kunci agar otomatis terbuka).
            </div>
        @endif

        @foreach (['SD', 'MI', 'SMP', 'MTs'] as $level)
            @php
                $levelData = $hierarchy['levels'][$level] ?? ['schools' => [], 'schoolCount' => 0, 'studentCount' => 0, 'bcCount' => 0];
                $isLevelOpen = in_array($level, $openLevels, true) || ($hasSearch && $levelData['studentCount'] > 0);
            @endphp

            <div class="gx-level-card" wire:key="ext-lvl-{{ $level }}">
                <button type="button" wire:click="toggleLevel('{{ $level }}')" class="gx-level-btn">
                    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.5rem;">
                        <span class="gx-level-tag">{{ $level }}</span>
                        <span>Jenjang {{ $level }}</span>
                        <span class="gx-glass-pill">{{ number_format($levelData['schoolCount'], 0, ',', '.') }} sekolah</span>
                        <span class="gx-glass-pill">{{ number_format($levelData['studentCount'], 0, ',', '.') }} siswa</span>
                        @if ($levelData['bcCount'] > 0 && $programFilter === 'all')
                            <span class="gx-pill gx-pill-gold" style="padding:0.12rem 0.55rem;font-size:0.7rem;">BC: {{ number_format($levelData['bcCount'], 0, ',', '.') }}</span>
                        @endif
                    </div>
                    <svg class="gx-chevron {{ $isLevelOpen ? 'gx-chevron-open' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                @if ($isLevelOpen)
                    <div class="gx-level-body">
                        @if (empty($levelData['schools']))
                            <div style="font-size:0.84rem;color:#64748b;padding:0.25rem 0.5rem;">
                                Belum ada siswa terdaftar di jenjang {{ $level }} untuk filter ini.
                            </div>
                        @else
                            @foreach ($levelData['schools'] as $sIdx => $school)
                                @php
                                    $schoolKey = $level . '::' . $school['key'];
                                    $isSchoolOpen = in_array($schoolKey, $openSchools, true) || $autoExpandDeep;
                                @endphp
                                <div class="gx-school-card" wire:key="ext-sch-{{ md5($schoolKey) }}">
                                    <button type="button" wire:click="toggleSchool(@js($schoolKey))" class="gx-school-bar">
                                        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.45rem;">
                                            <span>{{ $sIdx + 1 }}. {{ $school['name'] }}</span>
                                            <span class="gx-pill gx-pill-primary" style="padding:0.15rem 0.55rem;">{{ number_format($school['studentCount'], 0, ',', '.') }} siswa</span>
                                            @if ($school['bcCount'] > 0 && $programFilter === 'all')
                                                <span class="gx-pill gx-pill-gold" style="padding:0.12rem 0.5rem;font-size:0.7rem;">BC: {{ $school['bcCount'] }}</span>
                                            @endif
                                            @if ($school['username'])
                                                <span class="gx-pill gx-pill-muted gx-mono" style="padding:0.15rem 0.55rem;">Akun: {{ $school['username'] }}</span>
                                            @endif
                                        </div>
                                        <svg class="gx-chevron {{ $isSchoolOpen ? 'gx-chevron-open' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    @if ($isSchoolOpen)
                                        @php
                                            $schoolClasses = $this->getSchoolClasses($schoolKey, $school['rawSchools']);
                                        @endphp
                                        <div class="gx-school-body">
                                            @foreach ($schoolClasses as $cls)
                                                @php
                                                    $className = $cls['name'];
                                                    $classKey = $schoolKey . '::' . $className;
                                                    $isClassOpen = in_array($classKey, $openClasses, true) || $autoExpandDeep;
                                                @endphp
                                                <div class="gx-class-card" wire:key="ext-cls-{{ md5($classKey) }}">
                                                    <button type="button" wire:click="toggleClass(@js($classKey))" class="gx-class-btn">
                                                        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.45rem;">
                                                            <span>{{ $className }}</span>
                                                            <span class="gx-pill gx-pill-primary" style="padding:0.12rem 0.55rem;">{{ number_format($cls['total'], 0, ',', '.') }} siswa</span>
                                                            @if ($cls['bcCount'] > 0 && $programFilter === 'all')
                                                                <span class="gx-pill gx-pill-gold" style="padding:0.1rem 0.5rem;font-size:0.68rem;">BC: {{ $cls['bcCount'] }}</span>
                                                            @endif
                                                        </div>
                                                        <svg class="gx-chevron {{ $isClassOpen ? 'gx-chevron-open' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>

                                                    @if ($isClassOpen)
                                                        @php
                                                            $slice = $this->getClassStudents($classKey, $school['rawSchools'], $cls['rawClasses']);
                                                            $students = $slice['items'];
                                                        @endphp
                                                        <div class="gx-table-wrap">
                                                            <table class="gx-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width:2.8rem;">No</th>
                                                                        <th>Username / Kode</th>
                                                                        <th>NISN</th>
                                                                        <th>Nama Siswa</th>
                                                                        <th>Program</th>
                                                                        <th>Orang Tua / WA</th>
                                                                        <th style="text-align:right;">Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($students as $i => $student)
                                                                        @php
                                                                            $isBc = $student->course_level === \App\Livewire\BrainChampionRegistration::EVENT_TAG;
                                                                        @endphp
                                                                        <tr wire:key="ext-row-{{ $student->id }}" wire:click="startInlineEdit({{ $student->id }})" style="cursor:pointer;" title="Klik baris untuk edit siswa">
                                                                            <td class="gx-mono">{{ $i + 1 }}</td>
                                                                            <td class="gx-mono">{{ $student->user?->username ?: ($student->competition_code ?: $student->nis) }}</td>
                                                                            <td class="gx-mono">{{ $student->nisn ?: '-' }}</td>
                                                                            <td style="font-weight:700;color:#0a4282;" class="dark:!text-sky-300">{{ $student->name }}</td>
                                                                            <td>
                                                                                @if ($isBc)
                                                                                    <span class="gx-pill gx-pill-gold" style="padding:0.1rem 0.5rem;font-size:0.68rem;">Brain Champion</span>
                                                                                @else
                                                                                    <span class="gx-pill gx-pill-muted" style="padding:0.1rem 0.5rem;font-size:0.68rem;">{{ $student->course_level ?: 'Eksternal Umum' }}</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                <div>{{ $student->parent_name ?: ($student->father_name ?: ($student->mother_name ?: '-')) }}</div>
                                                                                <div class="gx-mono" style="color:#10b981;font-weight:600;">{{ $student->parent_phone ?: ($student->phone ?: '-') }}</div>
                                                                            </td>
                                                                            <td style="text-align:right;white-space:nowrap;" onclick="event.stopPropagation()">
                                                                                @if ($isBc)
                                                                                    <a href="{{ route('report.brain-champion-card', $student->id) }}" target="_blank" class="gx-btn gx-btn-card">Kartu</a>
                                                                                @endif
                                                                                <button type="button" wire:click.stop="deleteStudent({{ $student->id }})" wire:confirm="Hapus siswa {{ $student->name }}?" class="gx-btn gx-btn-del">Hapus</button>
                                                                            </td>
                                                                        </tr>
                                                                        @if ($editingStudentId === $student->id)
                                                                            <tr wire:key="ext-edit-{{ $student->id }}">
                                                                                <td colspan="7">
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
                                                                @php
                                                                    $isBc = $student->course_level === \App\Livewire\BrainChampionRegistration::EVENT_TAG;
                                                                @endphp
                                                                <div class="gx-m-card" wire:key="ext-m-{{ $student->id }}">
                                                                    <div wire:click="startInlineEdit({{ $student->id }})" style="cursor:pointer;">
                                                                        <div class="gx-kv"><span class="gx-kv-k">No</span><span class="gx-kv-v gx-mono">{{ $i + 1 }}</span></div>
                                                                        <div class="gx-kv"><span class="gx-kv-k">Username / Kode</span><span class="gx-kv-v gx-mono">{{ $student->user?->username ?: ($student->competition_code ?: $student->nis) }}</span></div>
                                                                        <div class="gx-kv"><span class="gx-kv-k">Nama</span><span class="gx-kv-v" style="color:#0a4282;font-weight:700;">{{ $student->name }}</span></div>
                                                                        <div class="gx-kv">
                                                                            <span class="gx-kv-k">Program</span>
                                                                            <span class="gx-kv-v">
                                                                                @if ($isBc)
                                                                                    <span class="gx-pill gx-pill-gold" style="padding:0.08rem 0.5rem;font-size:0.68rem;">Brain Champion</span>
                                                                                @else
                                                                                    <span class="gx-pill gx-pill-muted" style="padding:0.08rem 0.5rem;font-size:0.68rem;">{{ $student->course_level ?: 'Eksternal Umum' }}</span>
                                                                                @endif
                                                                            </span>
                                                                        </div>
                                                                        <div class="gx-kv"><span class="gx-kv-k">NISN</span><span class="gx-kv-v gx-mono">{{ $student->nisn ?: '-' }}</span></div>
                                                                        <div class="gx-kv"><span class="gx-kv-k">Orang Tua</span><span class="gx-kv-v">{{ $student->parent_name ?: ($student->father_name ?: ($student->mother_name ?: '-')) }}</span></div>
                                                                        <div class="gx-kv" style="border-bottom:none;"><span class="gx-kv-k">No. HP / WA</span><span class="gx-kv-v gx-mono" style="color:#10b981;">{{ $student->parent_phone ?: ($student->phone ?: '-') }}</span></div>
                                                                    </div>

                                                                    <div class="gx-m-actions">
                                                                        @if ($isBc)
                                                                            <a href="{{ route('report.brain-champion-card', $student->id) }}" target="_blank" class="gx-btn gx-btn-card">Kartu</a>
                                                                        @endif
                                                                        <button type="button" wire:click.stop="deleteStudent({{ $student->id }})" wire:confirm="Hapus siswa {{ $student->name }}?" class="gx-btn gx-btn-del">Hapus</button>
                                                                    </div>

                                                                    @if ($editingStudentId === $student->id)
                                                                        @include('filament.admin.pages.partials.external-student-inline-edit', ['student' => $student])
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        @if ($slice['hasMore'])
                                                            <div style="display:flex;align-items:center;justify-content:center;gap:0.6rem;padding:0.65rem;border-top:1px solid rgba(15,23,42,0.06);background:#f8fafc;" class="dark:!bg-slate-900/60 dark:!border-slate-800">
                                                                <span style="font-size:0.76rem;color:#64748b;" class="dark:!text-slate-400">
                                                                    Menampilkan {{ $slice['loaded'] }} dari {{ number_format($cls['total'], 0, ',', '.') }} siswa
                                                                </span>
                                                                <button
                                                                    type="button"
                                                                    wire:click="loadMoreClass(@js($classKey))"
                                                                    wire:loading.attr="disabled"
                                                                    class="gx-btn gx-btn-card"
                                                                >
                                                                    Muat {{ min(\App\Filament\Resources\HR\ExternalStudentResource\Pages\ListExternalStudents::PER_CLASS_STEP, $cls['total'] - $slice['loaded']) }} lagi
                                                                </button>
                                                            </div>
                                                        @endif
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
