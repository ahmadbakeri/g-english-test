<x-filament-panels::page>
    <style>
        .cbt-container {
            font-family: "Outfit", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #1f2937;
            line-height: 1.5;
        }
        .dark .cbt-container {
            color: #f3f4f6;
        }

        .exam-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px) saturate(180%);
            -webkit-backdrop-filter: blur(12px) saturate(180%);
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 1.25rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .dark .exam-card {
            background: rgba(30, 41, 59, 0.78);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.45);
        }
        .exam-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(10, 66, 130, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.03);
            border-color: rgba(10, 66, 130, 0.35);
        }
        .dark .exam-card:hover {
            border-color: rgba(251, 185, 0, 0.45);
        }
        .exam-card-header {
            padding: 1.35rem 1.5rem;
            background: linear-gradient(135deg, rgba(10, 66, 130, 0.08), rgba(251, 185, 0, 0.06));
            border-bottom: 1px solid rgba(15, 23, 42, 0.06);
            display: flex;
            align-items: center;
            gap: 1.1rem;
        }
        .dark .exam-card-header {
            background: linear-gradient(135deg, rgba(10, 66, 130, 0.28), rgba(251, 185, 0, 0.08));
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .exam-card-icon {
            width: 3.25rem;
            height: 3.25rem;
            border-radius: 0.9rem;
            background: linear-gradient(135deg, #0a4282, #1a368b);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fbb900;
            box-shadow: 0 6px 14px -3px rgba(10, 66, 130, 0.35);
            flex-shrink: 0;
        }
        .exam-card-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.3;
            margin: 0 0 0.25rem 0;
        }
        .dark .exam-card-title { color: #f8fafc; }
        .exam-card-subtitle {
            font-size: 0.85rem;
            color: #64748b;
            margin: 0;
            font-weight: 600;
        }
        .dark .exam-card-subtitle { color: #94a3b8; }
        .exam-card-body {
            padding: 1.35rem 1.5rem;
            flex-grow: 1;
        }
        .exam-description {
            font-size: 0.9rem;
            color: #475569;
            margin: 0 0 1.15rem 0;
            line-height: 1.55;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .dark .exam-description { color: #cbd5e1; }

        .exam-detail-item {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            margin-bottom: 0.65rem;
            font-size: 0.875rem;
            color: #334155;
            background: rgba(248, 250, 252, 0.9);
            border: 1px solid rgba(148, 163, 184, 0.18);
            padding: 0.55rem 0.8rem;
            border-radius: 0.65rem;
        }
        .dark .exam-detail-item {
            color: #e2e8f0;
            background: rgba(15, 23, 42, 0.5);
            border-color: rgba(255, 255, 255, 0.06);
        }

        .exam-card-footer {
            padding: 0 1.5rem 1.5rem 1.5rem;
            margin-top: auto;
        }
        .btn-take-exam {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.9rem 1.5rem;
            background: linear-gradient(135deg, #0a4282, #1a368b);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.975rem;
            border-radius: 9999px;
            transition: all 0.2s;
            box-shadow: 0 6px 15px -3px rgba(10, 66, 130, 0.35);
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .btn-take-exam:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(10, 66, 130, 0.5);
        }
        .btn-locked {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.9rem 1.5rem;
            background: #e2e8f0;
            color: #64748b;
            font-weight: 700;
            font-size: 0.975rem;
            border-radius: 9999px;
            cursor: not-allowed;
            border: none;
        }
        .dark .btn-locked {
            background: #334155;
            color: #94a3b8;
        }
        .exam-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1.75rem;
        }
        @media (min-width: 768px) {
            .exam-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (min-width: 1024px) {
            .exam-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        .empty-state {
            text-align: center;
            padding: 4.5rem 2rem;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            border: 2px dashed #cbd5e1;
            margin: 1.5rem 0;
        }
        .dark .empty-state {
            background: rgba(30, 41, 59, 0.7);
            border-color: #475569;
        }
    </style>

    <div class="cbt-container">
        @if(count($exams) > 0)
            <div class="exam-grid">
                @foreach($exams as $exam)
                    @php
                        $student = auth()->user()->student;
                        $attempt = \App\Models\CbtAttempt::where('cbt_exam_id', $exam->id)
                            ->where('student_id', $student->id)
                            ->first();

                        $hasAttempt = $attempt !== null;
                        $isCompleted = $hasAttempt && $attempt->is_completed;
                        $isLocked = $hasAttempt && (!$isCompleted) && ($attempt->attempt_number >= $exam->max_attempts);
                        $canTake = !$isCompleted && !$isLocked;

                        $remainingAttempts = $hasAttempt ? max(0, $exam->max_attempts - $attempt->attempt_number) : $exam->max_attempts;
                    @endphp

                    <div class="exam-card">
                        <div class="exam-card-header">
                            <div class="exam-card-icon">
                                <x-heroicon-o-academic-cap style="width: 1.75rem; height: 1.75rem;" />
                            </div>
                            <div style="min-width: 0; flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.25rem;">
                                    <span style="padding: 0.15rem 0.6rem; border-radius: 9999px; font-size: 0.68rem; font-weight: 800; text-transform: uppercase; background: {{ ($exam->cbt_mode ?? 'google_form') === 'internal_excel' ? 'rgba(10, 66, 130, 0.12)' : 'rgba(14, 165, 233, 0.15)' }}; color: {{ ($exam->cbt_mode ?? 'google_form') === 'internal_excel' ? '#0a4282' : '#0284c7' }};" class="dark:text-amber-300">
                                        {{ ($exam->cbt_mode ?? 'google_form') === 'internal_excel' ? 'Real CBT' : 'Google Form' }}
                                    </span>
                                </div>
                                <h3 class="exam-card-title">{{ $exam->title }}</h3>
                                <p class="exam-card-subtitle">{{ $exam->subject->name ?? 'No Subject' }}</p>
                            </div>
                        </div>

                        <div class="exam-card-body">
                            @if($exam->description)
                                <p class="exam-description">{{ $exam->description }}</p>
                            @endif

                            <div>
                                <div class="exam-detail-item">
                                    <x-heroicon-o-clock style="width: 1.15rem; height: 1.15rem; color: #0a4282; flex-shrink: 0;" class="dark:text-amber-400" />
                                    <span>Durasi: <strong>{{ $exam->duration_minutes }} Menit</strong></span>
                                </div>
                                <div class="exam-detail-item">
                                    <x-heroicon-o-arrow-path style="width: 1.15rem; height: 1.15rem; color: #0a4282; flex-shrink: 0;" class="dark:text-amber-400" />
                                    <span>Kesempatan Masuk: <strong><span style="color: {{ $remainingAttempts == 0 ? '#ef4444' : '#10b981' }}">{{ $remainingAttempts }}</span> / {{ $exam->max_attempts }}</strong></span>
                                </div>
                                @if($exam->start_time || $exam->end_time)
                                    <div class="exam-detail-item">
                                        <x-heroicon-o-calendar style="width: 1.15rem; height: 1.15rem; color: #0a4282; flex-shrink: 0;" class="dark:text-amber-400" />
                                        <span>
                                            @if($exam->start_time) {{ \Carbon\Carbon::parse($exam->start_time)->format('d M Y H:i') }} @endif
                                            @if($exam->start_time && $exam->end_time) - @endif
                                            @if($exam->end_time) {{ \Carbon\Carbon::parse($exam->end_time)->format('d M Y H:i') }} @endif
                                        </span>
                                    </div>
                                @endif
                            </div>

                            @if($isCompleted)
                                <div style="margin-top: 1.1rem; padding-top: 0.9rem; border-top: 1px dashed rgba(148, 163, 184, 0.3);">
                                    @if($exam->isDeferredScoring() && $attempt->score === null)
                                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; background: rgba(251, 185, 0, 0.14); padding: 0.6rem 0.9rem; border-radius: 0.75rem; border: 1px solid rgba(251, 185, 0, 0.45);">
                                            <span style="font-size: 0.78rem; color: #92400e; font-weight: 800; text-transform: uppercase;" class="dark:text-amber-300">Status Nilai</span>
                                            <span style="font-size: 0.82rem; font-weight: 800; color: #b45309;" class="dark:text-amber-200">Menunggu Penilaian Admin</span>
                                        </div>
                                    @else
                                        <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(16, 185, 129, 0.1); padding: 0.6rem 0.9rem; border-radius: 0.75rem; border: 1px solid rgba(16, 185, 129, 0.25);">
                                            <span style="font-size: 0.8rem; color: #059669; font-weight: 700; text-transform: uppercase;">Skor Akhir</span>
                                            <span style="font-size: 1.45rem; font-weight: 900; color: #10b981;">{{ $attempt->score ?? '-' }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="exam-card-footer">
                            @if($canTake)
                                <a href="{{ route('filament.student.pages.take-cbt-exam.{record}', ['record' => $exam->id]) }}" class="btn-take-exam">
                                    <span>{{ $hasAttempt ? 'Lanjutkan Ujian (Resume)' : 'Mulai Ujian' }}</span>
                                    <x-heroicon-m-arrow-right style="width: 1.15rem; height: 1.15rem; margin-left: 0.5rem; color: #fbb900;" />
                                </a>
                            @elseif($isCompleted)
                                <a href="{{ route('filament.student.pages.take-cbt-exam.{record}', ['record' => $exam->id]) }}" class="btn-take-exam" style="background: linear-gradient(135deg, #10b981, #059669);">
                                    <span>Lihat Hasil</span>
                                    <x-heroicon-m-clipboard-document-check style="width: 1.15rem; height: 1.15rem; margin-left: 0.5rem;" />
                                </a>
                            @else
                                <button disabled class="btn-locked">
                                    <x-heroicon-m-lock-closed style="width: 1.15rem; height: 1.15rem; margin-right: 0.5rem;" />
                                    <span>Terkunci (Hubungi Guru)</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <x-heroicon-o-document-magnifying-glass style="width: 3.75rem; height: 3.75rem; margin: 0 auto 1rem auto; color: #0a4282;" class="dark:text-amber-400" />
                <h3 style="font-size: 1.4rem; font-weight: 800; color: #0f172a; margin: 0 0 0.4rem 0;" class="dark:text-white">No Exams Available</h3>
                <p style="color: #64748b; font-size: 1rem; margin: 0;">There are currently no CBT exams available for your class.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
