<x-filament-panels::page>
    <style>
        .grade-container {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #1f2937;
            line-height: 1.5;
        }
        .dark .grade-container {
            color: #f3f4f6;
        }
        
        .report-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 1.25rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            overflow: hidden;
            position: relative;
        }
        .dark .report-card {
            background: rgba(30, 41, 59, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
        }

        .report-header {
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(168, 85, 247, 0.05));
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }
        .dark .report-header {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(168, 85, 247, 0.05));
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .report-icon {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 1rem;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 8px 15px -3px rgba(99, 102, 241, 0.4);
            flex-shrink: 0;
        }
        .report-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #111827;
            margin: 0;
        }
        .dark .report-title { color: #f9fafb; }
        .report-subtitle {
            font-size: 1rem;
            color: #6b7280;
            margin: 0;
            font-weight: 600;
        }
        .dark .report-subtitle { color: #9ca3af; }
        
        .report-body {
            padding: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        @media (min-width: 768px) {
            .stats-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        .stat-box {
            background: rgba(0,0,0,0.02);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .stat-box:hover {
            transform: translateY(-4px);
        }
        .dark .stat-box {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.05);
        }
        
        .stat-label {
            font-size: 0.9rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }
        .dark .stat-label { color: #94a3b8; }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: 900;
            line-height: 1;
        }
        
        .val-attendance { color: #0ea5e9; }
        .val-cbt { color: #f59e0b; }
        .val-final { color: #10b981; }

        .stat-desc {
            font-size: 0.85rem;
            color: #94a3b8;
            margin-top: 0.5rem;
        }

        .formula-bar {
            background: rgba(99, 102, 241, 0.1);
            padding: 1rem;
            border-radius: 0.75rem;
            text-align: center;
            font-size: 0.95rem;
            color: #4f46e5;
            font-weight: 600;
            margin-bottom: 2rem;
            border: 1px dashed rgba(99, 102, 241, 0.3);
        }
        .dark .formula-bar {
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
            border-color: rgba(99, 102, 241, 0.3);
        }

        .history-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(0,0,0,0.05);
        }
        .dark .history-title {
            color: #f1f5f9;
            border-bottom-color: rgba(255,255,255,0.05);
        }

        .history-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .history-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
            padding: 1rem 1.25rem;
            border-radius: 0.75rem;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .dark .history-item {
            background: rgba(30, 41, 59, 0.5);
            border-color: rgba(255,255,255,0.05);
        }
        .history-date {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 0.25rem;
        }
        .history-note {
            font-size: 1rem;
            font-weight: 600;
            color: #334155;
        }
        .dark .history-note { color: #e2e8f0; }
        .history-score {
            font-size: 1.25rem;
            font-weight: 800;
            color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
            padding: 0.25rem 1rem;
            border-radius: 2rem;
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

    <div class="grade-container">
        @if(count($reportCards) > 0)
            @foreach($reportCards as $report)
                <div class="report-card">
                    <div class="report-header">
                        <div class="report-icon">
                            <x-heroicon-o-academic-cap style="width: 2rem; height: 2rem;" />
                        </div>
                        <div>
                            <h2 class="report-title">{{ $report['subject_name'] }}</h2>
                            <p class="report-subtitle">Class: {{ $report['class_name'] }}</p>
                        </div>
                    </div>

                    <div class="report-body">
                        <div class="formula-bar">
                            <x-heroicon-o-calculator style="width: 1.25rem; height: 1.25rem; display: inline-block; vertical-align: text-bottom; margin-right: 0.5rem;" />
                            Final Grade Formula = (Attendance &times; 40%) + (CBT Average &times; 60%)
                        </div>

                        <div class="stats-grid">
                            <div class="stat-box">
                                <div class="stat-label">Attendance Score (40%)</div>
                                <div class="stat-value val-attendance">{{ $report['attendance_score'] }}</div>
                                <div class="stat-desc">{{ $report['present_sessions'] }} / {{ $report['total_sessions'] }} Sessions Present</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-label">CBT Average (60%)</div>
                                <div class="stat-value val-cbt">{{ $report['cbt_average'] }}</div>
                                <div class="stat-desc">From {{ count($report['evaluations']) }} Exam(s) Taken</div>
                            </div>
                            <div class="stat-box" style="background: rgba(16, 185, 129, 0.05); border-color: rgba(16, 185, 129, 0.2);">
                                <div class="stat-label" style="color: #059669;">Final Grade</div>
                                <div class="stat-value val-final">{{ $report['final_grade'] }}</div>
                                <div class="stat-desc">Total Calculated Score</div>
                            </div>
                        </div>

                        <div>
                            <h3 class="history-title">Rincian Nilai per Item (Ujian & Evaluasi)</h3>
                            @if(count($report['evaluations']) > 0)
                                <div class="history-list">
                                    @foreach($report['evaluations'] as $eval)
                                        <div class="history-item">
                                            <div>
                                                <div class="history-date">{{ \Carbon\Carbon::parse($eval->date)->format('F d, Y') }}</div>
                                                <div class="history-note">{{ $eval->notes ?: 'CBT Exam / Assignment' }}</div>
                                            </div>
                                            <div class="history-score">
                                                {{ $eval->score }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p style="color: #64748b; text-align: center; padding: 2rem 0; background: rgba(0,0,0,0.02); border-radius: 0.5rem; font-style: italic;">No evaluation history found for this subject.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <x-heroicon-o-document-magnifying-glass style="width: 4rem; height: 4rem; margin: 0 auto 1rem auto; color: #94a3b8;" />
                <h3 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 0 0 0.5rem 0;">No Grades Available</h3>
                <p style="color: #64748b; font-size: 1.1rem; margin: 0;">You are not enrolled in any classes or have no grade reports yet.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
