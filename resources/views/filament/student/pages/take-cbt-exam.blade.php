<x-filament-panels::page>
    <style>
        /* Base Reset & Variables for CBT (Aligned with DESIGN_SYSTEM.md & exam.css) */
        .cbt-container {
            font-family: "Outfit", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #1f2937;
            line-height: 1.5;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        .dark .cbt-container {
            color: #f3f4f6;
        }

        /* Glass Panel (DESIGN_SYSTEM.md Rule 3 & 6) */
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px) saturate(180%);
            -webkit-backdrop-filter: blur(12px) saturate(180%);
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 1.25rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .dark .glass-panel {
            background: rgba(30, 41, 59, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
        }

        /* Grid Layouts */
        .cbt-grid {
            display: grid;
            gap: 1.5rem;
        }
        .cbt-grid-cols-4 {
            grid-template-columns: 1fr;
        }
        @media (min-width: 1024px) {
            .cbt-grid-cols-4 {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
            .cbt-col-span-3 {
                grid-column: span 3 / span 3;
            }
            .cbt-col-span-1 {
                grid-column: span 1 / span 1;
            }
        }
        .cbt-flex-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .cbt-flex-center {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Pill Buttons (DESIGN_SYSTEM.md Rule 3) */
        .cbt-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.875rem 1.75rem;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.25s ease-in-out;
            border: none;
            text-decoration: none;
        }
        .cbt-btn-primary {
            background: linear-gradient(135deg, #0a4282, #1a368b);
            color: white;
            box-shadow: 0 4px 15px -3px rgba(10, 66, 130, 0.4);
        }
        .cbt-btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -5px rgba(10, 66, 130, 0.55);
        }
        .cbt-btn-gold {
            background: #fbb900;
            color: #0a4282;
            box-shadow: 0 4px 14px 0 rgba(251, 185, 0, 0.35);
        }
        .cbt-btn-gold:hover:not(:disabled) {
            transform: translateY(-2px);
            background: #f59e0b;
            color: #ffffff;
        }
        .cbt-btn-secondary {
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        .dark .cbt-btn-secondary {
            background: #1e293b;
            color: #cbd5e1;
            border: 1px solid #334155;
        }
        .cbt-btn-secondary:hover:not(:disabled) {
            background: #f8fafc;
            transform: translateY(-2px);
        }
        .dark .cbt-btn-secondary:hover:not(:disabled) {
            background: #334155;
        }
        .cbt-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }
        .cbt-btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 4px 15px -3px rgba(16, 185, 129, 0.4);
        }
        .cbt-btn-success:hover:not(:disabled) {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px);
        }

        /* Typography & Header */
        .cbt-title {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: #0a4282;
        }
        .dark .cbt-title { color: #f8fafc; }

        .cbt-subtitle {
            font-size: 1rem;
            color: #64748b;
            margin-bottom: 1.5rem;
        }
        .dark .cbt-subtitle { color: #94a3b8; }

        .cbt-question-text {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 2rem;
            color: #1e293b;
            line-height: 1.6;
        }
        .dark .cbt-question-text { color: #f1f5f9; }

        /* Options */
        .cbt-options-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .cbt-option-label {
            display: flex;
            align-items: center;
            padding: 1.15rem 1.25rem;
            border-radius: 1rem;
            border: 2px solid #e2e8f0;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }
        .dark .cbt-option-label {
            border-color: #334155;
            background: #1e293b;
        }
        .cbt-option-label:hover {
            border-color: #38bdf8;
            background: #f0f9ff;
            transform: translateX(4px);
        }
        .dark .cbt-option-label:hover {
            border-color: #38bdf8;
            background: rgba(14, 165, 233, 0.12);
        }
        .cbt-option-label.selected {
            border-color: #0a4282;
            background: #eff6ff;
            box-shadow: 0 0 0 1px #0a4282;
        }
        .dark .cbt-option-label.selected {
            background: rgba(59, 130, 246, 0.2);
            border-color: #fbb900;
            box-shadow: 0 0 0 1px #fbb900;
        }
        .cbt-option-badge {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            border-radius: 0.65rem;
            margin-right: 1.25rem;
            flex-shrink: 0;
            font-size: 1.05rem;
        }
        .dark .cbt-option-badge {
            background: #0f172a;
            color: #cbd5e1;
        }
        .cbt-option-label.selected .cbt-option-badge {
            background: #0a4282;
            color: #ffffff;
        }
        .dark .cbt-option-label.selected .cbt-option-badge {
            background: #fbb900;
            color: #0a4282;
        }

        /* Timer Box (DESIGN_SYSTEM.md Monospace) */
        .cbt-timer {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.25rem;
            background: rgba(244, 63, 94, 0.1);
            color: #e11d48;
            border: 2px solid rgba(244, 63, 94, 0.25);
            border-radius: 9999px;
            font-family: ui-monospace, monospace;
            font-weight: 800;
            font-size: 1.25rem;
        }
        .dark .cbt-timer {
            background: rgba(244, 63, 94, 0.2);
            color: #FDA4AF;
            border-color: rgba(244, 63, 94, 0.35);
        }

        /* Navigation Grid Buttons (Aligned with exam.css data-status) */
        .cbt-nav-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .cbt-nav-btn {
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            font-weight: 700;
            font-size: 1rem;
            background: #ffffff;
            border: 2px solid #cbd5e1;
            color: #334155;
            cursor: pointer;
            transition: all 0.2s;
        }
        .dark .cbt-nav-btn {
            background: #1e293b;
            border-color: #334155;
            color: #cbd5e1;
        }
        .cbt-nav-btn:hover {
            transform: scale(1.05);
        }
        .cbt-nav-btn.answered {
            background: #0a4282;
            border-color: #0a4282;
            color: #ffffff;
        }
        .cbt-nav-btn.doubt {
            background: #fbb900;
            border-color: #fbb900;
            color: #0a4282;
        }
        .cbt-nav-btn.active {
            outline: 3px solid #0ea5e9;
            outline-offset: 2px;
            transform: scale(1.08);
            z-index: 10;
        }

        .cbt-legend-item {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            color: #475569;
        }
        .dark .cbt-legend-item { color: #94a3b8; }
        .cbt-dot {
            width: 0.75rem;
            height: 0.75rem;
            border-radius: 50%;
            margin-right: 0.5rem;
        }

        /* Google Form Iframe Wrapper */
        .gform-iframe-wrapper {
            width: 100%;
            min-height: 75vh;
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.08);
            background: #ffffff;
        }
        .dark .gform-iframe-wrapper {
            border-color: rgba(255, 255, 255, 0.12);
            background: #0f172a;
        }
        .gform-iframe-wrapper iframe {
            width: 100%;
            height: 75vh;
            border: 0;
        }
    </style>

    @php
        $mode = $exam->cbt_mode ?? 'google_form';
        $student = auth()->user()->student;
        $endTimeIso = $attempt && $attempt->start_time
            ? $attempt->start_time->copy()->addMinutes($exam->duration_minutes)->toIso8601String()
            : now()->addMinutes($exam->duration_minutes)->toIso8601String();
    @endphp

    <div class="cbt-container"
         @contextmenu.prevent
         @copy.prevent
         @cut.prevent
         @paste.prevent>

        {{-- ========================================================= --}}
        {{-- FINISHED STATE                                            --}}
        {{-- ========================================================= --}}
        @if($isFinished)
            <div class="glass-panel" style="text-align: center; max-width: 600px; margin: 3rem auto;">
                <div style="width: 5rem; height: 5rem; background: rgba(16, 185, 129, 0.15); color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
                    <x-heroicon-o-check-badge style="width: 3rem; height: 3rem;" />
                </div>
                <h2 class="cbt-title" style="margin-bottom: 0.5rem;">Ujian Selesai!</h2>
                <p class="cbt-subtitle">Jawaban ujian Anda telah berhasil disimpan.</p>

                @if($exam->show_result_immediately && $attempt && $attempt->score !== null)
                    <div style="background: rgba(10, 66, 130, 0.08); border: 2px solid #0a4282; border-radius: 1.25rem; padding: 2rem; margin: 2rem 0; display: inline-block; min-width: 220px;">
                        <p style="font-size: 0.875rem; color: #0ea5e9; text-transform: uppercase; font-weight: 700; margin-bottom: 0.5rem;">Skor Akhir</p>
                        <p style="font-size: 4rem; font-weight: 900; color: #0a4282; line-height: 1;" class="dark:text-amber-400">{{ $attempt->score }}</p>
                    </div>
                @elseif($mode === 'google_form')
                    <div style="background: rgba(14, 165, 233, 0.1); border: 1px solid rgba(14, 165, 233, 0.3); border-radius: 1rem; padding: 1.25rem; margin: 1.5rem 0;">
                        <p style="margin: 0; font-weight: 600; color: #0284c7;" class="dark:text-sky-300">
                            Nilai Google Form Anda akan disinkronkan secara otomatis ke portal dalam beberapa menit.
                        </p>
                    </div>
                @endif

                <div style="margin-top: 1.5rem;">
                    <a href="{{ route('filament.student.pages.student-cbt-exams') }}" class="cbt-btn cbt-btn-primary">
                        <x-heroicon-m-arrow-left style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;" />
                        Kembali ke Daftar Ujian
                    </a>
                </div>
            </div>

        {{-- ========================================================= --}}
        {{-- MODE A: GOOGLE FORM (Pre-filled Iframe + Pure Alpine Timer) --}}
        {{-- ========================================================= --}}
        @elseif($mode === 'google_form')
            <div x-data="{
                    endTime: new Date('{{ $endTimeIso }}').getTime(),
                    timeLeft: '00:00:00',
                    isTimeUp: false,
                    timerId: null,
                    init() {
                        this.tick();
                        this.timerId = setInterval(() => this.tick(), 1000);
                    },
                    tick() {
                        const now = Date.now();
                        const distance = this.endTime - now;
                        if (distance <= 0) {
                            this.timeLeft = '00:00:00';
                            this.isTimeUp = true;
                            if (this.timerId) clearInterval(this.timerId);
                        } else {
                            const h = Math.floor(distance / (1000 * 60 * 60));
                            const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            const s = Math.floor((distance % (1000 * 60)) / 1000);
                            this.timeLeft = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                        }
                    }
                }">
                <!-- Header Bar -->
                <div class="glass-panel cbt-flex-between" style="padding: 1.25rem 2rem;">
                    <div>
                        <span style="display: inline-block; padding: 0.2rem 0.75rem; border-radius: 9999px; background: rgba(14, 165, 233, 0.15); color: #0284c7; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.25rem;">
                            Google Form CBT
                        </span>
                        <h2 style="font-weight: 800; font-size: 1.35rem; margin: 0;">{{ $exam->title }}</h2>
                        <p style="color: #64748b; font-size: 0.875rem; margin: 0.25rem 0 0 0;">
                            {{ $exam->subject->name ?? '' }} &bull; NIS: <strong>{{ $student->nis }}</strong> &bull; {{ $student->name }}
                        </p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div class="cbt-timer">
                            <x-heroicon-o-clock style="width: 1.5rem; height: 1.5rem; margin-right: 0.5rem;" />
                            <span x-text="timeLeft"></span>
                        </div>
                        <button type="button" wire:click="submitExam" class="cbt-btn cbt-btn-success">
                            Selesai Mengerjakan
                            <x-heroicon-m-check style="width: 1.25rem; height: 1.25rem; margin-left: 0.5rem;" />
                        </button>
                    </div>
                </div>

                <!-- Iframe Container (Hidden when timer reaches 00:00) -->
                <div class="glass-panel" x-show="!isTimeUp" style="padding: 1rem;">
                    @if($prefilledGformUrl)
                        <div class="gform-iframe-wrapper">
                            <iframe src="{{ $prefilledGformUrl }}" allowfullscreen loading="lazy">Memuat Google Form...</iframe>
                        </div>
                    @else
                        <div style="padding: 3rem; text-align: center; color: #ef4444; font-weight: 700;">
                            URL Google Form belum dikonfigurasi oleh Guru / Admin.
                        </div>
                    @endif
                </div>

                <!-- Time-Up Notice at 00:00 -->
                <div class="glass-panel" x-show="isTimeUp" style="display: none; text-align: center; padding: 3.5rem 2rem;">
                    <div style="width: 5rem; height: 5rem; background: rgba(244, 63, 94, 0.15); color: #f43f5e; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
                        <x-heroicon-o-clock style="width: 3rem; height: 3rem;" />
                    </div>
                    <h2 class="cbt-title" style="color: #e11d48;">Waktu Ujian Telah Habis (00:00)</h2>
                    <p class="cbt-subtitle">Batas waktu pengerjaan ujian ini sudah berakhir. Klik tombol di bawah untuk kembali ke daftar ujian.</p>
                    <button type="button" wire:click="submitExam" class="cbt-btn cbt-btn-primary">
                        Simpan & Kembali ke Daftar Ujian
                    </button>
                </div>
            </div>

        {{-- ========================================================= --}}
        {{-- MODE B: INTERNAL EXCEL - PRE-START SCREEN                 --}}
        {{-- ========================================================= --}}
        @elseif(!$attempt)
            <div class="glass-panel" style="max-width: 800px; margin: 2rem auto;">
                <h1 class="cbt-title">{{ $exam->title }}</h1>
                <p class="cbt-subtitle">{{ $exam->subject->name ?? '' }}</p>

                <div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px; padding: 1.25rem; background: rgba(14, 165, 233, 0.1); border: 1px solid rgba(14, 165, 233, 0.25); border-radius: 1rem;">
                        <div style="font-size: 0.875rem; color: #0284c7; font-weight: 700; display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <x-heroicon-o-clock style="width: 1.25rem; height: 1.25rem; margin-right: 0.35rem;" /> Durasi Ujian
                        </div>
                        <div style="font-size: 1.5rem; font-weight: 800;">{{ $exam->duration_minutes }} Menit</div>
                    </div>
                    <div style="flex: 1; min-width: 200px; padding: 1.25rem; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 1rem;">
                        <div style="font-size: 0.875rem; color: #059669; font-weight: 700; display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <x-heroicon-o-document-text style="width: 1.25rem; height: 1.25rem; margin-right: 0.35rem;" /> Jumlah Soal
                        </div>
                        <div style="font-size: 1.5rem; font-weight: 800;">{{ count($questionsData) }} Soal</div>
                    </div>
                </div>

                <div style="margin-bottom: 2.5rem; padding: 1.5rem; border-left: 4px solid #0a4282; background: rgba(10, 66, 130, 0.05); border-radius: 0.75rem;">
                    <h3 style="font-weight: 700; font-size: 1.1rem; margin-bottom: 0.5rem;">Petunjuk Pengerjaan:</h3>
                    <p style="white-space: pre-line; line-height: 1.6; margin: 0;">{{ $exam->description ?? 'Pilihlah jawaban yang paling tepat. Jawaban Anda tersimpan otomatis di perangkat dan dikirim sekaligus saat menekan tombol Selesai & Kumpulkan.' }}</p>
                </div>

                <button wire:click="startExam" class="cbt-btn cbt-btn-primary" style="width: 100%; padding: 1.25rem; font-size: 1.15rem;">
                    Mulai Ujian Sekarang
                    <x-heroicon-m-play style="width: 1.5rem; height: 1.5rem; margin-left: 0.5rem;" />
                </button>
            </div>

        {{-- ========================================================= --}}
        {{-- MODE B: INTERNAL EXCEL - ACTIVE EXAM (ZERO NETWORK CLICK) --}}
        {{-- ========================================================= --}}
        @else
            <div x-data="{
                    storageKey: 'g_english_cbt_attempt_{{ $attempt->id }}',
                    doubtKey: 'g_english_cbt_doubt_{{ $attempt->id }}',
                    questions: @js($questionsData),
                    answers: @js((object) $answers),
                    doubts: {},
                    currentIndex: 0,
                    endTime: new Date('{{ $endTimeIso }}').getTime(),
                    timeLeft: '00:00:00',
                    submitting: false,
                    timerId: null,

                    init() {
                        try {
                            const saved = localStorage.getItem(this.storageKey);
                            if (saved) {
                                const parsed = JSON.parse(saved);
                                this.answers = { ...this.answers, ...parsed };
                            }
                            const savedDoubts = localStorage.getItem(this.doubtKey);
                            if (savedDoubts) {
                                this.doubts = JSON.parse(savedDoubts);
                            }
                        } catch (e) {}

                        this.tick();
                        this.timerId = setInterval(() => this.tick(), 1000);
                    },

                    get currentQuestion() {
                        return this.questions[this.currentIndex] || null;
                    },

                    selectOption(questionId, key) {
                        this.answers[questionId] = key;
                        try {
                            localStorage.setItem(this.storageKey, JSON.stringify(this.answers));
                        } catch (e) {}
                    },

                    toggleDoubt(questionId) {
                        this.doubts[questionId] = !this.doubts[questionId];
                        try {
                            localStorage.setItem(this.doubtKey, JSON.stringify(this.doubts));
                        } catch (e) {}
                    },

                    answeredCount() {
                        return Object.keys(this.answers).filter(k => this.answers[k]).length;
                    },

                    tick() {
                        const distance = this.endTime - Date.now();
                        if (distance <= 0) {
                            this.timeLeft = '00:00:00';
                            if (this.timerId) clearInterval(this.timerId);
                            this.submitAll(true);
                        } else {
                            const h = Math.floor(distance / (1000 * 60 * 60));
                            const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            const s = Math.floor((distance % (1000 * 60)) / 1000);
                            this.timeLeft = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                        }
                    },

                    submitAll(force = false) {
                        if (this.submitting) return;
                        if (!force) {
                            const unanswered = this.questions.length - this.answeredCount();
                            const msg = unanswered > 0
                                ? `Masih ada ${unanswered} soal yang belum dijawab. Yakin ingin mengumpulkan ujian sekarang?`
                                : 'Apakah Anda yakin ingin menyelesaikan dan mengumpulkan ujian ini?';
                            if (!confirm(msg)) return;
                        }
                        this.submitting = true;
                        $wire.submitExam(this.answers).then(() => {
                            try {
                                localStorage.removeItem(this.storageKey);
                                localStorage.removeItem(this.doubtKey);
                            } catch (e) {}
                        });
                    }
                }"
                class="cbt-grid cbt-grid-cols-4">

                <!-- Left Panel: Question -->
                <div class="cbt-col-span-3">
                    <!-- Header / Timer -->
                    <div class="glass-panel cbt-flex-between" style="padding: 1.25rem 2rem;">
                        <div>
                            <h2 style="font-weight: 800; font-size: 1.25rem; margin: 0;">{{ $exam->title }}</h2>
                            <p style="color: #64748b; font-size: 0.875rem; margin: 0.25rem 0 0 0;">
                                Soal <span x-text="currentIndex + 1"></span> dari <span x-text="questions.length"></span>
                                &bull; Terjawab: <strong x-text="answeredCount()"></strong>/<span x-text="questions.length"></span>
                            </p>
                        </div>
                        <div class="cbt-timer">
                            <x-heroicon-o-clock style="width: 1.5rem; height: 1.5rem; margin-right: 0.5rem;" />
                            <span x-text="timeLeft"></span>
                        </div>
                    </div>

                    <!-- Question Body -->
                    <template x-if="currentQuestion">
                        <div class="glass-panel" style="padding: 2.25rem;">
                            <div class="cbt-question-text" x-html="currentQuestion.question_text"></div>

                            <template x-if="currentQuestion.attachment_url">
                                <div style="margin-top: 1.25rem; margin-bottom: 1.75rem; text-align: center;">
                                    <template x-if="currentQuestion.attachment_type === 'image'">
                                        <img :src="currentQuestion.attachment_url" alt="Attachment" style="max-width: 100%; max-height: 380px; border-radius: 12px; display: inline-block;">
                                    </template>
                                    <template x-if="currentQuestion.attachment_type === 'audio'">
                                        <audio controls style="width: 100%; max-width: 500px; display: inline-block;" :src="currentQuestion.attachment_url"></audio>
                                    </template>
                                </div>
                            </template>

                            <div class="cbt-options-list">
                                <template x-for="(optText, optKey) in currentQuestion.options" :key="optKey">
                                    <div @click="selectOption(currentQuestion.id, optKey)"
                                         class="cbt-option-label"
                                         :class="{ 'selected': answers[currentQuestion.id] === optKey }">
                                        <span class="cbt-option-badge" x-text="optKey"></span>
                                        <span style="font-size: 1.05rem; font-weight: 500;" x-text="optText"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Bottom Navigation Actions -->
                    <div class="cbt-flex-between" style="margin-top: 1.25rem;">
                        <button type="button"
                                @click="if (currentIndex > 0) currentIndex--"
                                :disabled="currentIndex === 0"
                                class="cbt-btn cbt-btn-secondary">
                            <x-heroicon-m-arrow-left style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;" />
                            Sebelumnya
                        </button>

                        <template x-if="currentQuestion">
                            <button type="button"
                                    @click="toggleDoubt(currentQuestion.id)"
                                    class="cbt-btn cbt-btn-gold">
                                <span x-text="doubts[currentQuestion.id] ? 'Hapus Ragu-Ragu' : 'Ragu-Ragu'"></span>
                            </button>
                        </template>

                        <button type="button"
                                x-show="currentIndex < questions.length - 1"
                                @click="if (currentIndex < questions.length - 1) currentIndex++"
                                class="cbt-btn cbt-btn-primary">
                            Selanjutnya
                            <x-heroicon-m-arrow-right style="width: 1.25rem; height: 1.25rem; margin-left: 0.5rem;" />
                        </button>

                        <button type="button"
                                x-show="currentIndex >= questions.length - 1"
                                @click="submitAll(false)"
                                :disabled="submitting"
                                class="cbt-btn cbt-btn-success">
                            <span x-text="submitting ? 'Mengirim Jawaban...' : 'Selesai & Submit Ujian'"></span>
                            <x-heroicon-m-check style="width: 1.25rem; height: 1.25rem; margin-left: 0.5rem;" />
                        </button>
                    </div>
                </div>

                <!-- Right Panel: Navigation Grid -->
                <div class="cbt-col-span-1">
                    <div class="glass-panel" style="position: sticky; top: 1.5rem;">
                        <h3 style="font-weight: 800; font-size: 1.1rem; margin: 0 0 1.25rem 0; display: flex; align-items: center; border-bottom: 2px solid rgba(0,0,0,0.05); padding-bottom: 0.85rem;">
                            <x-heroicon-m-squares-2x2 style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem; color: #0ea5e9;" />
                            Navigasi Soal
                        </h3>

                        <div class="cbt-nav-container">
                            <template x-for="(q, idx) in questions" :key="q.id">
                                <button type="button"
                                        @click="currentIndex = idx"
                                        class="cbt-nav-btn"
                                        :class="{
                                            'active': currentIndex === idx,
                                            'doubt': doubts[q.id],
                                            'answered': !doubts[q.id] && answers[q.id]
                                        }"
                                        x-text="idx + 1">
                                </button>
                            </template>
                        </div>

                        <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid rgba(100,116,139,0.2);">

                        <div style="margin-bottom: 1.5rem;">
                            <div class="cbt-legend-item">
                                <span class="cbt-dot" style="background: #0a4282;"></span> Sudah Dijawab
                            </div>
                            <div class="cbt-legend-item">
                                <span class="cbt-dot" style="background: #fbb900;"></span> Ragu-Ragu
                            </div>
                            <div class="cbt-legend-item">
                                <span class="cbt-dot" style="background: #ffffff; border: 2px solid #cbd5e1;"></span> Belum Dijawab
                            </div>
                        </div>

                        <button type="button"
                                @click="submitAll(false)"
                                :disabled="submitting"
                                class="cbt-btn cbt-btn-success"
                                style="width: 100%;">
                            <span x-text="submitting ? 'Memproses...' : 'Selesai / Submit Ujian'"></span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
