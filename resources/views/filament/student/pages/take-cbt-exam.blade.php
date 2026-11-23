<x-filament-panels::page>
    <style>
        .cbt-container {
            font-family: "Outfit", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #0f172a;
            line-height: 1.5;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }
        .dark .cbt-container {
            color: #f8fafc;
        }

        /* True Fullscreen Shell — hides all Filament sidebars, topbars & menus */
        .cbt-fullscreen-shell {
            width: 100%;
        }
        .cbt-fullscreen-shell:fullscreen,
        .cbt-fullscreen-shell:-webkit-full-screen {
            width: 100vw !important;
            height: 100vh !important;
            overflow-y: auto !important;
            padding: 1.25rem 1.75rem 2rem 1.75rem !important;
            background: radial-gradient(circle at top right, rgba(251, 185, 0, 0.08), transparent 35%),
                        radial-gradient(circle at top left, rgba(10, 66, 130, 0.10), transparent 40%),
                        #f1f5f9 !important;
            box-sizing: border-box;
        }
        .dark .cbt-fullscreen-shell:fullscreen,
        .dark .cbt-fullscreen-shell:-webkit-full-screen {
            background: radial-gradient(circle at top right, rgba(251, 185, 0, 0.07), transparent 35%),
                        radial-gradient(circle at top left, rgba(10, 66, 130, 0.25), transparent 40%),
                        #0b1120 !important;
        }

        /* Glass Panel */
        .glass-panel {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(14px) saturate(180%);
            -webkit-backdrop-filter: blur(14px) saturate(180%);
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 1.25rem;
            box-shadow: 0 12px 30px -8px rgba(10, 66, 130, 0.08);
            padding: 1.6rem;
            margin-bottom: 1.25rem;
        }
        .dark .glass-panel {
            background: rgba(30, 41, 59, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 12px 30px -8px rgba(0, 0, 0, 0.45);
        }

        /* Sticky Exam Header */
        .cbt-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 1rem 1.5rem;
            border-radius: 1.25rem;
            background: linear-gradient(135deg, #0a4282 0%, #102a5c 100%);
            color: #ffffff;
            border-bottom: 4px solid #fbb900;
            box-shadow: 0 12px 28px -6px rgba(10, 66, 130, 0.35);
            margin-bottom: 1.25rem;
        }

        /* Grid Layout */
        .cbt-grid {
            display: grid;
            gap: 1.35rem;
        }
        .cbt-grid-cols-4 {
            grid-template-columns: 1fr;
        }
        @media (min-width: 1024px) {
            .cbt-grid-cols-4 {
                grid-template-columns: minmax(0, 1fr) 330px;
                align-items: start;
            }
        }
        .cbt-flex-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.85rem;
        }

        /* Pill Buttons */
        .cbt-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.78rem 1.5rem;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.925rem;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
        }
        .cbt-btn-primary {
            background: linear-gradient(135deg, #0a4282, #1a368b);
            color: #ffffff;
            box-shadow: 0 4px 14px -3px rgba(10, 66, 130, 0.45);
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
            border: 1.5px solid #cbd5e1;
        }
        .dark .cbt-btn-secondary {
            background: #1e293b;
            color: #e2e8f0;
            border: 1.5px solid #334155;
        }
        .cbt-btn-secondary:hover:not(:disabled) {
            border-color: #0a4282;
            transform: translateY(-2px);
        }
        .cbt-btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
            box-shadow: 0 4px 14px -3px rgba(16, 185, 129, 0.45);
        }
        .cbt-btn-success:hover:not(:disabled) {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #059669, #047857);
        }
        .cbt-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
            transform: none !important;
        }

        .cbt-title {
            font-size: 1.65rem;
            font-weight: 800;
            margin-bottom: 0.35rem;
            color: #0a4282;
        }
        .dark .cbt-title { color: #f8fafc; }

        .cbt-subtitle {
            font-size: 0.95rem;
            color: #64748b;
            margin-bottom: 1.35rem;
        }
        .dark .cbt-subtitle { color: #94a3b8; }

        .cbt-question-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.3rem 0.85rem;
            border-radius: 9999px;
            background: rgba(10, 66, 130, 0.1);
            color: #0a4282;
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .dark .cbt-question-badge {
            background: rgba(251, 185, 0, 0.18);
            color: #fbb900;
        }

        .cbt-question-text {
            font-size: 1.18rem;
            font-weight: 600;
            margin: 1rem 0 1.6rem 0;
            color: #0f172a;
            line-height: 1.7;
        }
        .dark .cbt-question-text { color: #f1f5f9; }

        /* Options */
        .cbt-options-list {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }
        .cbt-option-label {
            display: flex;
            align-items: center;
            padding: 1rem 1.2rem;
            border-radius: 1rem;
            border: 2px solid #e2e8f0;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.16s ease;
        }
        .dark .cbt-option-label {
            border-color: #334155;
            background: #1e293b;
        }
        .cbt-option-label:hover {
            border-color: #0a4282;
            background: #f0f7ff;
            transform: translateX(4px);
        }
        .dark .cbt-option-label:hover {
            border-color: #fbb900;
            background: rgba(251, 185, 0, 0.08);
        }
        .cbt-option-label.selected {
            border-color: #0a4282;
            background: #eff6ff;
            box-shadow: 0 0 0 2px rgba(10, 66, 130, 0.18);
        }
        .dark .cbt-option-label.selected {
            background: rgba(10, 66, 130, 0.42);
            border-color: #fbb900;
            box-shadow: 0 0 0 2px rgba(251, 185, 0, 0.22);
        }
        .cbt-option-badge {
            width: 2.35rem;
            height: 2.35rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            color: #334155;
            font-weight: 800;
            border-radius: 0.7rem;
            margin-right: 1rem;
            flex-shrink: 0;
            font-size: 0.95rem;
            transition: all 0.16s ease;
        }
        .dark .cbt-option-badge {
            background: #0f172a;
            color: #cbd5e1;
        }
        .cbt-option-label.selected .cbt-option-badge {
            background: #0a4282;
            color: #fbb900;
        }
        .dark .cbt-option-label.selected .cbt-option-badge {
            background: #fbb900;
            color: #0a4282;
        }

        /* Timer & Violation Pills */
        .cbt-timer-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.5rem 1.05rem;
            background: rgba(255, 255, 255, 0.14);
            border: 1.5px solid rgba(251, 185, 0, 0.65);
            color: #fbb900;
            border-radius: 9999px;
            font-family: ui-monospace, monospace;
            font-weight: 900;
            font-size: 1.1rem;
            letter-spacing: 0.04em;
        }
        .cbt-violation-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem 0.9rem;
            border-radius: 9999px;
            background: rgba(244, 63, 94, 0.22);
            border: 1px solid rgba(253, 164, 175, 0.45);
            color: #fecdd3;
            font-size: 0.8rem;
            font-weight: 800;
        }

        /* Answer Mapping Grid */
        .cbt-map-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.5rem;
            margin-bottom: 0.9rem;
        }
        .cbt-map-stat {
            padding: 0.5rem 0.4rem;
            border-radius: 0.75rem;
            text-align: center;
            font-size: 0.72rem;
            font-weight: 700;
        }
        .cbt-nav-container {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 0.5rem;
        }
        .cbt-nav-btn {
            height: 2.75rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            font-weight: 800;
            font-size: 0.88rem;
            line-height: 1.1;
            background: #ffffff;
            border: 2px solid #cbd5e1;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .dark .cbt-nav-btn {
            background: #0f172a;
            border-color: #334155;
            color: #cbd5e1;
        }
        .cbt-nav-btn:hover {
            transform: translateY(-2px);
            border-color: #0a4282;
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
            outline: 3px solid #38bdf8;
            outline-offset: 2px;
            transform: scale(1.06);
            z-index: 5;
        }
        .cbt-nav-sub {
            font-size: 0.65rem;
            opacity: 0.85;
            font-weight: 700;
        }

        /* Google Form Iframe Wrapper */
        .gform-iframe-wrapper {
            width: 100%;
            height: 75vh;
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid rgba(15, 23, 42, 0.1);
            background: #ffffff;
        }
        .cbt-fullscreen-shell:fullscreen .gform-iframe-wrapper,
        .cbt-fullscreen-shell:-webkit-full-screen .gform-iframe-wrapper {
            height: calc(100vh - 105px) !important;
        }
        /* Sleek Modal Backdrop inside Fullscreen Shell */
        .cbt-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(11, 17, 32, 0.92);
            backdrop-filter: blur(12px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .cbt-modal-card {
            max-width: 480px;
            width: 100%;
            text-align: center;
            margin: 0;
            border-radius: 1.5rem;
            padding: 2rem;
            background: #ffffff;
            border: 2px solid rgba(10, 66, 130, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .dark .cbt-modal-card {
            background: #1e293b;
            border-color: rgba(251, 185, 0, 0.45);
        }
        .gform-iframe-wrapper iframe {
            width: 100%;
            height: 100%;
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

    <script>
        window.cbtGformApp = function (configId, wire) {
            let cfg = {};
            try {
                cfg = JSON.parse(document.getElementById(configId)?.textContent || '{}');
            } catch (e) {}
            return {
                endTime: new Date(cfg.endTimeIso || Date.now()).getTime(),
                timeLeft: '00:00:00',
                isTimeUp: false,
                submitting: false,
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
                finishExam() {
                    if (this.submitting) return;
                    this.submitting = true;
                    if (document.fullscreenElement) {
                        document.exitFullscreen().catch(() => {});
                    }
                    wire.submitExam();
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

        window.cbtExamApp = function (configId, wire) {
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

                doubtCount() {
                    return Object.keys(this.doubts).filter(k => this.doubts[k]).length;
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
                        this.showSubmitModal = true;
                        return;
                    }
                    this.showSubmitModal = false;
                    this.submitting = true;
                    if (document.fullscreenElement) {
                        document.exitFullscreen().catch(() => {});
                    }
                    wire.submitExam(this.answers).then(() => {
                        try {
                            localStorage.removeItem(this.storageKey);
                            localStorage.removeItem(this.doubtKey);
                        } catch (e) {}
                    });
                }
            };
        };
    </script>

    <div class="cbt-container"
         @contextmenu.prevent
         @copy.prevent
         @cut.prevent
         @paste.prevent>

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

                @if($exam->show_result_immediately && $attempt && $attempt->score !== null)
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
                        finishExam() {
                            if (this.submitting) return;
                            this.submitting = true;
                            if (document.fullscreenElement) {
                                document.exitFullscreen().catch(() => {});
                            }
                            wire.submitExam();
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
                                @click="finishExam()"
                                class="cbt-btn cbt-btn-success"
                                style="padding: 0.5rem 1.2rem; font-size: 0.85rem;">
                            <span>Akhiri Tes</span>
                            <x-heroicon-m-check style="width: 1.05rem; height: 1.05rem; margin-left: 0.35rem;" />
                        </button>
                    </div>
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

                <div style="margin-bottom: 2rem; padding: 1.35rem 1.5rem; border-left: 4px solid #0a4282; background: rgba(10, 66, 130, 0.05); border-radius: 0.85rem;">
                    <h3 style="font-weight: 800; font-size: 1.025rem; margin-bottom: 0.4rem;">Petunjuk Pengerjaan:</h3>
                    <p style="white-space: pre-line; line-height: 1.65; margin: 0; font-size: 0.95rem;">{{ $exam->description ?? 'Pilihlah jawaban yang paling tepat. Jawaban Anda tersimpan otomatis di perangkat dan dikirim sekaligus saat menekan tombol Selesai & Submit Ujian.' }}</p>
                </div>

                <button wire:click="startExam" class="cbt-btn cbt-btn-primary" style="width: 100%; padding: 1.15rem; font-size: 1.1rem;">
                    <span>Mulai Ujian Sekarang</span>
                    <x-heroicon-m-play style="width: 1.35rem; height: 1.35rem; margin-left: 0.5rem; color: #fbb900;" />
                </button>
            </div>

        {{-- ========================================================= --}}
        {{-- MODE B: INTERNAL EXCEL - ACTIVE EXAM (TRUE FULLSCREEN)    --}}
        {{-- ========================================================= --}}
        @else
            @php
                $examConfig = [
                    'storageKey' => 'g_english_cbt_attempt_' . $attempt->id,
                    'doubtKey' => 'g_english_cbt_doubt_' . $attempt->id,
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

                        doubtCount() {
                            return Object.keys(this.doubts).filter(k => this.doubts[k]).length;
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
                                this.showSubmitModal = true;
                                return;
                            }
                            this.showSubmitModal = false;
                            this.submitting = true;
                            if (document.fullscreenElement) {
                                document.exitFullscreen().catch(() => {});
                            }
                            wire.submitExam(this.answers).then(() => {
                                try {
                                    localStorage.removeItem(this.storageKey);
                                    localStorage.removeItem(this.doubtKey);
                                } catch (e) {}
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
                            <div class="glass-panel" style="padding: 1.85rem;">
                                <div class="cbt-flex-between" style="border-bottom: 1px solid rgba(148, 163, 184, 0.22); padding-bottom: 0.85rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span class="cbt-question-badge">
                                            Soal #<span x-text="currentIndex + 1"></span>
                                        </span>
                                        <span x-show="doubts[currentQuestion.id]" style="padding: 0.25rem 0.7rem; border-radius: 9999px; background: #fbb900; color: #0a4282; font-size: 0.72rem; font-weight: 800;">
                                            Ragu-Ragu
                                        </span>
                                    </div>
                                    <span style="font-size: 0.82rem; font-weight: 700; color: #64748b;" class="dark:text-slate-300">
                                        Bobot: <strong x-text="currentQuestion.points || 1"></strong> Poin
                                    </span>
                                </div>

                                <div class="cbt-question-text" x-html="currentQuestion.question_text"></div>

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

                                <div class="cbt-options-list">
                                    <template x-for="(optText, optKey) in currentQuestion.options" :key="optKey">
                                        <div @click="selectOption(currentQuestion.id, optKey)"
                                             class="cbt-option-label"
                                             :class="{ 'selected': answers[currentQuestion.id] === optKey }">
                                            <span class="cbt-option-badge" x-text="optKey"></span>
                                            <span style="font-size: 1rem; font-weight: 600;" x-text="optText"></span>
                                        </div>
                                    </template>
                                </div>
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
                                                'answered': !doubts[q.id] && answers[q.id]
                                            }">
                                        <span x-text="idx + 1"></span>
                                        <span class="cbt-nav-sub" x-show="answers[q.id]" x-text="answers[q.id]"></span>
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
