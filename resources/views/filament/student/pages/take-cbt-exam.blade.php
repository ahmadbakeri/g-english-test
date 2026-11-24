<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/cbt-exam.css') }}?v=3">

    @php
        $mode = $exam->cbt_mode ?? 'google_form';
        $isDeferred = $exam->isDeferredScoring();
        $student = auth()->user()->student;
        $endTimeIso = $attempt && $attempt->start_time
            ? $attempt->start_time->copy()->addMinutes($exam->duration_minutes)->toIso8601String()
            : now()->addMinutes($exam->duration_minutes)->toIso8601String();
    @endphp

    <div class="cbt-container"
         @contextmenu.prevent
         @copy.prevent
         @cut.prevent>

        {{-- ========================================================= --}}
        {{-- LOCKED STATE (3 VIOLATIONS)                               --}}
        {{-- ========================================================= --}}
        @if($isLocked)
            <div class="glass-panel" style="text-align: center; max-width: 620px; margin: 2.5rem auto; border: 2px solid #e11d48;">
                <div style="width: 5rem; height: 5rem; background: rgba(244, 63, 94, 0.15); color: #e11d48; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.35rem auto;">
                    <x-heroicon-o-lock-closed style="width: 3rem; height: 3rem;" />
                </div>
                <h2 class="cbt-title" style="margin-bottom: 0.4rem; color: #e11d48;">Ujian Terkunci!</h2>
                <p class="cbt-subtitle" style="margin-bottom: 1rem;">
                    Anda telah melampaui batas pelanggaran (<strong>{{ $attempt->violation_count ?? 3 }} / {{ $attempt->max_violations ?? 3 }}</strong>) karena keluar dari mode layar penuh atau berpindah tab/aplikasi.
                </p>
                <div style="background: rgba(244, 63, 94, 0.08); border: 1px solid rgba(244, 63, 94, 0.25); border-radius: 1rem; padding: 1.15rem; margin-bottom: 1.5rem; font-size: 0.925rem; font-weight: 600; color: #be123c;" class="dark:text-rose-300">
                    Silakan hubungi <strong>Admin / Pengawas</strong> untuk membuka kunci (Reset) ujian Anda melalui menu <em>Exam Monitoring</em>.
                </div>
                <a href="{{ route('filament.student.pages.student-cbt-exams') }}" class="cbt-btn cbt-btn-primary">
                    <x-heroicon-m-arrow-left style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;" />
                    <span>Kembali ke Daftar Ujian</span>
                </a>
            </div>

        {{-- ========================================================= --}}
        {{-- FINISHED STATE                                            --}}
        {{-- ========================================================= --}}
        @elseif($isFinished)
            <div class="glass-panel" style="text-align: center; max-width: 600px; margin: 2.5rem auto;">
                <div style="width: 5rem; height: 5rem; background: rgba(16, 185, 129, 0.15); color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.35rem auto;">
                    <x-heroicon-o-check-badge style="width: 3rem; height: 3rem;" />
                </div>
                <h2 class="cbt-title" style="margin-bottom: 0.4rem;">Ujian Selesai!</h2>
                <p class="cbt-subtitle">Jawaban ujian Anda telah berhasil disimpan ke sistem.</p>

                @if($isDeferred && $attempt && $attempt->score === null)
                    <div style="background: rgba(251, 185, 0, 0.14); border: 1.5px solid rgba(251, 185, 0, 0.55); border-radius: 1rem; padding: 1.35rem 1.5rem; margin: 1.5rem 0;">
                        <p style="margin: 0 0 0.35rem 0; font-weight: 800; font-size: 1rem; color: #92400e;" class="dark:text-amber-300">
                            Menunggu Penilaian Admin
                        </p>
                        <p style="margin: 0; font-size: 0.9rem; font-weight: 500; color: #78350f; line-height: 1.55;" class="dark:text-amber-100">
                            Seluruh jawaban Anda telah terekam aman di server. Nilai resmi tryout akan diproses dan diumumkan oleh Admin setelah proses penilaian selesai.
                        </p>
                    </div>
                @elseif(($exam->show_result_immediately || ($attempt && $attempt->scoring_status === 'manual_uploaded')) && $attempt && $attempt->score !== null)
                    <div style="background: rgba(10, 66, 130, 0.08); border: 2px solid #0a4282; border-radius: 1.25rem; padding: 1.75rem 2.25rem; margin: 1.5rem 0; display: inline-block; min-width: 220px;">
                        <p style="font-size: 0.8rem; color: #0284c7; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 800; margin-bottom: 0.4rem;">Skor Akhir</p>
                        <p style="font-size: 3.75rem; font-weight: 900; color: #0a4282; line-height: 1; margin: 0;" class="dark:text-amber-400">{{ $attempt->score }}</p>
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
                        <span>Kembali ke Daftar Ujian</span>
                    </a>
                </div>
            </div>

        {{-- ========================================================= --}}
        {{-- MODE A: GOOGLE FORM (True Element Fullscreen + Timer)      --}}
        {{-- ========================================================= --}}
        @elseif($mode === 'google_form')
            @php
                $gformConfig = [
                    'endTimeIso' => $endTimeIso,
                    'violationCount' => (int) ($attempt->violation_count ?? 0),
                    'maxViolations' => (int) ($attempt->max_violations ?? 3),
                ];
            @endphp
            <script id="cbt-gform-data-{{ $attempt->id }}" type="application/json">@json($gformConfig)</script>
            <script>
                window.cbtGformApp = window.cbtGformApp || function (configId, wire) {
                    let cfg = {};
                    try {
                        cfg = JSON.parse(document.getElementById(configId)?.textContent || '{}');
                    } catch (e) {}
                    return {
                        endTime: new Date(cfg.endTimeIso || Date.now()).getTime(),
                        timeLeft: '00:00:00',
                        isTimeUp: false,
                        submitting: false,
                        submitNotice: '',
                        submitFailed: false,
                        timerId: null,
                        inFullscreen: false,
                        hasEnteredFullscreen: false,
                        violationCount: Number(cfg.violationCount || 0),
                        maxViolations: Number(cfg.maxViolations || 3),
                        lastViolationAt: 0,
                        violationWarning: '',
                        gformLoadCount: 0,
                        gformSubmitted: false,
                        init() {
                            this.inFullscreen = (document.fullscreenElement === this.$refs.examShell);
                            this.tick();
                            this.timerId = setInterval(() => this.tick(), 1000);
                            this.bindAntiCheat();
                        },
                        onGformLoad() {
                            this.gformLoadCount++;
                            if (this.gformLoadCount >= 2) {
                                this.gformSubmitted = true;
                            }
                        },
                        enterFullscreen() {
                            const el = this.$refs.examShell;
                            if (el && el.requestFullscreen) {
                                el.requestFullscreen().then(() => {
                                    this.inFullscreen = true;
                                    this.hasEnteredFullscreen = true;
                                }).catch(() => {
                                    this.inFullscreen = true;
                                    this.hasEnteredFullscreen = true;
                                });
                            } else {
                                this.inFullscreen = true;
                                this.hasEnteredFullscreen = true;
                            }
                        },
                        bindAntiCheat() {
                            document.addEventListener('fullscreenchange', () => {
                                this.inFullscreen = (document.fullscreenElement === this.$refs.examShell);
                                if (this.hasEnteredFullscreen && !this.inFullscreen && !this.isTimeUp && !this.submitting) {
                                    this.triggerViolation('Keluar dari mode layar penuh');
                                }
                            });
                            document.addEventListener('visibilitychange', () => {
                                if (document.hidden && this.hasEnteredFullscreen && !this.isTimeUp && !this.submitting) {
                                    this.triggerViolation('Pindah tab atau meminimalkan browser');
                                }
                            });
                            window.addEventListener('blur', () => {
                                if (this.hasEnteredFullscreen && !this.isTimeUp && !this.submitting && document.activeElement?.tagName !== 'IFRAME') {
                                    this.triggerViolation('Keluar dari jendela ujian');
                                }
                            });
                        },
                        triggerViolation(reason) {
                            if (this.submitting || this.isTimeUp) return;
                            const now = Date.now();
                            if (now - this.lastViolationAt < 1500) return;
                            this.lastViolationAt = now;
                            wire.recordViolation(reason).then((res) => {
                                if (res) {
                                    this.violationCount = res.violation_count;
                                    this.maxViolations = res.max_violations;
                                    this.violationWarning = `Peringatan (${res.violation_count}/${res.max_violations}): ${reason}. Jika mencapai ${res.max_violations}x, ujian otomatis terkunci!`;
                                    if (res.locked && document.fullscreenElement) {
                                        document.exitFullscreen().catch(() => {});
                                    }
                                }
                            });
                        },
                        finishExam(attemptNo = 1) {
                            if (this.submitting && attemptNo === 1) return;
                            this.submitting = true;
                            this.submitFailed = false;
                            this.submitNotice = attemptNo === 1
                                ? 'Menyimpan status selesai ujian...'
                                : `Server sedang ramai, mengirim ulang otomatis (${attemptNo}/8)...`;
                            if (document.fullscreenElement) {
                                document.exitFullscreen().catch(() => {});
                            }
                            Promise.resolve(wire.submitExam()).catch(() => {
                                if (attemptNo < 8) {
                                    const waitMs = Math.min(2000 + attemptNo * 1500, 8000) + Math.floor(Math.random() * 2000);
                                    this.submitNotice = `Server sibuk, mencoba ulang otomatis dalam ${Math.ceil(waitMs / 1000)} dtk (${attemptNo}/8)...`;
                                    setTimeout(() => this.finishExam(attemptNo + 1), waitMs);
                                } else {
                                    this.submitting = false;
                                    this.submitFailed = true;
                                    this.submitNotice = 'Koneksi terputus saat mengakhiri tes. Klik tombol lagi saat internet stabil.';
                                }
                            });
                        },
                        tick() {
                            const distance = this.endTime - Date.now();
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
                    };
                };
            </script>
            <div x-ref="examShell"
                 wire:key="cbt-gform-shell-{{ $attempt->id }}"
                 class="cbt-fullscreen-shell"
                 x-data="window.cbtGformApp('cbt-gform-data-{{ $attempt->id }}', $wire)">
                <!-- Fullscreen Gate Overlay -->
                <div x-show="!inFullscreen && !submitting" class="cbt-modal-backdrop">
                    <div class="cbt-modal-card" style="border-color: rgba(251, 185, 0, 0.55);">
                        <div style="width: 4.25rem; height: 4.25rem; border-radius: 1.15rem; background: linear-gradient(135deg, #0a4282, #1a368b); color: #fbb900; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.1rem auto; box-shadow: 0 10px 20px -5px rgba(10, 66, 130, 0.45);">
                            <x-heroicon-o-arrows-pointing-out style="width: 2.25rem; height: 2.25rem;" />
                        </div>
                        <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; background: rgba(244, 63, 94, 0.12); color: #e11d48; font-size: 0.75rem; font-weight: 800; margin-bottom: 0.65rem;">
                            Pelanggaran: <span x-text="violationCount"></span> / <span x-text="maxViolations"></span>
                        </span>
                        <h2 class="cbt-title" style="font-size: 1.45rem; margin-bottom: 0.5rem;">Perbesar Layar Penuh untuk Ujian</h2>
                        <p class="cbt-subtitle" style="margin-bottom: 1.1rem; font-size: 0.9rem;">
                            Ujian Google Form hanya tampil dalam mode Layar Penuh. Dilarang keluar layar penuh atau pindah tab (maks. <strong x-text="maxViolations"></strong>x pelanggaran).
                        </p>
                        <div x-show="violationWarning" x-text="violationWarning" style="margin-bottom: 1.1rem; padding: 0.85rem 1rem; border-radius: 0.85rem; background: rgba(244, 63, 94, 0.12); border: 1px solid rgba(244, 63, 94, 0.3); color: #e11d48; font-weight: 700; font-size: 0.85rem;"></div>
                        <button type="button" @click="enterFullscreen()" class="cbt-btn cbt-btn-primary" style="width: 100%; padding: 0.95rem;">
                            Perbesar Layar &amp; Mulai Ujian
                        </button>
                    </div>
                </div>

                <!-- Slim Header Bar (Only Timer + Akhiri Tes after submit) -->
                <div class="cbt-topbar" style="padding: 0.7rem 1.25rem; margin-bottom: 0.85rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                        <span style="display: inline-block; padding: 0.2rem 0.65rem; border-radius: 9999px; background: #fbb900; color: #0a4282; font-size: 0.7rem; font-weight: 900; text-transform: uppercase;">
                            Google Form CBT
                        </span>
                        <h2 style="font-weight: 800; font-size: 1.05rem; margin: 0; color: #ffffff;">{{ $exam->title }}</h2>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                        <span class="cbt-violation-pill">
                            Pelanggaran: <span x-text="violationCount"></span>/<span x-text="maxViolations"></span>
                        </span>
                        <div class="cbt-timer-pill">
                            <x-heroicon-o-clock style="width: 1.15rem; height: 1.15rem;" />
                            <span x-text="timeLeft"></span>
                        </div>
                        <button type="button"
                                x-show="gformSubmitted"
                                x-transition
                                :disabled="submitting"
                                @click="finishExam(1)"
                                class="cbt-btn cbt-btn-success"
                                style="padding: 0.5rem 1.2rem; font-size: 0.85rem;">
                            <span x-text="submitting ? 'Menyimpan...' : 'Akhiri Tes'"></span>
                            <x-heroicon-m-check style="width: 1.05rem; height: 1.05rem; margin-left: 0.35rem;" />
                        </button>
                    </div>
                </div>

                <!-- Live Submit / Retry Status Banner (G-Form) -->
                <div x-show="submitNotice" x-cloak x-transition style="margin-bottom:0.85rem;padding:0.75rem 1.1rem;border-radius:0.85rem;font-size:0.85rem;font-weight:700;display:flex;align-items:center;justify-content:space-between;gap:0.75rem;" :style="submitFailed ? 'background:rgba(244,63,94,0.14);border:1px solid rgba(244,63,94,0.35);color:#be123c;' : 'background:rgba(251,185,0,0.2);border:1px solid rgba(251,185,0,0.45);color:#0a4282;'">
                    <span x-text="submitNotice"></span>
                    <button x-show="submitFailed" type="button" @click="finishExam(1)" class="cbt-btn cbt-btn-primary" style="padding:0.4rem 0.95rem;font-size:0.78rem;">Coba Lagi</button>
                </div>

                <!-- Iframe Container -->
                <div class="glass-panel" x-show="!isTimeUp" style="padding: 0.5rem; margin-bottom: 0;">
                    @if($prefilledGformUrl)
                        <div class="gform-iframe-wrapper">
                            <iframe src="{{ $prefilledGformUrl }}" @load="onGformLoad()" allowfullscreen loading="lazy">Memuat Google Form...</iframe>
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
                    <button type="button" @click="finishExam()" class="cbt-btn cbt-btn-primary">
                        Simpan &amp; Kembali ke Daftar Ujian
                    </button>
                </div>
            </div>

        {{-- ========================================================= --}}
        {{-- MODE B: INTERNAL EXCEL - PRE-START SCREEN                 --}}
        {{-- ========================================================= --}}
        @elseif(!$attempt)
            <div class="glass-panel" style="max-width: 780px; margin: 1.5rem auto;">
                <span class="cbt-question-badge">Real CBT Exam</span>
                <h1 class="cbt-title">{{ $exam->title }}</h1>
                <p class="cbt-subtitle">{{ $exam->subject->name ?? '' }}</p>

                <div style="display: flex; gap: 1rem; margin-bottom: 1.75rem; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px; padding: 1.25rem; background: rgba(10, 66, 130, 0.08); border: 1px solid rgba(10, 66, 130, 0.2); border-radius: 1rem;">
                        <div style="font-size: 0.825rem; color: #0a4282; font-weight: 800; text-transform: uppercase; display: flex; align-items: center; margin-bottom: 0.4rem;" class="dark:text-amber-400">
                            <x-heroicon-o-clock style="width: 1.2rem; height: 1.2rem; margin-right: 0.35rem;" /> Durasi Ujian
                        </div>
                        <div style="font-size: 1.5rem; font-weight: 900;">{{ $exam->duration_minutes }} Menit</div>
                    </div>
                    <div style="flex: 1; min-width: 200px; padding: 1.25rem; background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 1rem;">
                        <div style="font-size: 0.825rem; color: #059669; font-weight: 800; text-transform: uppercase; display: flex; align-items: center; margin-bottom: 0.4rem;">
                            <x-heroicon-o-document-text style="width: 1.2rem; height: 1.2rem; margin-right: 0.35rem;" /> Jumlah Soal
                        </div>
                        <div style="font-size: 1.5rem; font-weight: 900;">{{ count($questionsData) }} Soal</div>
                    </div>
                </div>

                <div style="margin-bottom: 2rem; padding: 1.35rem 1.5rem; border: 1px solid rgba(10, 66, 130, 0.2); background: rgba(10, 66, 130, 0.05); border-radius: 0.85rem;">
                    <h3 style="font-weight: 800; font-size: 1.025rem; margin-bottom: 0.4rem;">Petunjuk Pengerjaan:</h3>
                    <p style="white-space: pre-line; line-height: 1.65; margin: 0; font-size: 0.95rem;">{{ $exam->description ?? 'Pilihlah jawaban yang paling tepat. Jawaban Anda tersimpan otomatis di perangkat dan dikirim sekaligus saat menekan tombol Selesai & Submit Ujian.' }}</p>
                </div>

                <button wire:click="startExam" wire:loading.attr="disabled" class="cbt-btn cbt-btn-primary" style="width: 100%; padding: 1.15rem; font-size: 1.1rem;">
                    <span wire:loading.remove wire:target="startExam">Mulai Ujian Sekarang</span>
                    <span wire:loading wire:target="startExam">Menyiapkan Lembar Ujian...</span>
                    <x-heroicon-m-play style="width: 1.35rem; height: 1.35rem; margin-left: 0.5rem; color: #fbb900;" />
                </button>
            </div>

        {{-- ========================================================= --}}
        {{-- MODE B: INTERNAL EXCEL - ACTIVE EXAM (TRUE FULLSCREEN)    --}}
        {{-- ========================================================= --}}
        @else
            @php
                $examConfig = [
                    'storageKey' => 'g_english_cbt_' . $exam->id . '_attempt_' . $attempt->id,
                    'doubtKey' => 'g_english_cbt_' . $exam->id . '_doubt_' . $attempt->id,
                    'questions' => $questionsData,
                    'answers' => (object) $answers,
                    'endTimeIso' => $endTimeIso,
                    'violationCount' => (int) ($attempt->violation_count ?? 0),
                    'maxViolations' => (int) ($attempt->max_violations ?? 3),
                ];
            @endphp
            <script id="cbt-exam-data-{{ $attempt->id }}" type="application/json">@json($examConfig)</script>
            <script>
                window.cbtExamApp = window.cbtExamApp || function (configId, wire) {
                    let cfg = {};
                    try {
                        cfg = JSON.parse(document.getElementById(configId)?.textContent || '{}');
                    } catch (e) {}
                    return {
                        storageKey: cfg.storageKey || 'g_english_cbt_attempt',
                        doubtKey: cfg.doubtKey || 'g_english_cbt_doubt',
                        questions: Array.isArray(cfg.questions) ? cfg.questions : [],
                        answers: cfg.answers || {},
                        doubts: {},
                        currentIndex: 0,
                        endTime: new Date(cfg.endTimeIso || Date.now()).getTime(),
                        timeLeft: '00:00:00',
                        submitting: false,
                        isTimeUp: false,
                        jitterCountdown: 0,
                        submitNotice: '',
                        submitFailed: false,
                        timerId: null,
                        inFullscreen: false,
                        hasEnteredFullscreen: false,
                        violationCount: Number(cfg.violationCount || 0),
                        maxViolations: Number(cfg.maxViolations || 3),
                        lastViolationAt: 0,
                        violationWarning: '',
                        showSubmitModal: false,

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

                            this.inFullscreen = (document.fullscreenElement === this.$refs.examShell);
                            this.tick();
                            this.timerId = setInterval(() => this.tick(), 1000);
                            this.bindAntiCheat();
                            this.$watch('currentIndex', () => this.renderMath());
                            this.renderMath();
                        },

                        renderMath() {
                            this.$nextTick(() => {
                                if (typeof window.renderCbtMath === 'function') {
                                    window.renderCbtMath(this.$refs.questionStage || this.$refs.examShell);
                                }
                            });
                        },

                        enterFullscreen() {
                            const el = this.$refs.examShell;
                            if (el && el.requestFullscreen) {
                                el.requestFullscreen().then(() => {
                                    this.inFullscreen = true;
                                    this.hasEnteredFullscreen = true;
                                }).catch(() => {
                                    this.inFullscreen = true;
                                    this.hasEnteredFullscreen = true;
                                });
                            } else {
                                this.inFullscreen = true;
                                this.hasEnteredFullscreen = true;
                            }
                        },

                        bindAntiCheat() {
                            document.addEventListener('fullscreenchange', () => {
                                this.inFullscreen = (document.fullscreenElement === this.$refs.examShell);
                                if (this.hasEnteredFullscreen && !this.inFullscreen && !this.submitting) {
                                    this.triggerViolation('Keluar dari mode layar penuh');
                                }
                            });
                            document.addEventListener('visibilitychange', () => {
                                if (document.hidden && this.hasEnteredFullscreen && !this.submitting) {
                                    this.triggerViolation('Pindah tab atau meminimalkan browser');
                                }
                            });
                            window.addEventListener('blur', () => {
                                if (this.hasEnteredFullscreen && !this.submitting) {
                                    this.triggerViolation('Keluar dari jendela ujian');
                                }
                            });
                            window.addEventListener('keydown', (e) => {
                                if (!this.inFullscreen || this.submitting || !this.currentQuestion) return;
                                const tag = (document.activeElement?.tagName || '').toLowerCase();
                                if (tag === 'input' || tag === 'textarea') return;
                                if (e.key === 'ArrowRight' && this.currentIndex < this.questions.length - 1) {
                                    this.currentIndex++;
                                } else if (e.key === 'ArrowLeft' && this.currentIndex > 0) {
                                    this.currentIndex--;
                                }
                            });
                        },

                        triggerViolation(reason) {
                            if (this.submitting) return;
                            const now = Date.now();
                            if (now - this.lastViolationAt < 1500) return;
                            this.lastViolationAt = now;
                            wire.recordViolation(reason).then((res) => {
                                if (res) {
                                    this.violationCount = res.violation_count;
                                    this.maxViolations = res.max_violations;
                                    this.violationWarning = `Peringatan (${res.violation_count}/${res.max_violations}): ${reason}. Jika mencapai ${res.max_violations}x, ujian otomatis terkunci!`;
                                    if (res.locked && document.fullscreenElement) {
                                        document.exitFullscreen().catch(() => {});
                                    }
                                }
                            });
                        },

                        get currentQuestion() {
                            return this.questions[this.currentIndex] || null;
                        },

                        selectOption(questionId, key) {
                            if (this.isTimeUp || this.submitting) return;
                            this.answers[questionId] = key;
                            this.persistAnswers();
                        },

                        selectComplexTf(questionId, stmtNo, val) {
                            if (this.isTimeUp || this.submitting) return;
                            const cur = (this.answers[questionId] && typeof this.answers[questionId] === 'object')
                                ? { ...this.answers[questionId] }
                                : {};
                            cur[String(stmtNo)] = val;
                            this.answers[questionId] = cur;
                            this.persistAnswers();
                        },

                        getComplexTfVal(questionId, stmtNo) {
                            const cur = this.answers[questionId];
                            if (!cur || typeof cur !== 'object') return '';
                            return cur[String(stmtNo)] || '';
                        },

                        setShortAnswer(questionId, val) {
                            if (this.isTimeUp || this.submitting) return;
                            this.answers[questionId] = val;
                            this.persistAnswers();
                        },

                        persistAnswers() {
                            try {
                                localStorage.setItem(this.storageKey, JSON.stringify(this.answers));
                            } catch (e) {}
                        },

                        isQuestionAnswered(q) {
                            if (!q) return false;
                            const ans = this.answers[q.id];
                            if (q.question_type === 'complex_tf') {
                                if (!ans || typeof ans !== 'object') return false;
                                const total = Array.isArray(q.statements) ? q.statements.length : 0;
                                if (total === 0) return false;
                                const filled = Object.values(ans).filter(v => v === 'B' || v === 'S').length;
                                return filled >= total;
                            }
                            return typeof ans === 'string' && ans.trim() !== '';
                        },

                        navBadgeText(q) {
                            if (!q) return '';
                            const ans = this.answers[q.id];
                            if (q.question_type === 'complex_tf') {
                                const total = Array.isArray(q.statements) ? q.statements.length : 0;
                                const filled = (ans && typeof ans === 'object')
                                    ? Object.values(ans).filter(v => v === 'B' || v === 'S').length
                                    : 0;
                                return filled > 0 ? `${filled}/${total}` : '';
                            }
                            if (q.question_type === 'short_answer') {
                                return (typeof ans === 'string' && ans.trim() !== '') ? '✓' : '';
                            }
                            return typeof ans === 'string' ? ans : '';
                        },

                        toggleDoubt(questionId) {
                            this.doubts[questionId] = !this.doubts[questionId];
                            try {
                                localStorage.setItem(this.doubtKey, JSON.stringify(this.doubts));
                            } catch (e) {}
                        },

                        answeredCount() {
                            return this.questions.filter(q => this.isQuestionAnswered(q)).length;
                        },

                        doubtCount() {
                            return this.questions.filter(q => Boolean(this.doubts[q.id])).length;
                        },

                        unansweredCount() {
                            return Math.max(0, this.questions.length - this.answeredCount());
                        },

                        progressPercent() {
                            if (!this.questions.length) return 0;
                            return Math.round((this.answeredCount() / this.questions.length) * 100);
                        },

                        tick() {
                            const distance = this.endTime - Date.now();
                            if (distance <= 0) {
                                this.timeLeft = '00:00:00';
                                this.isTimeUp = true;
                                if (this.timerId) clearInterval(this.timerId);
                                this.queueAutoSubmitWithJitter();
                            } else {
                                const h = Math.floor(distance / (1000 * 60 * 60));
                                const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                const s = Math.floor((distance % (1000 * 60)) / 1000);
                                this.timeLeft = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                            }
                        },

                        queueAutoSubmitWithJitter() {
                            if (this.submitting) return;
                            this.showSubmitModal = false;
                            this.submitting = true;
                            // Spread simultaneous 00:00 auto-submits across 1..18s so 1,000-1,500 browsers never slam the server in 1 second
                            const delaySec = 1 + Math.floor(Math.random() * 18);
                            this.jitterCountdown = delaySec;
                            this.submitNotice = `Waktu habis! Jawaban aman tersimpan di perangkat. Mengantre pengiriman otomatis dalam ${delaySec} detik...`;
                            const jt = setInterval(() => {
                                this.jitterCountdown--;
                                if (this.jitterCountdown <= 0) {
                                    clearInterval(jt);
                                    this.sendSubmitWithRetry(1);
                                } else {
                                    this.submitNotice = `Waktu habis! Jawaban aman tersimpan di perangkat. Mengantre pengiriman otomatis dalam ${this.jitterCountdown} detik...`;
                                }
                            }, 1000);
                        },

                        submitAll(force = false) {
                            if (this.submitting) return;
                            if (!force) {
                                this.showSubmitModal = true;
                                return;
                            }
                            this.showSubmitModal = false;
                            this.submitting = true;
                            this.sendSubmitWithRetry(1);
                        },

                        sendSubmitWithRetry(attemptNo = 1) {
                            this.submitting = true;
                            this.submitFailed = false;
                            this.submitNotice = attemptNo === 1
                                ? 'Mengirim jawaban Anda ke server...'
                                : `Server sedang ramai, mengirim ulang otomatis (percobaan ${attemptNo}/10)...`;
                            if (document.fullscreenElement) {
                                document.exitFullscreen().catch(() => {});
                            }
                            Promise.resolve(wire.submitExam(this.answers)).then(() => {
                                try {
                                    localStorage.removeItem(this.storageKey);
                                    localStorage.removeItem(this.doubtKey);
                                } catch (e) {}
                            }).catch(() => {
                                if (attemptNo < 10) {
                                    const waitMs = Math.min(2000 + attemptNo * 1500, 9000) + Math.floor(Math.random() * 2500);
                                    this.submitNotice = `Koneksi/server sibuk — jawaban aman di perangkat. Mencoba kirim ulang dalam ${Math.ceil(waitMs / 1000)} dtk (${attemptNo}/10)...`;
                                    setTimeout(() => this.sendSubmitWithRetry(attemptNo + 1), waitMs);
                                } else {
                                    this.submitting = false;
                                    this.submitFailed = true;
                                    this.submitNotice = 'Gagal terhubung ke server setelah 10 percobaan. Jangan tutup halaman — jawaban Anda aman di perangkat. Klik Kirim Ulang saat internet aktif.';
                                }
                            });
                        }
                    };
                };
            </script>
            <div x-ref="examShell"
                 wire:key="cbt-exam-shell-{{ $attempt->id }}"
                 class="cbt-fullscreen-shell"
                 x-data="window.cbtExamApp('cbt-exam-data-{{ $attempt->id }}', $wire)">

                <!-- Fullscreen Gate Overlay -->
                <div x-show="!inFullscreen && !submitting" class="cbt-modal-backdrop">
                    <div class="cbt-modal-card" style="border-color: rgba(251, 185, 0, 0.55);">
                        <div style="width: 4.25rem; height: 4.25rem; border-radius: 1.15rem; background: linear-gradient(135deg, #0a4282, #1a368b); color: #fbb900; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.1rem auto; box-shadow: 0 10px 20px -5px rgba(10, 66, 130, 0.45);">
                            <x-heroicon-o-arrows-pointing-out style="width: 2.25rem; height: 2.25rem;" />
                        </div>
                        <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; background: rgba(244, 63, 94, 0.12); color: #e11d48; font-size: 0.75rem; font-weight: 800; margin-bottom: 0.65rem;">
                            Pelanggaran: <span x-text="violationCount"></span> / <span x-text="maxViolations"></span>
                        </span>
                        <h2 class="cbt-title" style="font-size: 1.45rem; margin-bottom: 0.5rem;">Perbesar Layar Penuh untuk Ujian</h2>
                        <p class="cbt-subtitle" style="margin-bottom: 1.1rem; font-size: 0.9rem;">
                            Saat masuk layar penuh, hanya soal dan peta jawaban yang tampil. Dilarang pindah tab atau keluar layar penuh (maks. <strong x-text="maxViolations"></strong>x pelanggaran).
                        </p>
                        <div x-show="violationWarning" x-text="violationWarning" style="margin-bottom: 1.1rem; padding: 0.85rem 1rem; border-radius: 0.85rem; background: rgba(244, 63, 94, 0.12); border: 1px solid rgba(244, 63, 94, 0.3); color: #e11d48; font-weight: 700; font-size: 0.85rem;"></div>
                        <button type="button" @click="enterFullscreen()" class="cbt-btn cbt-btn-primary" style="width: 100%; padding: 0.95rem;">
                            Perbesar Layar &amp; Lanjutkan Ujian
                        </button>
                    </div>
                </div>

                <!-- Custom Submit Confirmation Modal (No native confirm() popup) -->
                <div x-show="showSubmitModal" x-transition class="cbt-modal-backdrop" style="display: none;">
                    <div class="cbt-modal-card">
                        <div style="width: 4rem; height: 4rem; border-radius: 1rem; background: rgba(16, 185, 129, 0.14); color: #059669; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
                            <x-heroicon-o-paper-airplane style="width: 2rem; height: 2rem;" />
                        </div>
                        <h2 class="cbt-title" style="font-size: 1.4rem; margin-bottom: 0.4rem;">Kumpulkan Jawaban Ujian?</h2>
                        <p class="cbt-subtitle" style="margin-bottom: 1.1rem; font-size: 0.9rem;">
                            Pastikan semua jawaban sudah diperiksa. Setelah dikirim, jawaban tidak dapat diubah lagi.
                        </p>
                        <div class="cbt-map-stats" style="margin-bottom: 1.35rem;">
                            <div class="cbt-map-stat" style="background: rgba(10, 66, 130, 0.12); color: #0a4282;">
                                <div style="font-size: 1.15rem; font-weight: 900;" x-text="answeredCount()"></div>
                                <div>Dijawab</div>
                            </div>
                            <div class="cbt-map-stat" style="background: rgba(251, 185, 0, 0.22); color: #92400e;">
                                <div style="font-size: 1.15rem; font-weight: 900;" x-text="doubtCount()"></div>
                                <div>Ragu</div>
                            </div>
                            <div class="cbt-map-stat" style="background: rgba(244, 63, 94, 0.14); color: #be123c;">
                                <div style="font-size: 1.15rem; font-weight: 900;" x-text="unansweredCount()"></div>
                                <div>Kosong</div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 0.75rem;">
                            <button type="button" @click="showSubmitModal = false" class="cbt-btn cbt-btn-secondary" style="flex: 1;">
                                Kembali ke Soal
                            </button>
                            <button type="button" @click="submitAll(true)" :disabled="submitting" class="cbt-btn cbt-btn-success" style="flex: 1;">
                                <span x-text="submitting ? 'Mengirim...' : 'Ya, Kirim Jawaban'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Live Submit / Anti-Stampede Jitter / Auto-Retry Banner (CBT Native) -->
                <div x-show="submitNotice" x-cloak x-transition style="margin-bottom:0.9rem;padding:0.85rem 1.15rem;border-radius:0.95rem;font-size:0.88rem;font-weight:700;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:0.75rem;" :style="submitFailed ? 'background:rgba(244,63,94,0.14);border:1px solid rgba(244,63,94,0.35);color:#be123c;' : 'background:rgba(251,185,0,0.22);border:1px solid rgba(251,185,0,0.5);color:#0a4282;'">
                    <span x-text="submitNotice"></span>
                    <button x-show="submitFailed" type="button" @click="sendSubmitWithRetry(1)" class="cbt-btn cbt-btn-primary" style="padding:0.45rem 1.05rem;font-size:0.8rem;">
                        Kirim Ulang Sekarang
                    </button>
                </div>

                <!-- Sleek Topbar -->
                <div class="cbt-topbar">
                    <div>
                        <span style="display: inline-block; padding: 0.18rem 0.65rem; border-radius: 9999px; background: #fbb900; color: #0a4282; font-size: 0.7rem; font-weight: 900; text-transform: uppercase; margin-bottom: 0.25rem;">
                            G-English CBT
                        </span>
                        <h2 style="font-weight: 800; font-size: 1.2rem; margin: 0; color: #ffffff;">{{ $exam->title }}</h2>
                        <p style="color: #cbd5e1; font-size: 0.82rem; margin: 0.15rem 0 0 0;">
                            {{ $student->name }} ({{ $student->nis }}) &bull; Soal <strong x-text="currentIndex + 1"></strong> / <span x-text="questions.length"></span>
                        </p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                        <span class="cbt-violation-pill">
                            Pelanggaran: <span x-text="violationCount"></span>/<span x-text="maxViolations"></span>
                        </span>
                        <div class="cbt-timer-pill">
                            <x-heroicon-o-clock style="width: 1.2rem; height: 1.2rem;" />
                            <span x-text="timeLeft"></span>
                        </div>
                    </div>
                </div>

                <div class="cbt-grid cbt-grid-cols-4">
                    <!-- Left Panel: Question -->
                    <div>
                        <template x-if="currentQuestion">
                            <div x-ref="questionStage" class="glass-panel" style="padding: 1.85rem;">
                                <div class="cbt-flex-between" style="border-bottom: 1px solid rgba(148, 163, 184, 0.22); padding-bottom: 0.85rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                        <span class="cbt-question-badge">
                                            Soal #<span x-text="currentIndex + 1"></span>
                                        </span>
                                        <span style="padding: 0.22rem 0.65rem; border-radius: 9999px; background: rgba(14, 165, 233, 0.14); color: #0284c7; font-size: 0.72rem; font-weight: 800;"
                                              x-text="currentQuestion.question_type === 'complex_tf' ? 'Benar / Salah' : (currentQuestion.question_type === 'short_answer' ? 'Isian Singkat' : 'Pilihan Ganda')">
                                        </span>
                                        <span x-show="doubts[currentQuestion.id]" style="padding: 0.25rem 0.7rem; border-radius: 9999px; background: #fbb900; color: #0a4282; font-size: 0.72rem; font-weight: 800;">
                                            Ragu-Ragu
                                        </span>
                                    </div>
                                    @if(!$isDeferred)
                                        <span style="font-size: 0.82rem; font-weight: 700; color: #64748b;" class="dark:text-slate-300">
                                            Bobot: <strong x-text="currentQuestion.points || 1"></strong> Poin
                                        </span>
                                    @else
                                        <span style="font-size: 0.78rem; font-weight: 700; color: #b45309;" class="dark:text-amber-300">
                                            Penilaian Tryout (Proses Admin)
                                        </span>
                                    @endif
                                </div>

                                <div class="cbt-question-text cbt-rich-content" x-html="currentQuestion.question_text"></div>

                                <template x-if="currentQuestion.attachment_url">
                                    <div style="margin: 1rem 0 1.5rem 0; text-align: center;">
                                        <template x-if="currentQuestion.attachment_type === 'image'">
                                            <img :src="currentQuestion.attachment_url" alt="Attachment" style="max-width: 100%; max-height: 360px; border-radius: 12px; display: inline-block;">
                                        </template>
                                        <template x-if="currentQuestion.attachment_type === 'audio'">
                                            <audio controls style="width: 100%; max-width: 500px; display: inline-block;" :src="currentQuestion.attachment_url"></audio>
                                        </template>
                                    </div>
                                </template>

                                {{-- TYPE 1: MCQ A-E (HTML + KaTeX supported!) --}}
                                <template x-if="(currentQuestion.question_type || 'mcq') === 'mcq'">
                                    <div class="cbt-options-list">
                                        <template x-for="(optText, optKey) in currentQuestion.options" :key="currentQuestion.id + '_' + optKey">
                                            <div @click="selectOption(currentQuestion.id, optKey)"
                                                 class="cbt-option-label"
                                                 :class="{ 'selected': answers[currentQuestion.id] === optKey }">
                                                <span class="cbt-option-badge" x-text="optKey"></span>
                                                <div class="cbt-rich-content cbt-option-content" style="flex: 1;" x-html="optText"></div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                {{-- TYPE 2: COMPLEX TRUE/FALSE TABLE (UTBK-SNBT Pernyataan Benar/Salah) --}}
                                <template x-if="currentQuestion.question_type === 'complex_tf'">
                                    <div class="cbt-rich-content" style="margin-top: 0.75rem; overflow-x: auto;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <thead>
                                                <tr>
                                                    <th style="width: 52px;">No</th>
                                                    <th style="text-align: left;">Pernyataan</th>
                                                    <th style="width: 96px;">Benar</th>
                                                    <th style="width: 96px;">Salah</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="(stmt, sIdx) in (currentQuestion.statements || [])" :key="currentQuestion.id + '_st_' + (stmt.no || sIdx)">
                                                    <tr>
                                                        <td style="text-align: center; font-weight: 800;" x-text="stmt.no || (sIdx + 1)"></td>
                                                        <td class="cbt-tf-stmt" style="font-size: 0.96rem; line-height: 1.65;" x-html="stmt.text"></td>
                                                        <td style="text-align: center;">
                                                            <button type="button"
                                                                    @click="selectComplexTf(currentQuestion.id, stmt.no || (sIdx + 1), 'B')"
                                                                    style="padding: 0.42rem 0.9rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 800; cursor: pointer; transition: all 0.15s;"
                                                                    :style="getComplexTfVal(currentQuestion.id, stmt.no || (sIdx + 1)) === 'B'
                                                                        ? 'background:#10b981;color:#ffffff;border:2px solid #059669;'
                                                                        : 'background:transparent;color:#475569;border:2px solid #cbd5e1;'">
                                                                Benar
                                                            </button>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <button type="button"
                                                                    @click="selectComplexTf(currentQuestion.id, stmt.no || (sIdx + 1), 'S')"
                                                                    style="padding: 0.42rem 0.9rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 800; cursor: pointer; transition: all 0.15s;"
                                                                    :style="getComplexTfVal(currentQuestion.id, stmt.no || (sIdx + 1)) === 'S'
                                                                        ? 'background:#e11d48;color:#ffffff;border:2px solid #be123c;'
                                                                        : 'background:transparent;color:#475569;border:2px solid #cbd5e1;'">
                                                                Salah
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>

                                {{-- TYPE 3: SHORT ANSWER (UTBK Isian Singkat Numerik/Teks) --}}
                                <template x-if="currentQuestion.question_type === 'short_answer'">
                                    <div style="margin-top: 1rem;">
                                        <label style="display: block; font-size: 0.85rem; font-weight: 800; color: #0a4282; margin-bottom: 0.45rem;" class="dark:text-amber-400">
                                            Ketik Jawaban Singkat Anda:
                                        </label>
                                        <input type="text"
                                               :value="typeof answers[currentQuestion.id] === 'string' ? answers[currentQuestion.id] : ''"
                                               @input="setShortAnswer(currentQuestion.id, $event.target.value)"
                                               placeholder="Masukkan angka atau jawaban singkat..."
                                               style="width: 100%; max-width: 380px; padding: 0.85rem 1.1rem; border-radius: 0.85rem; border: 2px solid #0a4282; font-size: 1.05rem; font-weight: 700; background: #ffffff; color: #0f172a; user-select: text; -webkit-user-select: text;">
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Bottom Navigation Actions -->
                        <div class="cbt-flex-between" style="margin-top: 0.5rem;">
                            <button type="button"
                                    @click="if (currentIndex > 0) currentIndex--"
                                    :disabled="currentIndex === 0"
                                    class="cbt-btn cbt-btn-secondary">
                                <x-heroicon-m-arrow-left style="width: 1.1rem; height: 1.1rem; margin-right: 0.4rem;" />
                                <span>Sebelumnya</span>
                            </button>

                            <template x-if="currentQuestion">
                                <button type="button"
                                        @click="toggleDoubt(currentQuestion.id)"
                                        class="cbt-btn cbt-btn-gold">
                                    <span x-text="doubts[currentQuestion.id] ? 'Batal Ragu' : 'Tandai Ragu-Ragu'"></span>
                                </button>
                            </template>

                            <button type="button"
                                    x-show="currentIndex < questions.length - 1"
                                    @click="if (currentIndex < questions.length - 1) currentIndex++"
                                    class="cbt-btn cbt-btn-primary">
                                <span>Selanjutnya</span>
                                <x-heroicon-m-arrow-right style="width: 1.1rem; height: 1.1rem; margin-left: 0.4rem; color: #fbb900;" />
                            </button>

                            <button type="button"
                                    x-show="currentIndex >= questions.length - 1"
                                    @click="submitAll(false)"
                                    :disabled="submitting"
                                    class="cbt-btn cbt-btn-success">
                                <span x-text="submitting ? 'Mengirim...' : 'Selesai & Submit'"></span>
                                <x-heroicon-m-check style="width: 1.1rem; height: 1.1rem; margin-left: 0.4rem;" />
                            </button>
                        </div>
                    </div>

                    <!-- Right Panel: Answer Mapping -->
                    <div>
                        <div class="glass-panel" style="position: sticky; top: 1rem; padding: 1.35rem; margin-bottom: 0;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                                <h3 style="font-weight: 800; font-size: 1rem; margin: 0; display: flex; align-items: center; gap: 0.45rem;">
                                    <x-heroicon-m-squares-2x2 style="width: 1.15rem; height: 1.15rem; color: #0a4282;" class="dark:text-amber-400" />
                                    <span>Peta Jawaban</span>
                                </h3>
                                <span style="font-size: 0.78rem; font-weight: 800; color: #0a4282;" class="dark:text-amber-400" x-text="progressPercent() + '%'"></span>
                            </div>

                            <!-- Progress bar -->
                            <div style="width: 100%; height: 6px; border-radius: 9999px; background: rgba(148, 163, 184, 0.25); overflow: hidden; margin-bottom: 0.9rem;">
                                <div style="height: 100%; background: linear-gradient(90deg, #0a4282, #fbb900); transition: width 0.25s ease;" :style="'width: ' + progressPercent() + '%'"></div>
                            </div>

                            <!-- Summary Counters -->
                            <div class="cbt-map-stats">
                                <div class="cbt-map-stat" style="background: rgba(10, 66, 130, 0.12); color: #0a4282;" class="dark:text-sky-300">
                                    <div style="font-size: 1rem; font-weight: 900;" x-text="answeredCount()"></div>
                                    <div>Dijawab</div>
                                </div>
                                <div class="cbt-map-stat" style="background: rgba(251, 185, 0, 0.22); color: #92400e;" class="dark:text-amber-300">
                                    <div style="font-size: 1rem; font-weight: 900;" x-text="doubtCount()"></div>
                                    <div>Ragu</div>
                                </div>
                                <div class="cbt-map-stat" style="background: rgba(148, 163, 184, 0.18); color: #475569;" class="dark:text-slate-300">
                                    <div style="font-size: 1rem; font-weight: 900;" x-text="unansweredCount()"></div>
                                    <div>Kosong</div>
                                </div>
                            </div>

                            <div class="cbt-nav-container">
                                <template x-for="(q, idx) in questions" :key="q.id">
                                    <button type="button"
                                            @click="currentIndex = idx"
                                            class="cbt-nav-btn"
                                            :class="{
                                                'active': currentIndex === idx,
                                                'doubt': doubts[q.id],
                                                'answered': !doubts[q.id] && isQuestionAnswered(q)
                                            }">
                                        <span x-text="idx + 1"></span>
                                        <span class="cbt-nav-sub" x-show="navBadgeText(q)" x-text="navBadgeText(q)"></span>
                                    </button>
                                </template>
                            </div>

                            <button type="button"
                                    @click="submitAll(false)"
                                    :disabled="submitting"
                                    class="cbt-btn cbt-btn-success"
                                    style="width: 100%; margin-top: 1.25rem;">
                                <span x-text="submitting ? 'Memproses...' : 'Selesai & Submit Ujian'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
