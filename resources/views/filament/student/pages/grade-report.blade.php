<x-filament-panels::page>
    <style>
        .grade-container {
            font-family: "Outfit", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #1f2937;
            line-height: 1.5;
        }
        .dark .grade-container {
            color: #f3f4f6;
        }

        .report-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px) saturate(180%);
            -webkit-backdrop-filter: blur(12px) saturate(180%);
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 1.25rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            overflow: hidden;
            position: relative;
        }
        .dark .report-card {
            background: rgba(30, 41, 59, 0.78);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.45);
        }

        .report-header {
            padding: 1.4rem 1.85rem;
            background: linear-gradient(135deg, rgba(10, 66, 130, 0.08), rgba(251, 185, 0, 0.06));
            border-bottom: 1px solid rgba(15, 23, 42, 0.06);
            display: flex;
            align-items: center;
            gap: 1.15rem;
        }
        .dark .report-header {
            background: linear-gradient(135deg, rgba(10, 66, 130, 0.28), rgba(251, 185, 0, 0.08));
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .report-icon {
            width: 3.35rem;
            height: 3.35rem;
            border-radius: 0.95rem;
            background: linear-gradient(135deg, #0a4282, #1a368b);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fbb900;
            box-shadow: 0 8px 15px -3px rgba(10, 66, 130, 0.35);
            flex-shrink: 0;
        }
        .report-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: #0a4282;
            margin: 0;
        }
        .dark .report-title { color: #f8fafc; }
        .report-subtitle {
            font-size: 0.925rem;
            color: #64748b;
            margin: 0.15rem 0 0 0;
            font-weight: 600;
        }
        .dark .report-subtitle { color: #94a3b8; }

        .report-body {
            padding: 1.75rem 1.85rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.75rem;
        }
        @media (min-width: 768px) {
            .stats-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        .stat-box {
            background: rgba(248, 250, 252, 0.9);
            border-radius: 1rem;
            padding: 1.35rem;
            text-align: center;
            border: 1px solid rgba(148, 163, 184, 0.22);
            transition: transform 0.2s ease, border-color 0.2s ease;
        }
        .stat-box:hover {
            transform: translateY(-3px);
            border-color: rgba(10, 66, 130, 0.35);
        }
        .dark .stat-box {
            background: rgba(15, 23, 42, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .stat-label {
            font-size: 0.8rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }
        .dark .stat-label { color: #94a3b8; }

        .stat-value {
            font-size: 2.4rem;
            font-weight: 900;
            line-height: 1;
        }

        .val-attendance { color: #0a4282; }
        .dark .val-attendance { color: #38bdf8; }
        .val-cbt { color: #d97706; }
        .dark .val-cbt { color: #fbb900; }
        .val-final { color: #10b981; }

        .stat-desc {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 0.5rem;
            font-weight: 600;
        }
        .dark .stat-desc { color: #94a3b8; }

        .formula-bar {
            background: rgba(10, 66, 130, 0.06);
            padding: 0.85rem 1.15rem;
            border-radius: 0.85rem;
            text-align: center;
            font-size: 0.875rem;
            color: #0a4282;
            font-weight: 700;
            margin-bottom: 1.5rem;
            border: 1px dashed rgba(10, 66, 130, 0.28);
        }
        .dark .formula-bar {
            background: rgba(251, 185, 0, 0.1);
            color: #fbb900;
            border-color: rgba(251, 185, 0, 0.35);
        }

        .history-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.85rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.22);
        }
        .dark .history-title {
            color: #f1f5f9;
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        .score-table-wrapper {
            overflow-x: auto;
            border-radius: 0.9rem;
            border: 1px solid rgba(148, 163, 184, 0.22);
            background: #ffffff;
        }
        .dark .score-table-wrapper {
            background: rgba(15, 23, 42, 0.45);
            border-color: rgba(255, 255, 255, 0.08);
        }
        .score-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.9rem;
        }
        .score-table th {
            padding: 0.85rem 1.15rem;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            background: rgba(248, 250, 252, 0.9);
            border-bottom: 1px solid rgba(148, 163, 184, 0.2);
        }
        .dark .score-table th {
            background: rgba(15, 23, 42, 0.7);
            color: #94a3b8;
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }
        .score-table td {
            padding: 0.9rem 1.15rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.14);
            color: #1e293b;
            font-weight: 600;
        }
        .dark .score-table td {
            color: #e2e8f0;
            border-bottom-color: rgba(255, 255, 255, 0.06);
        }
        .score-table tr:last-child td {
            border-bottom: none;
        }
        .history-score-pill {
            display: inline-block;
            font-size: 1rem;
            font-weight: 800;
            color: #0a4282;
            background: rgba(10, 66, 130, 0.1);
            padding: 0.25rem 0.85rem;
            border-radius: 9999px;
        }
        .dark .history-score-pill {
            background: rgba(251, 185, 0, 0.18);
            color: #fbb900;
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

    <div class="grade-container">
        @if(count($reportCards) > 0)
            @foreach($reportCards as $report)
                <div class="report-card">
                    <div class="report-header">
                        <div class="report-icon">
                            <x-heroicon-o-academic-cap style="width: 1.85rem; height: 1.85rem;" />
                        </div>
                        <div>
                            <h2 class="report-title">{{ $report['subject_name'] }}</h2>
                            <p class="report-subtitle">Kelas: {{ $report['class_name'] }}</p>
                        </div>
                    </div>

                    <div class="report-body">
                        <div class="formula-bar">
                            <x-heroicon-o-calculator style="width: 1.15rem; height: 1.15rem; display: inline-block; vertical-align: text-bottom; margin-right: 0.35rem;" />
                            Rumus Nilai Akhir = (Kehadiran &times; 40%) + (Rata-rata CBT &amp; Evaluasi &times; 60%)
                        </div>

                        <div class="stats-grid">
                            <div class="stat-box">
                                <div class="stat-label">Skor Kehadiran (40%)</div>
                                <div class="stat-value val-attendance">{{ $report['attendance_score'] }}</div>
                                <div class="stat-desc">{{ $report['present_sessions'] }} / {{ $report['total_sessions'] }} Pertemuan Hadir</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-label">Rata-rata CBT &amp; Evaluasi (60%)</div>
                                <div class="stat-value val-cbt">{{ $report['cbt_average'] }}</div>
                                <div class="stat-desc">Dari {{ count($report['evaluations']) }} Penilaian</div>
                            </div>
                            <div class="stat-box" style="background: rgba(16, 185, 129, 0.07); border-color: rgba(16, 185, 129, 0.28);">
                                <div class="stat-label" style="color: #059669;">Nilai Akhir (Final Grade)</div>
                                <div class="stat-value val-final">{{ $report['final_grade'] }}</div>
                                <div class="stat-desc">Total Nilai Terhitung</div>
                            </div>
                        </div>

                        <div>
                            <h3 class="history-title">Rincian Nilai per Item (Ujian CBT &amp; Evaluasi)</h3>
                            @if(count($report['evaluations']) > 0)
                                <div class="score-table-wrapper">
                                    <table class="score-table">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Keterangan Evaluasi / Ujian</th>
                                                <th style="text-align: center;">Kategori</th>
                                                <th style="text-align: right;">Skor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($report['evaluations'] as $eval)
                                                <tr>
                                                    <td style="white-space: nowrap; color: #64748b;">
                                                        {{ \Carbon\Carbon::parse($eval->date)->format('d M Y') }}
                                                    </td>
                                                    <td>
                                                        {{ $eval->notes ?: 'CBT Exam / Assignment' }}
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <span style="display: inline-block; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 800; background: {{ !empty($eval->is_cbt) ? 'rgba(14, 165, 233, 0.14)' : 'rgba(100, 116, 139, 0.14)' }}; color: {{ !empty($eval->is_cbt) ? '#0284c7' : '#475569' }};" class="dark:text-slate-200">
                                                            {{ !empty($eval->is_cbt) ? 'CBT Exam' : 'Evaluasi Tutor' }}
                                                        </span>
                                                    </td>
                                                    <td style="text-align: right;">
                                                        <span class="history-score-pill">{{ $eval->score }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p style="color: #64748b; text-align: center; padding: 1.75rem 0; background: rgba(148, 163, 184, 0.08); border-radius: 0.75rem; font-style: italic; margin: 0;">
                                    Belum ada riwayat nilai ujian atau evaluasi untuk mata pelajaran ini.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <x-heroicon-o-document-magnifying-glass style="width: 3.75rem; height: 3.75rem; margin: 0 auto 1rem auto; color: #0a4282;" class="dark:text-amber-400" />
                <h3 style="font-size: 1.4rem; font-weight: 800; color: #0f172a; margin: 0 0 0.4rem 0;" class="dark:text-white">No Grades Available</h3>
                <p style="color: #64748b; font-size: 1rem; margin: 0;">You are not enrolled in any classes or have no grade reports yet.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
