<x-filament-panels::page>
    <div class="gx-wrap">
        <div class="gx-toolbar">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Cari nama / NIS / NISN / jenjang / HP / orang tua..." class="gx-search" />
            <div class="gx-badges">
                <span class="gx-pill gx-pill-primary">Program: Survival English</span>
                <span class="gx-pill gx-pill-primary">Total: {{ $this->groupedStudents['total'] }}</span>
                <span class="gx-pill gx-pill-gold">Aktif: {{ $this->groupedStudents['active'] }}</span>
            </div>
        </div>

        @foreach ($this->groupedStudents['groups'] as $className => $students)
            @php
                $isOpen = in_array($className, $openGroups, true) || trim($search) !== '';
                $tag = str_replace('Survival English ', '', $className);
            @endphp
            <div class="gx-level-card">
                <button type="button" wire:click="toggleGroup(@js($className))" class="gx-level-btn">
                    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.5rem;">
                        <span class="gx-level-tag">{{ $tag }}</span>
                        <span>{{ $className }}</span>
                        <span class="gx-glass-pill">{{ count($students) }} siswa</span>
                    </div>
                    <svg class="gx-chevron {{ $isOpen ? 'gx-chevron-open' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                @if ($isOpen)
                    @if (empty($students))
                        <div style="padding:0.9rem 1.05rem;font-size:0.84rem;color:#64748b;background:#f8fafc;" class="dark:!bg-slate-900">
                            Belum ada siswa pada jenjang {{ $className }}.
                        </div>
                    @else
                        <div class="gx-table-wrap">
                            <table class="gx-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Siswa</th>
                                        <th>NIS / NISN</th>
                                        <th>No. HP</th>
                                        <th>Orang Tua / WA</th>
                                        <th>Status</th>
                                        <th style="text-align:right;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students as $i => $student)
                                        @php
                                            $avatarUrl = $this->photoUrl($student->photo);
                                            $initials = strtoupper(collect(explode(' ', trim($student->name)))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->join(''));
                                        @endphp
                                        <tr wire:click="startInlineEdit({{ $student->id }})" class="gx-row-click" title="Klik baris untuk edit biodata & foto siswa">
                                            <td class="gx-mono">{{ $i + 1 }}</td>
                                            <td>
                                                <div style="display:flex;align-items:center;gap:0.6rem;">
                                                    @if ($avatarUrl)
                                                        <img src="{{ $avatarUrl }}" alt="{{ $student->name }}" class="gx-avatar" />
                                                    @else
                                                        <span class="gx-avatar">{{ $initials ?: 'S' }}</span>
                                                    @endif
                                                    <div>
                                                        <div style="font-weight:700;color:#0a4282;" class="dark:!text-sky-300">
                                                            {{ $student->name }}
                                                            <span class="gx-pill gx-pill-muted" style="padding:0.06rem 0.42rem;margin-left:0.2rem;">{{ $student->gender ?: '-' }}</span>
                                                        </div>
                                                        <div style="font-size:0.74rem;color:#64748b;">{{ $student->school_name ?: 'Survival English' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="gx-mono">
                                                <div>{{ $student->nis ?: '-' }}</div>
                                                @if ($student->nisn)<div style="font-size:0.72rem;color:#64748b;">NISN: {{ $student->nisn }}</div>@endif
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
                                                <td colspan="7" style="background:#f8fafc;" class="dark:!bg-slate-900">
                                                    @include('filament.admin.pages.partials.internal-student-inline-edit', ['student' => $student])
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
                                    $avatarUrl = $this->photoUrl($student->photo);
                                    $initials = strtoupper(collect(explode(' ', trim($student->name)))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->join(''));
                                @endphp
                                <div class="gx-m-card">
                                    <div wire:click="startInlineEdit({{ $student->id }})" class="gx-row-click">
                                        <div style="display:flex;align-items:center;gap:0.65rem;padding-bottom:0.45rem;margin-bottom:0.2rem;border-bottom:1px dashed rgba(15,23,42,0.08);">
                                            @if ($avatarUrl)
                                                <img src="{{ $avatarUrl }}" alt="{{ $student->name }}" class="gx-avatar" />
                                            @else
                                                <span class="gx-avatar">{{ $initials ?: 'S' }}</span>
                                            @endif
                                            <div style="min-width:0;flex:1;">
                                                <div style="font-weight:700;color:#0a4282;font-size:0.88rem;" class="dark:!text-sky-300">{{ $student->name }}</div>
                                                <div class="gx-mono" style="color:#64748b;font-size:0.74rem;">{{ $student->nis ?: '-' }} • {{ $student->gender ?: '-' }}</div>
                                            </div>
                                            <span class="gx-pill {{ $student->is_active ? 'gx-pill-success' : 'gx-pill-danger' }}" style="padding:0.1rem 0.5rem;">
                                                {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </div>
                                        <div class="gx-kv"><span class="gx-kv-k">No. HP</span><span class="gx-kv-v gx-mono">{{ $student->phone ?: '-' }}</span></div>
                                        <div class="gx-kv"><span class="gx-kv-k">Orang Tua</span><span class="gx-kv-v">{{ $student->father_name ?: ($student->mother_name ?: '-') }}</span></div>
                                        <div class="gx-kv" style="border-bottom:none;"><span class="gx-kv-k">WA Ortu</span><span class="gx-kv-v gx-mono" style="color:#10b981;">{{ $student->father_phone ?: ($student->mother_phone ?: '-') }}</span></div>
                                    </div>
                                    <div class="gx-m-actions">
                                        <a href="{{ route('report.certificate', $student->id) }}" target="_blank" class="gx-btn gx-btn-card">Sertifikat</a>
                                        <button type="button" wire:click.stop="deleteRecord({{ $student->id }})" wire:confirm="Hapus siswa {{ $student->name }}?" class="gx-btn gx-btn-del">Hapus</button>
                                    </div>
                                    @if ($editingId === $student->id)
                                        @include('filament.admin.pages.partials.internal-student-inline-edit', ['student' => $student])
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
