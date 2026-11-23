<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Search & Summary Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
            <div class="flex-1">
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari username / nama / NISN / kelas / sekolah..."
                    class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:border-primary-600 focus:ring-primary-600"
                />
            </div>
            <div class="flex items-center gap-2 text-xs font-semibold">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-blue-50 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200 border border-blue-200 dark:border-blue-800">
                    Total Siswa Eksternal: {{ $this->hierarchy['totalStudents'] }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200 border border-amber-200 dark:border-amber-800">
                    {{ $this->hierarchy['totalSchools'] }} Sekolah
                </span>
            </div>
        </div>

        {{-- Level 1: Jenjang (SD, MI, SMP, MTs) --}}
        @foreach (['SD', 'MI', 'SMP', 'MTs'] as $level)
            @php
                $levelData = $this->hierarchy['levels'][$level] ?? ['schools' => [], 'schoolCount' => 0, 'studentCount' => 0];
                $isLevelOpen = in_array($level, $openLevels, true) || trim($search) !== '';
                $levelColors = match ($level) {
                    'SD' => 'from-rose-600 to-red-600 text-white',
                    'MI' => 'from-emerald-600 to-teal-600 text-white',
                    'SMP' => 'from-blue-700 to-indigo-700 text-white',
                    'MTs' => 'from-amber-600 to-orange-600 text-white',
                };
            @endphp

            <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                {{-- Jenjang Header --}}
                <button
                    type="button"
                    wire:click="toggleLevel('{{ $level }}')"
                    class="w-full flex flex-wrap items-center justify-between gap-2 px-4 py-3 bg-gradient-to-r {{ $levelColors }} text-left font-bold transition hover:opacity-95"
                >
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-sm sm:text-base">Jenjang {{ $level }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs bg-white/20 backdrop-blur-sm">
                            {{ $levelData['schoolCount'] }} sekolah
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs bg-white/20 backdrop-blur-sm">
                            {{ $levelData['studentCount'] }} siswa
                        </span>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-black/15">
                        {{ $isLevelOpen ? 'Tutup ▲' : 'Buka ▼' }}
                    </span>
                </button>

                @if ($isLevelOpen)
                    <div class="p-3 sm:p-4 space-y-3 bg-slate-50/60 dark:bg-slate-950/40">
                        @if (empty($levelData['schools']))
                            <p class="text-sm text-slate-500 dark:text-slate-400 italic px-2 py-1">
                                Belum ada peserta terdaftar di jenjang {{ $level }}.
                            </p>
                        @else
                            {{-- Level 2: Sekolah --}}
                            @foreach ($levelData['schools'] as $sIdx => $school)
                                @php
                                    $schoolKey = $level . '::' . $school['key'];
                                    $isSchoolOpen = in_array($schoolKey, $openSchools, true) || trim($search) !== '';
                                @endphp
                                <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-xs">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-3.5 py-2.5 bg-gradient-to-r from-slate-800 to-slate-700 text-white">
                                        <button
                                            type="button"
                                            wire:click="toggleSchool(@js($schoolKey))"
                                            class="flex flex-wrap items-center gap-2 text-left flex-1"
                                        >
                                            <span class="font-bold text-sm">{{ $sIdx + 1 }}. {{ $school['name'] }}</span>
                                            <span class="px-2 py-0.5 rounded-full text-xs bg-amber-400/20 text-amber-200 border border-amber-400/30">
                                                {{ $school['studentCount'] }} siswa
                                            </span>
                                            @if ($school['username'])
                                                <span class="px-2 py-0.5 rounded-full text-xs bg-emerald-500/20 text-emerald-200 border border-emerald-400/30">
                                                    Username Sekolah: {{ $school['username'] }}
                                                </span>
                                            @endif
                                        </button>
                                        <div class="flex items-center gap-2">
                                            <button
                                                type="button"
                                                wire:click="toggleSchool(@js($schoolKey))"
                                                class="text-xs px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 font-semibold"
                                            >
                                                {{ $isSchoolOpen ? 'Tutup ▲' : 'Buka ▼' }}
                                            </button>
                                        </div>
                                    </div>

                                    @if ($isSchoolOpen)
                                        <div class="p-3 space-y-2.5">
                                            {{-- Level 3: Kelas --}}
                                            @foreach ($school['classes'] as $className => $students)
                                                @php
                                                    $classKey = $schoolKey . '::' . $className;
                                                    $isClassOpen = in_array($classKey, $openClasses, true) || trim($search) !== '';
                                                @endphp
                                                <div class="rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                                                    <button
                                                        type="button"
                                                        wire:click="toggleClass(@js($classKey))"
                                                        class="w-full flex items-center justify-between gap-2 px-3.5 py-2 bg-blue-50 dark:bg-slate-800/80 text-blue-900 dark:text-blue-200 font-bold text-xs sm:text-sm text-left hover:bg-blue-100/70 dark:hover:bg-slate-800"
                                                    >
                                                        <div class="flex items-center gap-2">
                                                            <span>{{ $className }}</span>
                                                            <span class="px-2 py-0.5 rounded-full text-xs bg-blue-600 text-white">
                                                                {{ count($students) }} siswa
                                                            </span>
                                                        </div>
                                                        <span class="text-xs">{{ $isClassOpen ? '▲' : '▼' }}</span>
                                                    </button>

                                                    @if ($isClassOpen)
                                                        {{-- Level 4: Siswa (Desktop Table + Mobile Key-Value Cards + Inline Edit) --}}
                                                        <div class="divide-y divide-slate-200 dark:divide-slate-800">
                                                            {{-- Desktop Table --}}
                                                            <div class="hidden md:block overflow-x-auto">
                                                                <table class="w-full text-xs sm:text-sm text-left">
                                                                    <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 uppercase text-[11px]">
                                                                        <tr>
                                                                            <th class="px-3 py-2 w-10">No</th>
                                                                            <th class="px-3 py-2">Kode / User</th>
                                                                            <th class="px-3 py-2">NISN</th>
                                                                            <th class="px-3 py-2">Nama Siswa</th>
                                                                            <th class="px-3 py-2">No HP</th>
                                                                            <th class="px-3 py-2">Orang Tua</th>
                                                                            <th class="px-3 py-2">HP Ortu</th>
                                                                            <th class="px-3 py-2 text-right">Aksi</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                                                        @foreach ($students as $i => $student)
                                                                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                                                                <td class="px-3 py-2 font-medium text-slate-500">{{ $i + 1 }}</td>
                                                                                <td class="px-3 py-2 font-mono text-xs text-slate-700 dark:text-slate-300">
                                                                                    {{ $student->user?->username ?: ($student->competition_code ?: $student->nis) }}
                                                                                </td>
                                                                                <td class="px-3 py-2 text-slate-600 dark:text-slate-400">{{ $student->nisn ?: '-' }}</td>
                                                                                <td class="px-3 py-2 font-bold text-slate-900 dark:text-white">{{ $student->name }}</td>
                                                                                <td class="px-3 py-2 text-emerald-700 dark:text-emerald-400">{{ $student->phone ?: '-' }}</td>
                                                                                <td class="px-3 py-2 text-slate-700 dark:text-slate-300">
                                                                                    {{ $student->parent_name ?: ($student->father_name ?: ($student->mother_name ?: '-')) }}
                                                                                </td>
                                                                                <td class="px-3 py-2 text-emerald-700 dark:text-emerald-400">
                                                                                    {{ $student->parent_phone ?: ($student->father_phone ?: ($student->mother_phone ?: '-')) }}
                                                                                </td>
                                                                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                                                                    <div class="inline-flex items-center gap-1.5">
                                                                                        <button
                                                                                            type="button"
                                                                                            wire:click="startInlineEdit({{ $student->id }})"
                                                                                            class="px-2.5 py-1 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold"
                                                                                        >
                                                                                            {{ $editingStudentId === $student->id ? 'Tutup' : 'Edit' }}
                                                                                        </button>
                                                                                        <a
                                                                                            href="{{ route('report.brain-champion-card', $student->id) }}"
                                                                                            target="_blank"
                                                                                            class="px-2.5 py-1 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold"
                                                                                        >
                                                                                            Kartu
                                                                                        </a>
                                                                                        <button
                                                                                            type="button"
                                                                                            wire:click="deleteStudent({{ $student->id }})"
                                                                                            wire:confirm="Hapus siswa {{ $student->name }}?"
                                                                                            class="px-2 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold"
                                                                                        >
                                                                                            Hapus
                                                                                        </button>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>

                                                                            {{-- Inline Edit Accordion (Desktop) --}}
                                                                            @if ($editingStudentId === $student->id)
                                                                                <tr class="bg-slate-50 dark:bg-slate-800/60">
                                                                                    <td colspan="8" class="p-4">
                                                                                        @include('filament.admin.pages.partials.external-student-inline-edit', ['student' => $student])
                                                                                    </td>
                                                                                </tr>
                                                                            @endif
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            {{-- Mobile Key-Value Cards --}}
                                                            <div class="md:hidden p-2.5 space-y-2.5 bg-slate-50 dark:bg-slate-950/40">
                                                                @foreach ($students as $i => $student)
                                                                    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-3 shadow-xs text-xs">
                                                                        <div class="divide-y divide-dashed divide-slate-200 dark:divide-slate-800">
                                                                            <div class="flex justify-between py-1.5"><span class="text-slate-500 font-semibold">No</span><span class="font-bold">{{ $i + 1 }}</span></div>
                                                                            <div class="flex justify-between py-1.5"><span class="text-slate-500 font-semibold">Username / Kode</span><span class="font-mono">{{ $student->user?->username ?: ($student->competition_code ?: $student->nis) }}</span></div>
                                                                            <div class="flex justify-between py-1.5"><span class="text-slate-500 font-semibold">Nama</span><span class="font-bold text-slate-900 dark:text-white text-right">{{ $student->name }}</span></div>
                                                                            <div class="flex justify-between py-1.5"><span class="text-slate-500 font-semibold">NISN</span><span>{{ $student->nisn ?: '-' }}</span></div>
                                                                            <div class="flex justify-between py-1.5"><span class="text-slate-500 font-semibold">No HP</span><span class="text-emerald-600 font-semibold">{{ $student->phone ?: '-' }}</span></div>
                                                                            <div class="flex justify-between py-1.5"><span class="text-slate-500 font-semibold">Orang Tua</span><span>{{ $student->parent_name ?: ($student->father_name ?: ($student->mother_name ?: '-')) }}</span></div>
                                                                            <div class="flex justify-between py-1.5"><span class="text-slate-500 font-semibold">HP Ortu</span><span class="text-emerald-600 font-semibold">{{ $student->parent_phone ?: ($student->father_phone ?: ($student->mother_phone ?: '-')) }}</span></div>
                                                                        </div>
                                                                        <div class="flex justify-end gap-1.5 pt-2.5 mt-2 border-t border-slate-200 dark:border-slate-800">
                                                                            <button
                                                                                type="button"
                                                                                wire:click="startInlineEdit({{ $student->id }})"
                                                                                class="px-3 py-1.5 rounded-lg bg-amber-500 text-white font-semibold"
                                                                            >
                                                                                {{ $editingStudentId === $student->id ? 'Tutup' : 'Edit' }}
                                                                            </button>
                                                                            <a
                                                                                href="{{ route('report.brain-champion-card', $student->id) }}"
                                                                                target="_blank"
                                                                                class="px-3 py-1.5 rounded-lg bg-blue-600 text-white font-semibold"
                                                                            >
                                                                                Kartu
                                                                            </a>
                                                                            <button
                                                                                type="button"
                                                                                wire:click="deleteStudent({{ $student->id }})"
                                                                                wire:confirm="Hapus siswa {{ $student->name }}?"
                                                                                class="px-3 py-1.5 rounded-lg bg-rose-600 text-white font-semibold"
                                                                            >
                                                                                Hapus
                                                                            </button>
                                                                        </div>

                                                                        @if ($editingStudentId === $student->id)
                                                                            <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                                                                                @include('filament.admin.pages.partials.external-student-inline-edit', ['student' => $student])
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
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
