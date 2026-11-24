<x-filament-panels::page>
    @php
        $batch = $this->batch;
        $drafts = $this->filteredDrafts;
        $allDrafts = $batch->drafts;
        $exams = $this->availableExams;
        $summary = is_array($batch->parse_summary) ? $batch->parse_summary : [];
    @endphp

    <style>
        .stg-shell {
            font-family: "Outfit", ui-sans-serif, system-ui, sans-serif;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }
        .stg-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.25rem 1.4rem;
            box-shadow: 0 4px 14px -4px rgba(15, 23, 42, 0.06);
        }
        .dark .stg-card {
            background: #1e293b;
            border-color: #334155;
        }
        .stg-kpi-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
        }
        @media (min-width: 768px) {
            .stg-kpi-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }
        .stg-kpi {
            border-radius: 0.85rem;
            padding: 0.85rem 1rem;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .dark .stg-kpi { background: #0f172a; border-color: #334155; }
        .stg-kpi-val { font-size: 1.6rem; font-weight: 900; line-height: 1.1; }
        .stg-kpi-lbl { font-size: 0.76rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b; margin-top: 0.2rem; }
        .dark .stg-kpi-lbl { color: #94a3b8; }

        .stg-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.85rem;
            align-items: flex-end;
            justify-content: space-between;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed #cbd5e1;
        }
        .dark .stg-toolbar { border-color: #334155; }

        .stg-input, .stg-select, .stg-textarea {
            width: 100%;
            border: 1.5px solid #cbd5e1;
            border-radius: 0.65rem;
            padding: 0.55rem 0.8rem;
            font-size: 0.9rem;
            background: #ffffff;
            color: #0f172a;
        }
        .dark .stg-input, .dark .stg-select, .dark .stg-textarea {
            background: #0f172a;
            border-color: #475569;
            color: #f8fafc;
        }
        .stg-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.58rem 1.05rem;
            border-radius: 0.65rem;
            font-size: 0.84rem;
            font-weight: 700;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.15s ease;
        }
        .stg-btn:disabled { opacity: 0.45; cursor: not-allowed; }
        .stg-btn-primary { background: linear-gradient(135deg, #0a4282, #1a368b); color: #ffffff; }
        .stg-btn-primary:hover:not(:disabled) { filter: brightness(1.08); }
        .stg-btn-success { background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; }
        .stg-btn-success:hover:not(:disabled) { filter: brightness(1.08); }
        .stg-btn-amber { background: #fbb900; color: #0a4282; }
        .stg-btn-ghost { background: #f8fafc; color: #334155; border-color: #cbd5e1; }
        .dark .stg-btn-ghost { background: #0f172a; color: #e2e8f0; border-color: #475569; }
        .stg-btn-danger { background: rgba(244, 63, 94, 0.1); color: #e11d48; border-color: rgba(244, 63, 94, 0.25); }

        .stg-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.22rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.73rem;
            font-weight: 800;
        }
        .stg-pill-navy { background: rgba(10, 66, 130, 0.12); color: #0a4282; }
        .dark .stg-pill-navy { background: rgba(251, 185, 0, 0.18); color: #fbb900; }
        .stg-pill-green { background: rgba(16, 185, 129, 0.14); color: #059669; }
        .stg-pill-amber { background: rgba(245, 158, 11, 0.18); color: #b45309; }
        .dark .stg-pill-amber { color: #fbbf24; }
        .stg-pill-red { background: rgba(244, 63, 94, 0.14); color: #e11d48; }
        .stg-pill-sky { background: rgba(14, 165, 233, 0.14); color: #0284c7; }

        .stg-filter-bar { display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; }
        .stg-filter-btn {
            padding: 0.4rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 700;
            border: 1.5px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            cursor: pointer;
        }
        .dark .stg-filter-btn { background: #1e293b; border-color: #475569; color: #cbd5e1; }
        .stg-filter-btn.active { background: #0a4282; border-color: #0a4282; color: #ffffff; }
        .dark .stg-filter-btn.active { background: #fbb900; border-color: #fbb900; color: #0a4282; }

        .stg-q-card {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 12px -4px rgba(15, 23, 42, 0.05);
        }
        .dark .stg-q-card { background: #1e293b; border-color: #334155; }
        .stg-q-card.is-warning { border-color: #f59e0b; }
        .stg-q-card.is-error { border-color: #f43f5e; }
        .stg-q-head {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.65rem;
            padding: 0.85rem 1.25rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .dark .stg-q-head { background: #0f172a; border-color: #334155; }
        .stg-q-body { padding: 1.2rem 1.35rem; }

        .stg-opt-row {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.6rem 0.9rem;
            border-radius: 0.7rem;
            border: 1.5px solid #e2e8f0;
            background: #ffffff;
            margin-bottom: 0.45rem;
        }
        .dark .stg-opt-row { background: #0f172a; border-color: #334155; }
        .stg-opt-row.is-key { border-color: #10b981; background: rgba(16, 185, 129, 0.08); }
        .stg-opt-badge {
            width: 1.75rem;
            height: 1.75rem;
            border-radius: 9999px;
            background: #f1f5f9;
            color: #0a4282;
            font-weight: 900;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .stg-opt-row.is-key .stg-opt-badge { background: #10b981; color: #ffffff; }

        .stg-modal-bg {
            position: fixed;
            inset: 0;
            z-index: 60;
            background: rgba(15, 23, 42, 0.72);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            overflow-y: auto;
        }
        .stg-modal {
            width: 100%;
            max-width: 920px;
            max-height: 92vh;
            overflow-y: auto;
            background: #ffffff;
            border-radius: 1.15rem;
            border: 1px solid #cbd5e1;
            padding: 1.4rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
        }
        .dark .stg-modal { background: #1e293b; border-color: #475569; }
        .stg-paste-zone {
            border: 2px dashed #0a4282;
            border-radius: 0.9rem;
            padding: 1.5rem;
            text-align: center;
            background: rgba(10, 66, 130, 0.04);
            cursor: pointer;
        }
        .dark .stg-paste-zone { border-color: #fbb900; background: rgba(251, 185, 0, 0.06); }
    </style>

    <div class="stg-shell"
         x-data="{
            init() {
                this.$nextTick(() => window.renderCbtMath(this.$el));
                window.addEventListener('cbt-math-refresh', () => {
                    setTimeout(() => window.renderCbtMath(this.$el), 80);
                });
            }
         }">

        {{-- Top Summary & Publish Card --}}
        <div class="stg-card">
            <div style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:0.75rem;margin-bottom:1rem;">
                <div>
                    <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
                        <span class="stg-pill stg-pill-navy">Staging Preview (.docx)</span>
                        @if($batch->status === 'published')
                            <span class="stg-pill stg-pill-green">Sudah Dipublish {{ $batch->published_at?->format('d M Y H:i') }}</span>
                        @else
                            <span class="stg-pill stg-pill-amber">Draft — Belum Dipublish</span>
                        @endif
                    </div>
                    <p style="margin:0.35rem 0 0 0;font-size:0.84rem;color:#64748b;">
                        File: <strong>{{ $batch->original_filename }}</strong>
                        @if(!empty($summary['media_extracted']))
                            &bull; {{ $summary['media_extracted'] }} gambar terekstrak otomatis
                        @endif
                        &bull; Kunci jawaban bersifat <strong>opsional</strong> (untuk penilaian Admin / IRT).
                    </p>
                </div>
                <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                    <button type="button" wire:click="$toggle('showKeyDrawer')" class="stg-btn stg-btn-ghost">
                        Kunci Jawaban (Opsional)
                    </button>
                    <button type="button" wire:click="addNewDraft" class="stg-btn stg-btn-ghost">
                        + Tambah Soal
                    </button>
                    <a href="{{ \App\Filament\Resources\Academic\CbtImportBatches\CbtImportBatchResource::getUrl('index') }}" class="stg-btn stg-btn-ghost">
                        Kembali ke Daftar Batch
                    </a>
                </div>
            </div>

            <div class="stg-kpi-grid">
                <div class="stg-kpi">
                    <div class="stg-kpi-val" style="color:#0a4282;" class="dark:text-sky-300">{{ $batch->total_questions }}</div>
                    <div class="stg-kpi-lbl">Total Soal Draft</div>
                </div>
                <div class="stg-kpi">
                    <div class="stg-kpi-val" style="color:#059669;">{{ $batch->valid_count }}</div>
                    <div class="stg-kpi-lbl">Siap Publish</div>
                </div>
                <div class="stg-kpi">
                    <div class="stg-kpi-val" style="color:#d97706;">{{ $batch->warning_count }}</div>
                    <div class="stg-kpi-lbl">Perlu Cek / Diagram</div>
                </div>
                <div class="stg-kpi">
                    <div class="stg-kpi-val" style="color:#e11d48;">{{ $batch->error_count }}</div>
                    <div class="stg-kpi-lbl">Error (Blokir Publish)</div>
                </div>
            </div>

            {{-- Publish Controls --}}
            <div class="stg-toolbar">
                <div style="flex:1;min-width:240px;">
                    <label style="display:block;font-size:0.78rem;font-weight:800;margin-bottom:0.3rem;color:#475569;" class="dark:text-slate-300">
                        Target Ujian CBT (Mode Real CBT)
                    </label>
                    <select wire:model="targetExamId" class="stg-select">
                        <option value="">-- Pilih Ujian CBT Target --</option>
                        @foreach($exams as $ex)
                            <option value="{{ $ex->id }}">
                                {{ $ex->title }} ({{ $ex->classRoom?->name ?? '-' }} • {{ $ex->isDeferredScoring() ? 'Proses Admin/IRT' : 'Nilai Otomatis' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="min-width:210px;">
                    <label style="display:block;font-size:0.78rem;font-weight:800;margin-bottom:0.3rem;color:#475569;" class="dark:text-slate-300">
                        Mode Publish
                    </label>
                    <select wire:model="publishMode" class="stg-select">
                        <option value="append">Tambahkan ke soal existing</option>
                        <option value="replace">Ganti semua soal di ujian ini</option>
                    </select>
                </div>

                <div>
                    <button type="button"
                            wire:click="publishBatch"
                            wire:loading.attr="disabled"
                            @if($batch->error_count > 0 || $batch->total_questions === 0) disabled @endif
                            class="stg-btn stg-btn-success"
                            style="padding:0.65rem 1.35rem;font-size:0.9rem;">
                        <span wire:loading.remove wire:target="publishBatch">Publish {{ $batch->total_questions }} Soal ke Bank Soal CBT</span>
                        <span wire:loading wire:target="publishBatch">Mempublish...</span>
                    </button>
                </div>
            </div>

            {{-- Optional Quick Answer Key Drawer --}}
            @if($showKeyDrawer)
                <div style="margin-top:1rem;padding:1rem;border-radius:0.85rem;background:rgba(10,66,130,0.05);border:1px solid rgba(10,66,130,0.18);">
                    <div style="font-weight:800;font-size:0.86rem;margin-bottom:0.35rem;">Input Cepat Kunci Jawaban PG (Opsional)</div>
                    <p style="margin:0 0 0.65rem 0;font-size:0.8rem;color:#64748b;">
                        Kosongkan saja jika ujian memakai sistem penilaian <strong>Proses Admin / IRT</strong>. Jika ingin mengisi kunci, paste format <code>1:B 2:D 3:A ...</code> atau deretan huruf <code>B D A C ...</code> lalu klik Terapkan.
                    </p>
                    <div style="display:flex;gap:0.6rem;flex-wrap:wrap;">
                        <input type="text" wire:model="bulkKeyInput" placeholder="Contoh: 1:B 2:D 3:A 4:C atau B D A C E..." class="stg-input" style="flex:1;min-width:260px;">
                        <button type="button" wire:click="applyBulkKeys" class="stg-btn stg-btn-primary">Terapkan Kunci</button>
                    </div>
                </div>
            @endif
        </div>

        {{-- Filter Bar --}}
        <div class="stg-filter-bar">
            <button type="button" wire:click="setFilter('all')" class="stg-filter-btn {{ $activeFilter === 'all' ? 'active' : '' }}">
                Semua ({{ $batch->total_questions }})
            </button>
            <button type="button" wire:click="setFilter('warning')" class="stg-filter-btn {{ $activeFilter === 'warning' ? 'active' : '' }}">
                Perlu Cek / Diagram ({{ $batch->warning_count }})
            </button>
            <button type="button" wire:click="setFilter('error')" class="stg-filter-btn {{ $activeFilter === 'error' ? 'active' : '' }}">
                Error ({{ $batch->error_count }})
            </button>
            <button type="button" wire:click="setFilter('mcq')" class="stg-filter-btn {{ $activeFilter === 'mcq' ? 'active' : '' }}">
                PG A–E ({{ $allDrafts->where('question_type', 'mcq')->count() }})
            </button>
            <button type="button" wire:click="setFilter('complex_tf')" class="stg-filter-btn {{ $activeFilter === 'complex_tf' ? 'active' : '' }}">
                PG Kompleks B/S ({{ $allDrafts->where('question_type', 'complex_tf')->count() }})
            </button>
            <button type="button" wire:click="setFilter('short_answer')" class="stg-filter-btn {{ $activeFilter === 'short_answer' ? 'active' : '' }}">
                Isian Singkat ({{ $allDrafts->where('question_type', 'short_answer')->count() }})
            </button>
        </div>

        {{-- Visual Question Cards --}}
        @forelse($drafts as $draft)
            <div wire:key="draft-card-{{ $draft->id }}"
                 class="stg-q-card {{ $draft->validation_status === 'warning' ? 'is-warning' : ($draft->validation_status === 'error' ? 'is-error' : '') }}">

                <div class="stg-q-head">
                    <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
                        <span class="stg-pill stg-pill-navy">Soal #{{ $draft->sequence_no }}</span>
                        <span class="stg-pill stg-pill-sky">
                            @if($draft->question_type === 'complex_tf')
                                PG Kompleks (Benar/Salah)
                            @elseif($draft->question_type === 'short_answer')
                                Isian Singkat
                            @else
                                Pilihan Ganda (A–E)
                            @endif
                        </span>
                        @if($draft->validation_status === 'valid')
                            <span class="stg-pill stg-pill-green">Siap Publish</span>
                        @elseif($draft->validation_status === 'warning')
                            <span class="stg-pill stg-pill-amber">Perlu Cek</span>
                        @else
                            <span class="stg-pill stg-pill-red">Error</span>
                        @endif
                        @if($draft->correct_answer)
                            <span class="stg-pill stg-pill-green">Kunci: {{ $draft->correct_answer }}</span>
                        @else
                            <span class="stg-pill" style="background:rgba(148,163,184,0.18);color:#64748b;">Tanpa Kunci (Proses Admin/IRT)</span>
                        @endif
                    </div>

                    <div style="display:flex;gap:0.45rem;flex-wrap:wrap;">
                        <button type="button" wire:click="openImageModal({{ $draft->id }})" class="stg-btn stg-btn-amber" style="padding:0.38rem 0.75rem;font-size:0.78rem;">
                            Paste / Upload Gambar
                        </button>
                        @if($draft->validation_status === 'warning')
                            <button type="button" wire:click="clearDraftWarning({{ $draft->id }})" class="stg-btn stg-btn-ghost" style="padding:0.38rem 0.75rem;font-size:0.78rem;">
                                Tandai Oke
                            </button>
                        @endif
                        <button type="button" wire:click="openEditModal({{ $draft->id }})" class="stg-btn stg-btn-primary" style="padding:0.38rem 0.8rem;font-size:0.78rem;">
                            Edit Soal
                        </button>
                        <button type="button"
                                wire:click="deleteDraft({{ $draft->id }})"
                                wire:confirm="Hapus draft Soal #{{ $draft->sequence_no }}?"
                                class="stg-btn stg-btn-danger"
                                style="padding:0.38rem 0.7rem;font-size:0.78rem;">
                            Hapus
                        </button>
                    </div>
                </div>

                <div class="stg-q-body">
                    {{-- Issues / Warnings Banner --}}
                    @if(!empty($draft->validation_issues))
                        <div style="margin-bottom:0.9rem;padding:0.7rem 0.95rem;border-radius:0.7rem;font-size:0.82rem;font-weight:600;{{ $draft->validation_status === 'error' ? 'background:rgba(244,63,94,0.1);border:1px solid rgba(244,63,94,0.3);color:#be123c;' : 'background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.35);color:#92400e;' }}">
                            @foreach($draft->validation_issues as $issue)
                                <div>&bull; {{ $issue }}</div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Visual Question Body (Matches Student CBT) --}}
                    <div class="cbt-rich-content" data-cbt-math style="font-size:1rem;line-height:1.68;margin-bottom:1rem;">
                        {!! $draft->question_html !!}
                    </div>

                    {{-- MCQ Options --}}
                    @if($draft->question_type === 'mcq')
                        <div data-cbt-math>
                            @foreach(['A' => $draft->option_a, 'B' => $draft->option_b, 'C' => $draft->option_c, 'D' => $draft->option_d, 'E' => $draft->option_e] as $letter => $optHtml)
                                @if($optHtml !== null && trim($optHtml) !== '')
                                    <div class="stg-opt-row {{ strtoupper((string) $draft->correct_answer) === $letter ? 'is-key' : '' }}">
                                        <button type="button"
                                                title="Klik untuk tandai/batal kunci {{ $letter }} (opsional)"
                                                wire:click="setQuickKey({{ $draft->id }}, '{{ strtoupper((string) $draft->correct_answer) === $letter ? '' : $letter }}')"
                                                class="stg-opt-badge"
                                                style="border:none;cursor:pointer;">
                                            {{ $letter }}
                                        </button>
                                        <div class="cbt-rich-content" style="flex:1;font-size:0.95rem;">
                                            {!! $optHtml !!}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                    {{-- Complex True/False Statements Table --}}
                    @elseif($draft->question_type === 'complex_tf')
                        <div class="cbt-rich-content" data-cbt-math>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width:48px;">No</th>
                                        <th style="text-align:left;">Pernyataan</th>
                                        <th style="width:88px;">Benar</th>
                                        <th style="width:88px;">Salah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(($draft->statements ?? []) as $stmt)
                                        <tr>
                                            <td style="text-align:center;font-weight:700;">{{ $stmt['no'] ?? $loop->iteration }}</td>
                                            <td>{!! $stmt['text'] ?? '' !!}</td>
                                            <td style="text-align:center;">
                                                <span style="display:inline-block;width:18px;height:18px;border-radius:9999px;border:2px solid {{ ($stmt['answer'] ?? '') === 'B' ? '#10b981' : '#94a3b8' }};background:{{ ($stmt['answer'] ?? '') === 'B' ? '#10b981' : 'transparent' }};"></span>
                                            </td>
                                            <td style="text-align:center;">
                                                <span style="display:inline-block;width:18px;height:18px;border-radius:9999px;border:2px solid {{ ($stmt['answer'] ?? '') === 'S' ? '#e11d48' : '#94a3b8' }};background:{{ ($stmt['answer'] ?? '') === 'S' ? '#e11d48' : 'transparent' }};"></span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    {{-- Short Answer --}}
                    @elseif($draft->question_type === 'short_answer')
                        <div style="padding:0.75rem 1rem;border-radius:0.75rem;border:1.5px dashed #94a3b8;background:#f8fafc;font-size:0.86rem;color:#475569;" class="dark:bg-slate-900 dark:text-slate-300">
                            Kotak Isian Singkat Siswa (Numerik / Teks)
                            @if($draft->correct_answer)
                                &mdash; Kunci: <strong>{{ $draft->correct_answer }}</strong>
                            @endif
                        </div>
                    @endif

                    @if($draft->explanation_html)
                        <div style="margin-top:0.85rem;padding:0.75rem 1rem;border-radius:0.75rem;background:rgba(14,165,233,0.08);border:1px solid rgba(14,165,233,0.22);font-size:0.86rem;" class="cbt-rich-content" data-cbt-math>
                            <div style="font-weight:800;color:#0284c7;margin-bottom:0.25rem;">Pembahasan:</div>
                            {!! $draft->explanation_html !!}
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="stg-card" style="text-align:center;padding:2.5rem;color:#64748b;">
                Tidak ada soal draft pada filter ini.
            </div>
        @endforelse

        {{-- =========================================================== --}}
        {{-- MODAL 1: EDIT DRAFT WITH LIVE KATEX PREVIEW                 --}}
        {{-- =========================================================== --}}
        @if($showEditModal)
            <div class="stg-modal-bg">
                <div class="stg-modal"
                     x-data="{
                        previewHtml: @js($editQuestionHtml),
                        refreshPreview() {
                            this.$nextTick(() => window.renderCbtMath(this.$refs.livePreview));
                        }
                     }"
                     x-init="refreshPreview()">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                        <h3 style="margin:0;font-size:1.15rem;font-weight:900;">Edit Draft Soal</h3>
                        <button type="button" wire:click="$set('showEditModal', false)" class="stg-btn stg-btn-ghost" style="padding:0.35rem 0.7rem;">Tutup</button>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:0.85rem;margin-bottom:0.9rem;">
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:800;margin-bottom:0.25rem;">Tipe Soal</label>
                            <select wire:model.live="editQuestionType" class="stg-select">
                                <option value="mcq">Pilihan Ganda (A–E)</option>
                                <option value="complex_tf">PG Kompleks (Tabel Benar/Salah)</option>
                                <option value="short_answer">Isian Singkat (Numerik/Teks)</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:800;margin-bottom:0.25rem;">Kunci Jawaban (Opsional — kosongkan utk IRT)</label>
                            <input type="text" wire:model="editCorrectAnswer" placeholder="Kosong / A–E / angka" class="stg-input">
                        </div>
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:800;margin-bottom:0.25rem;">Poin</label>
                            <input type="number" min="1" wire:model="editPoints" class="stg-input">
                        </div>
                    </div>

                    <div style="margin-bottom:0.9rem;">
                        <label style="display:block;font-size:0.78rem;font-weight:800;margin-bottom:0.25rem;">
                            HTML &amp; LaTeX Soal (gunakan <code>\(...\)</code> untuk rumus inline, <code>\[...\]</code> untuk rumus blok)
                        </label>
                        <textarea rows="6"
                                  wire:model="editQuestionHtml"
                                  x-model="previewHtml"
                                  @input="refreshPreview()"
                                  class="stg-textarea"
                                  style="font-family:monospace;font-size:0.84rem;"></textarea>
                    </div>

                    <div style="margin-bottom:1rem;padding:0.9rem 1rem;border-radius:0.8rem;border:1.5px solid #cbd5e1;background:#f8fafc;" class="dark:bg-slate-900 dark:border-slate-700">
                        <div style="font-size:0.72rem;font-weight:800;text-transform:uppercase;color:#64748b;margin-bottom:0.4rem;">Live Preview KaTeX &amp; HTML</div>
                        <div x-ref="livePreview" class="cbt-rich-content" x-html="previewHtml"></div>
                    </div>

                    @if($editQuestionType === 'mcq')
                        <div style="display:grid;grid-template-columns:1fr;gap:0.55rem;margin-bottom:1rem;">
                            @foreach(['A' => 'editOptionA', 'B' => 'editOptionB', 'C' => 'editOptionC', 'D' => 'editOptionD', 'E' => 'editOptionE'] as $lbl => $prop)
                                <div style="display:flex;align-items:center;gap:0.55rem;">
                                    <span class="stg-opt-badge">{{ $lbl }}</span>
                                    <input type="text" wire:model="{{ $prop }}" class="stg-input" placeholder="Opsi {{ $lbl }} (mendukung HTML &amp; \(LaTeX\))">
                                </div>
                            @endforeach
                        </div>
                    @elseif($editQuestionType === 'complex_tf')
                        <div style="margin-bottom:1rem;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
                                <label style="font-size:0.82rem;font-weight:800;">Daftar Pernyataan (Benar / Salah)</label>
                                <button type="button" wire:click="addStatementRow" class="stg-btn stg-btn-ghost" style="padding:0.3rem 0.7rem;font-size:0.76rem;">+ Tambah Baris</button>
                            </div>
                            @foreach($editStatements as $i => $row)
                                <div style="display:flex;gap:0.5rem;align-items:center;margin-bottom:0.45rem;">
                                    <span class="stg-opt-badge">{{ $i + 1 }}</span>
                                    <input type="text" wire:model="editStatements.{{ $i }}.text" class="stg-input" placeholder="Teks pernyataan..." style="flex:1;">
                                    <select wire:model="editStatements.{{ $i }}.answer" class="stg-select" style="width:130px;">
                                        <option value="">Kunci (Kosong)</option>
                                        <option value="B">Benar</option>
                                        <option value="S">Salah</option>
                                    </select>
                                    <button type="button" wire:click="removeStatementRow({{ $i }})" class="stg-btn stg-btn-danger" style="padding:0.45rem 0.65rem;">&times;</button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div style="margin-bottom:0.85rem;">
                        <label style="display:block;font-size:0.78rem;font-weight:800;margin-bottom:0.25rem;">Pembahasan (Opsional)</label>
                        <textarea rows="3" wire:model="editExplanationHtml" class="stg-textarea"></textarea>
                    </div>

                    <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.84rem;font-weight:700;margin-bottom:1.1rem;cursor:pointer;">
                        <input type="checkbox" wire:model="editClearWarnings">
                        <span>Tandai warning diagram/parser pada soal ini sudah selesai diperiksa</span>
                    </label>

                    <div style="display:flex;justify-content:flex-end;gap:0.65rem;">
                        <button type="button" wire:click="$set('showEditModal', false)" class="stg-btn stg-btn-ghost">Batal</button>
                        <button type="button" wire:click="saveEditDraft" class="stg-btn stg-btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </div>
        @endif

        {{-- =========================================================== --}}
        {{-- MODAL 2: PASTE (CTRL+V) / UPLOAD IMAGE + APPLY TO MULTIPLE  --}}
        {{-- =========================================================== --}}
        @if($showImageModal)
            <div class="stg-modal-bg">
                <div class="stg-modal" style="max-width:640px;"
                     x-data="{
                        previewSrc: '',
                        uploading: false,
                        handlePaste(e) {
                            const items = (e.clipboardData || window.clipboardData)?.items || [];
                            for (const item of items) {
                                if (item.type && item.type.startsWith('image/')) {
                                    const file = item.getAsFile();
                                    this.readFile(file);
                                    e.preventDefault();
                                    return;
                                }
                            }
                        },
                        handleFile(e) {
                            const file = e.target.files?.[0];
                            if (file) this.readFile(file);
                        },
                        readFile(file) {
                            const reader = new FileReader();
                            reader.onload = (ev) => { this.previewSrc = ev.target.result; };
                            reader.readAsDataURL(file);
                        },
                        submitImage() {
                            if (!this.previewSrc || this.uploading) return;
                            this.uploading = true;
                            $wire.attachPastedImage(this.previewSrc).finally(() => { this.uploading = false; });
                        }
                     }"
                     @paste.window="handlePaste($event)">

                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.85rem;">
                        <h3 style="margin:0;font-size:1.1rem;font-weight:900;">Paste (Ctrl+V) / Upload Gambar Diagram</h3>
                        <button type="button" wire:click="$set('showImageModal', false)" class="stg-btn stg-btn-ghost" style="padding:0.35rem 0.7rem;">Tutup</button>
                    </div>

                    <p style="margin:0 0 0.9rem 0;font-size:0.84rem;color:#64748b;">
                        Sangat berguna untuk diagram alir / Word Shapes (seperti Soal #13–16). Cukup screenshot dari PDF/Word lalu tekan <strong>Ctrl+V</strong> di sini, atau pilih file gambar.
                    </p>

                    <div class="stg-paste-zone" @click="$refs.fileInput.click()">
                        <input type="file" x-ref="fileInput" accept="image/png,image/jpeg,image/webp,image/gif" class="hidden" style="display:none;" @change="handleFile($event)">
                        <template x-if="!previewSrc">
                            <div>
                                <div style="font-weight:800;font-size:0.95rem;color:#0a4282;" class="dark:text-amber-400">
                                    Tekan Ctrl+V untuk Paste Screenshot — atau Klik untuk Pilih File PNG/JPG
                                </div>
                                <div style="font-size:0.78rem;color:#64748b;margin-top:0.25rem;">Maks. 8 MB &bull; PNG, JPG, WebP</div>
                            </div>
                        </template>
                        <template x-if="previewSrc">
                            <div>
                                <img :src="previewSrc" alt="Preview" style="max-height:240px;margin:0 auto 0.5rem auto;border-radius:0.65rem;">
                                <div style="font-size:0.78rem;color:#059669;font-weight:700;">Gambar siap dipasang! Klik lagi untuk ganti.</div>
                            </div>
                        </template>
                    </div>

                    <div style="margin-top:1rem;">
                        <label style="display:block;font-size:0.78rem;font-weight:800;margin-bottom:0.3rem;">Posisi Gambar di Soal</label>
                        <select wire:model="imagePosition" class="stg-select">
                            <option value="after">Sisipkan di bawah teks/tabel soal</option>
                            <option value="before">Sisipkan di atas teks soal</option>
                            <option value="replace_imgs">Ganti gambar lama pada soal</option>
                        </select>
                    </div>

                    <div style="margin-top:1rem;">
                        <label style="display:block;font-size:0.78rem;font-weight:800;margin-bottom:0.4rem;">
                            Terapkan Gambar yang Sama ke Soal Nomor (bisa pilih banyak sekaligus, mis. #13–#16):
                        </label>
                        <div style="display:flex;flex-wrap:wrap;gap:0.4rem;max-height:140px;overflow-y:auto;padding:0.5rem;border:1px solid #cbd5e1;border-radius:0.65rem;">
                            @foreach($allDrafts as $d)
                                <label style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.25rem 0.55rem;border-radius:0.5rem;border:1px solid #cbd5e1;font-size:0.8rem;font-weight:700;cursor:pointer;">
                                    <input type="checkbox" value="{{ $d->id }}" wire:model="imageApplyDraftIds">
                                    <span>#{{ $d->sequence_no }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div style="display:flex;justify-content:flex-end;gap:0.65rem;margin-top:1.15rem;">
                        <button type="button" wire:click="$set('showImageModal', false)" class="stg-btn stg-btn-ghost">Batal</button>
                        <button type="button" @click="submitImage()" :disabled="!previewSrc || uploading" class="stg-btn stg-btn-primary">
                            <span x-text="uploading ? 'Menyimpan...' : 'Pasang Gambar ke Soal Terpilih'"></span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
