<x-filament-panels::page>
    <style>
        /* Base Reset & Variables for CBT */
        .cbt-container {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
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

        /* Glass Panel */
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
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
        }
        .cbt-flex-center {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Buttons */
        .cbt-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 2rem;
            border-radius: 1rem;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            border: none;
            text-decoration: none;
        }
        .cbt-btn-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            box-shadow: 0 4px 15px -3px rgba(59, 130, 246, 0.4);
        }
        .cbt-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -5px rgba(59, 130, 246, 0.5);
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }
        .cbt-btn-secondary {
            background: #ffffff;
            color: #4b5563;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .dark .cbt-btn-secondary {
            background: #1e293b;
            color: #cbd5e1;
            border: 1px solid #334155;
        }
        .cbt-btn-secondary:hover:not(:disabled) {
            background: #f3f4f6;
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
        .cbt-btn-success:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px);
        }

        /* Typography & Header */
        .cbt-title {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: #111827;
        }
        .dark .cbt-title { color: #f9fafb; }
        
        .cbt-subtitle {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 1.5rem;
        }
        .dark .cbt-subtitle { color: #9ca3af; }

        .cbt-question-text {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 2rem;
            color: #1f2937;
            line-height: 1.6;
        }
        .dark .cbt-question-text { color: #f3f4f6; }

        /* Options */
        .cbt-options-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .cbt-option-input {
            display: none !important;
        }
        .cbt-option-label {
            display: flex;
            align-items: center;
            padding: 1.25rem;
            border-radius: 1rem;
            border: 2px solid #e5e7eb;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .dark .cbt-option-label {
            border-color: #334155;
            background: #1e293b;
        }
        .cbt-option-label:hover {
            border-color: #93c5fd;
            background: #eff6ff;
            transform: translateX(4px);
        }
        .dark .cbt-option-label:hover {
            border-color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
        }
        .cbt-option-input:checked + .cbt-option-label {
            border-color: #3b82f6;
            background: #eff6ff;
            box-shadow: 0 0 0 1px #3b82f6;
        }
        .dark .cbt-option-input:checked + .cbt-option-label {
            background: rgba(59, 130, 246, 0.2);
            border-color: #60a5fa;
            box-shadow: 0 0 0 1px #60a5fa;
        }
        .cbt-option-badge {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            color: #4b5563;
            font-weight: 700;
            border-radius: 0.5rem;
            margin-right: 1.5rem;
            flex-shrink: 0;
            font-size: 1.1rem;
        }
        .dark .cbt-option-badge {
            background: #0f172a;
            color: #cbd5e1;
        }
        .cbt-option-input:checked + .cbt-option-label .cbt-option-badge {
            background: #3b82f6;
            color: white;
        }

        /* Timer Box */
        .cbt-timer {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.25rem;
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 2px solid rgba(239, 68, 68, 0.2);
            border-radius: 0.75rem;
            font-family: monospace;
            font-weight: 800;
            font-size: 1.25rem;
        }
        .dark .cbt-timer {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.3);
        }

        /* Nav Grid */
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
            border: 2px solid #e5e7eb;
            color: #4b5563;
            cursor: pointer;
            transition: all 0.2s;
        }
        .dark .cbt-nav-btn {
            background: #1e293b;
            border-color: #334155;
            color: #cbd5e1;
        }
        .cbt-nav-btn:hover {
            background: #f3f4f6;
            transform: scale(1.05);
        }
        .dark .cbt-nav-btn:hover {
            background: #334155;
        }
        .cbt-nav-btn.active {
            background: #3b82f6;
            border-color: #3b82f6;
            color: white;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.4);
            transform: scale(1.1);
            z-index: 10;
        }
        .cbt-nav-btn.answered:not(.active) {
            background: #10b981;
            border-color: #10b981;
            color: white;
        }
        
        .cbt-legend-item {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            color: #4b5563;
        }
        .dark .cbt-legend-item { color: #9ca3af; }
        .cbt-dot {
            width: 0.75rem;
            height: 0.75rem;
            border-radius: 50%;
            margin-right: 0.5rem;
        }

        .cbt-icon {
            width: 1.5rem;
            height: 1.5rem;
            display: inline-block;
        /* Modal Layout Fix */
        .cbt-modal-overlay {
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 999999;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            color: white;
        }
    </style>

    <div class="cbt-container"
        @contextmenu.prevent
        @copy.prevent
        @cut.prevent
        @paste.prevent
        @if(isset($attempt) && $attempt && !$isFinished)
        x-data="{
            warningVisible: false,
            timeLeft: 30,
            interval: null,
            paused: false,
            handleVisibilityChange() {
                if (document.hidden) {
                    this.warningVisible = true;
                    this.paused = false;
                    this.interval = setInterval(() => {
                        this.timeLeft--;
                        if (this.timeLeft <= 0) {
                            clearInterval(this.interval);
                            $wire.cancelExam();
                            this.warningVisible = false;
                        }
                    }, 1000);
                } else {
                    if (this.warningVisible && this.timeLeft > 0) {
                        clearInterval(this.interval);
                        this.paused = true;
                    }
                }
            },
            dismissWarning() {
                this.warningVisible = false;
            }
        }"
        @visibilitychange.window="handleVisibilityChange"
        @endif
    >
        @if(isset($attempt) && $attempt && !$isFinished)
        <template x-teleport="body">
            <div x-show="warningVisible" x-transition.opacity.duration.300ms class="cbt-modal-overlay" style="display: none;">
                <div style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); padding: 3rem; border-radius: 1.5rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); text-align: center; max-width: 600px; width: 90%; margin: 0 auto;"
                     x-show="warningVisible" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-8 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                    <div style="width: 5rem; height: 5rem; background: rgba(239, 68, 68, 0.2); border: 2px solid #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
                        <x-heroicon-o-exclamation-triangle style="width: 3rem; height: 3rem; color: #ef4444;" />
                    </div>
                    <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 0.5rem; color: #f87171;">PERINGATAN KECURANGAN!</h1>
                    <p style="font-size: 1.125rem; color: #cbd5e1; margin-bottom: 2rem; line-height: 1.6;">Anda terdeteksi berpindah tab atau aplikasi. Harap patuhi aturan ujian. Ujian Anda akan dibatalkan secara otomatis jika countdown mencapai 0!</p>
                    
                    <div style="background: rgba(0,0,0,0.3); border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem;">
                        <p style="font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin-bottom: 0.5rem;">Sisa Waktu Toleransi</p>
                        <div style="font-size: 4rem; font-weight: 900; font-family: ui-monospace, monospace; color: #ef4444; line-height: 1;" x-text="timeLeft"></div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-top: 0.5rem;" x-show="paused">Hitung mundur otomatis dijeda.</p>
                    </div>

                    <button x-show="paused" @click="dismissWarning()" style="background: linear-gradient(135deg, #ef4444, #b91c1c); color: white; padding: 1rem 2.5rem; border-radius: 9999px; font-weight: 700; font-size: 1.125rem; border: none; cursor: pointer; box-shadow: 0 4px 14px 0 rgba(239, 68, 68, 0.39); transition: all 0.2s ease;">
                        SAYA MENGERTI
                    </button>
                </div>
            </div>
        </template>
        @endif

        @if($isFinished)
            <div class="glass-panel" style="text-align: center; max-width: 600px; margin: 3rem auto;">
                <div style="width: 5rem; height: 5rem; background: #d1fae5; color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
                    <x-heroicon-o-check-badge style="width: 3rem; height: 3rem;" />
                </div>
                <h2 class="cbt-title" style="margin-bottom: 0.5rem;">Exam Submitted!</h2>
                <p class="cbt-subtitle">You have successfully completed the exam.</p>
                
                <div style="background: #eff6ff; border: 2px solid #3b82f6; border-radius: 1rem; padding: 2rem; margin: 2rem 0; display: inline-block;">
                    <p style="font-size: 0.875rem; color: #3b82f6; text-transform: uppercase; font-weight: 700; margin-bottom: 0.5rem;">Your Score</p>
                    <p style="font-size: 4rem; font-weight: 900; color: #1d4ed8; line-height: 1;">{{ $attempt->score }}</p>
                </div>
                
                <div style="margin-top: 1rem;">
                    <a href="{{ route('filament.student.pages.student-cbt-exams') }}" class="cbt-btn cbt-btn-primary">
                        <x-heroicon-m-arrow-left style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;" />
                        Back to Exams
                    </a>
                </div>
            </div>

        @elseif(!$attempt)
            <div class="glass-panel" style="max-width: 800px; margin: 2rem auto;">
                <h1 class="cbt-title">{{ $exam->title }}</h1>
                <p class="cbt-subtitle">{{ $exam->subject->name ?? '' }}</p>
                
                <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
                    <div style="flex: 1; padding: 1.25rem; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 1rem;">
                        <div style="font-size: 0.875rem; color: #3b82f6; font-weight: 600; display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <x-heroicon-o-clock style="width: 1.25rem; height: 1.25rem; margin-right: 0.25rem;" /> Duration
                        </div>
                        <div style="font-size: 1.5rem; font-weight: 800;">{{ $exam->duration_minutes }} Min</div>
                    </div>
                    <div style="flex: 1; padding: 1.25rem; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 1rem;">
                        <div style="font-size: 0.875rem; color: #10b981; font-weight: 600; display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <x-heroicon-o-document-text style="width: 1.25rem; height: 1.25rem; margin-right: 0.25rem;" /> Total Questions
                        </div>
                        <div style="font-size: 1.5rem; font-weight: 800;">{{ count($exam->questions) }} Qs</div>
                    </div>
                </div>

                <div style="margin-bottom: 2.5rem; padding: 1.5rem; border-left: 4px solid #3b82f6; background: rgba(59, 130, 246, 0.05); border-radius: 0.5rem;">
                    <h3 style="font-weight: 700; font-size: 1.1rem; margin-bottom: 0.5rem;">Instructions:</h3>
                    <p style="white-space: pre-line; line-height: 1.6;">{{ $exam->description ?? 'No specific instructions.' }}</p>
                </div>

                <button wire:click="startExam" class="cbt-btn cbt-btn-primary" style="width: 100%; padding: 1.25rem; font-size: 1.25rem;">
                    Start Exam Now
                    <x-heroicon-m-play style="width: 1.5rem; height: 1.5rem; margin-left: 0.5rem;" />
                </button>
            </div>

        @else
            <div class="cbt-grid cbt-grid-cols-4">
                <!-- Left Panel: Question -->
                <div class="cbt-col-span-3">
                    <!-- Header / Timer -->
                    <div class="glass-panel cbt-flex-between" style="padding: 1.25rem 2rem;">
                        <div>
                            <h2 style="font-weight: 800; font-size: 1.25rem; margin: 0;">{{ $exam->title }}</h2>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">Question {{ $currentQuestionIndex + 1 }} of {{ count($exam->questions) }}</p>
                        </div>
                        <div class="cbt-timer"
                             x-data="{ 
                                endTime: new Date('{{ $attempt->start_time->copy()->addMinutes($exam->duration_minutes)->toIso8601String() }}').getTime(),
                                timeLeft: '00:00:00',
                                init() {
                                    setInterval(() => {
                                        let now = new Date().getTime();
                                        let distance = this.endTime - now;
                                        if (distance < 0) {
                                            this.timeLeft = '00:00:00';
                                            $wire.submitExam();
                                        } else {
                                            let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                            let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                            this.timeLeft = (hours < 10 ? '0' : '') + hours + ':' + 
                                                            (minutes < 10 ? '0' : '') + minutes + ':' + 
                                                            (seconds < 10 ? '0' : '') + seconds;
                                        }
                                    }, 1000);
                                }
                             }">
                            <x-heroicon-o-clock style="width: 1.5rem; height: 1.5rem; margin-right: 0.5rem;" />
                            <span x-text="timeLeft"></span>
                        </div>
                    </div>

                    <!-- Question Body -->
                    @if(isset($exam->questions[$currentQuestionIndex]))
                        @php $q = $exam->questions[$currentQuestionIndex]; @endphp
                        <div class="glass-panel" style="padding: 2.5rem;">
                            <div class="cbt-question-text">
                                {{ $q->question_text }}
                            </div>

                            @if($q->attachment)
                                <div style="margin-top: 1.5rem; margin-bottom: 2rem; text-align: center;" wire:key="attachment-{{ $q->id }}">
                                    @php
                                        $ext = strtolower(pathinfo($q->attachment, PATHINFO_EXTENSION));
                                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']);
                                        $isAudio = in_array($ext, ['mp3', 'wav', 'ogg', 'm4a']);
                                        $url = Storage::url($q->attachment);
                                    @endphp
                                    @if($isImage)
                                        <img src="{{ $url }}" alt="Attachment" style="max-width: 100%; max-height: 400px; border-radius: 12px; display: inline-block; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                                    @elseif($isAudio)
                                        <audio controls style="width: 100%; max-width: 500px; display: inline-block;">
                                            <source src="{{ $url }}" type="audio/{{ $ext == 'mp3' ? 'mpeg' : $ext }}">
                                            Your browser does not support the audio element.
                                        </audio>
                                    @else
                                        <a href="{{ $url }}" target="_blank" style="color: var(--primary-600); text-decoration: underline; font-weight: 500;">Download Lampiran</a>
                                    @endif
                                </div>
                            @endif

                            <div class="cbt-options-list">
                                @foreach(['A' => $q->option_a, 'B' => $q->option_b, 'C' => $q->option_c, 'D' => $q->option_d, 'E' => $q->option_e] as $key => $opt)
                                    @if($opt)
                                        <div>
                                            <input type="radio" 
                                                   id="option_{{ $q->id }}_{{ $key }}" 
                                                   name="question_{{ $q->id }}" 
                                                   class="cbt-option-input" 
                                                   wire:click="selectAnswer({{ $q->id }}, '{{ $key }}')"
                                                   @if(isset($answers[$q->id]) && $answers[$q->id] == $key) checked @endif>
                                            <label for="option_{{ $q->id }}_{{ $key }}" class="cbt-option-label">
                                                <span class="cbt-option-badge">{{ $key }}</span>
                                                <span style="font-size: 1.1rem; font-weight: 500;">{{ $opt }}</span>
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="cbt-flex-between" style="margin-top: 1.5rem;">
                            <button wire:click="previousQuestion" @if($currentQuestionIndex == 0) disabled @endif class="cbt-btn cbt-btn-secondary">
                                <x-heroicon-m-arrow-left style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;" />
                                Previous
                            </button>
                            
                            @if($currentQuestionIndex < count($exam->questions) - 1)
                                <button wire:click="nextQuestion" class="cbt-btn cbt-btn-primary">
                                    Next Question
                                    <x-heroicon-m-arrow-right style="width: 1.25rem; height: 1.25rem; margin-left: 0.5rem;" />
                                </button>
                            @else
                                <button wire:click="submitExam" onclick="return confirm('Are you sure you want to submit the exam?')" class="cbt-btn cbt-btn-success">
                                    Finish & Submit
                                    <x-heroicon-m-check style="width: 1.25rem; height: 1.25rem; margin-left: 0.5rem;" />
                                </button>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Right Panel: Navigation Grid -->
                <div class="cbt-col-span-1">
                    <div class="glass-panel" style="position: sticky; top: 1.5rem;">
                        <h3 style="font-weight: 800; font-size: 1.1rem; margin: 0 0 1.5rem 0; display: flex; align-items: center; border-bottom: 2px solid rgba(0,0,0,0.05); padding-bottom: 1rem;">
                            <x-heroicon-m-squares-2x2 style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem; color: #3b82f6;" />
                            Navigation
                        </h3>
                        
                        <div class="cbt-nav-container">
                            @foreach($exam->questions as $index => $q)
                                @php
                                    $isAnswered = isset($answers[$q->id]);
                                    $isActive = $index == $currentQuestionIndex;
                                    $classes = 'cbt-nav-btn';
                                    if ($isActive) $classes .= ' active';
                                    elseif ($isAnswered) $classes .= ' answered';
                                @endphp
                                <button wire:click="setQuestion({{ $index }})" class="{{ $classes }}">
                                    {{ $index + 1 }}
                                </button>
                            @endforeach
                        </div>

                        <hr style="margin: 2rem 0; border: none; border-top: 1px solid rgba(0,0,0,0.1);">

                        <div>
                            <div class="cbt-legend-item">
                                <span class="cbt-dot" style="background: #3b82f6;"></span> Current Question
                            </div>
                            <div class="cbt-legend-item">
                                <span class="cbt-dot" style="background: #10b981;"></span> Answered
                            </div>
                            <div class="cbt-legend-item">
                                <span class="cbt-dot" style="background: #ffffff; border: 2px solid #e5e7eb;"></span> Unanswered
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
