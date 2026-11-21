<x-filament-panels::page>
    <style>
        .cbt-container {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #1f2937;
            line-height: 1.5;
        }
        .dark .cbt-container {
            color: #f3f4f6;
        }

        .exam-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 1.25rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .dark .exam-card {
            background: rgba(30, 41, 59, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
        }
        .exam-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: rgba(59, 130, 246, 0.5);
        }
        .dark .exam-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
            border-color: rgba(59, 130, 246, 0.5);
        }
        .exam-card-header {
            padding: 1.5rem;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(37, 99, 235, 0.05));
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }
        .dark .exam-card-header {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(37, 99, 235, 0.05));
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .exam-card-icon {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 8px 15px -3px rgba(59, 130, 246, 0.4);
            flex-shrink: 0;
        }
        .exam-card-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #111827;
            line-height: 1.3;
            margin: 0 0 0.25rem 0;
        }
        .dark .exam-card-title { color: #f9fafb; }
        .exam-card-subtitle {
            font-size: 0.9rem;
            color: #6b7280;
            margin: 0;
            font-weight: 500;
        }
        .dark .exam-card-subtitle { color: #9ca3af; }
        .exam-card-body {
            padding: 1.5rem;
            flex-grow: 1;
        }
        .exam-description {
            font-size: 0.95rem;
            color: #4b5563;
            margin: 0 0 1.25rem 0;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .dark .exam-description { color: #d1d5db; }
        
        .exam-detail-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.85rem;
            font-size: 0.9rem;
            color: #374151;
            background: rgba(0,0,0,0.02);
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
        }
        .dark .exam-detail-item {
            color: #e5e7eb;
            background: rgba(255,255,255,0.02);
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
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            font-weight: 700;
            font-size: 1.05rem;
            border-radius: 1rem;
            transition: all 0.2s;
            box-shadow: 0 4px 15px -3px rgba(59, 130, 246, 0.4);
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .btn-take-exam:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -5px rgba(59, 130, 246, 0.5);
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }
        .btn-locked {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 1rem 1.5rem;
            background: #e5e7eb;
            color: #6b7280;
            font-weight: 700;
            font-size: 1.05rem;
            border-radius: 1rem;
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
            gap: 2rem;
        }
        @media (min-width: 768px) {
            .exam-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (min-width: 1024px) {
            .exam-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            border: 2px dashed #cbd5e1;
            margin: 2rem 0;
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
                            <div>
                                <h3 class="exam-card-title">{{ $exam->title }}</h3>
                                <p class="exam-card-subtitle">{{ $exam->subject->name ?? 'No Subject' }}</p>
                            </div>
                        </div>
                        
                        <div class="exam-card-body">
                            @if($exam->description)
                                <p class="exam-description">{{ $exam->description }}</p>
                            @endif
                            
                            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                <div class="exam-detail-item">
                                    <x-heroicon-o-clock style="width: 1.25rem; height: 1.25rem; color: #3b82f6;" />
                                    <span>Duration: <strong>{{ $exam->duration_minutes }} Minutes</strong></span>
                                </div>
                                <div class="exam-detail-item">
                                    <x-heroicon-o-arrow-path style="width: 1.25rem; height: 1.25rem; color: #3b82f6;" />
                                    <span>Kesempatan Masuk (Resume): <strong><span style="color: {{ $remainingAttempts == 0 ? '#ef4444' : '#10b981' }}">{{ $remainingAttempts }}</span> / {{ $exam->max_attempts }}</strong></span>
                                </div>
                                @if($exam->start_time || $exam->end_time)
                                    <div class="exam-detail-item">
                                        <x-heroicon-o-calendar style="width: 1.25rem; height: 1.25rem; color: #3b82f6;" />
                                        <span>
                                            @if($exam->start_time) {{ \Carbon\Carbon::parse($exam->start_time)->format('d M Y H:i') }} @endif
                                            @if($exam->start_time && $exam->end_time) - @endif
                                            @if($exam->end_time) {{ \Carbon\Carbon::parse($exam->end_time)->format('d M Y H:i') }} @endif
                                        </span>
                                    </div>
                                @endif
                            </div>

                            @if($isCompleted)
                                <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px dashed rgba(0,0,0,0.1);">
                                    <p style="font-size: 0.85rem; font-weight: 700; color: #6b7280; margin-bottom: 0.5rem; text-transform: uppercase;">Skor Akhir Anda</p>
                                    <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(16, 185, 129, 0.1); padding: 0.5rem 0.75rem; border-radius: 0.5rem; border: 1px solid rgba(16, 185, 129, 0.2);">
                                        <span style="font-size: 0.85rem; color: #065f46; font-weight: 600;">Selesai</span>
                                        <span style="font-size: 1.5rem; font-weight: 800; color: #10b981;">{{ $attempt->score }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="exam-card-footer">
                            @if($canTake)
                                <a href="{{ route('filament.student.pages.take-cbt-exam.{record}', ['record' => $exam->id]) }}" class="btn-take-exam">
                                    {{ $hasAttempt ? 'Lanjutkan Ujian (Resume)' : 'Mulai Ujian' }}
                                    <x-heroicon-m-arrow-right style="width: 1.25rem; height: 1.25rem; margin-left: 0.5rem;" />
                                </a>
                            @elseif($isCompleted)
                                <a href="{{ route('filament.student.pages.take-cbt-exam.{record}', ['record' => $exam->id]) }}" class="btn-take-exam" style="background: #10b981;">
                                    Lihat Hasil
                                    <x-heroicon-m-clipboard-document-check style="width: 1.25rem; height: 1.25rem; margin-left: 0.5rem;" />
                                </a>
                            @else
                                <button disabled class="btn-locked">
                                    <x-heroicon-m-lock-closed style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;" />
                                    Terkunci (Hubungi Guru)
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <x-heroicon-o-document-magnifying-glass style="width: 4rem; height: 4rem; margin: 0 auto 1rem auto; color: #94a3b8;" />
                <h3 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 0 0 0.5rem 0;">No Exams Available</h3>
                <p style="color: #64748b; font-size: 1.1rem; margin: 0;">There are currently no CBT exams available for your class.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
