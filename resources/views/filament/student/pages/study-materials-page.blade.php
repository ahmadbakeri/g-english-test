<x-filament-panels::page>
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px) saturate(180%);
            -webkit-backdrop-filter: blur(12px) saturate(180%);
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 1.25rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            padding: 1.75rem;
            margin-bottom: 1.25rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .dark .glass-panel {
            background: rgba(30, 41, 59, 0.78);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        }
        .glass-panel:hover {
            transform: translateY(-3px);
            border-color: rgba(10, 66, 130, 0.25);
            box-shadow: 0 20px 25px -5px rgba(10, 66, 130, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.03);
        }
        .dark .glass-panel:hover {
            border-color: rgba(251, 185, 0, 0.35);
        }

        .subject-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            padding-bottom: 1.25rem;
            margin-bottom: 1.25rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.2);
        }
        .subject-title {
            font-size: 1.375rem;
            font-weight: 800;
            color: #0a4282;
            margin: 0 0 0.5rem 0;
            line-height: 1.25;
        }
        .dark .subject-title { color: #f8fafc; }

        .subject-level-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            background: rgba(10, 66, 130, 0.1);
            color: #0a4282;
            border: 1px solid rgba(10, 66, 130, 0.2);
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.025em;
        }
        .dark .subject-level-badge {
            background: rgba(251, 185, 0, 0.15);
            color: #fbb900;
            border-color: rgba(251, 185, 0, 0.35);
        }

        .subject-icon-box {
            width: 3.25rem;
            height: 3.25rem;
            background: linear-gradient(135deg, #0a4282, #1a368b);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fbb900;
            flex-shrink: 0;
            box-shadow: 0 6px 14px -3px rgba(10, 66, 130, 0.35);
        }

        .material-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.15rem;
            background: rgba(248, 250, 252, 0.85);
            border-radius: 1rem;
            margin-top: 0.75rem;
            text-decoration: none;
            border: 1px solid rgba(148, 163, 184, 0.2);
            transition: all 0.2s ease;
        }
        .dark .material-card {
            background: rgba(15, 23, 42, 0.55);
            border-color: rgba(255, 255, 255, 0.07);
        }
        .material-card:hover {
            background: #eff6ff;
            border-color: #0a4282;
            transform: translateX(3px);
        }
        .dark .material-card:hover {
            background: rgba(10, 66, 130, 0.28);
            border-color: #fbb900;
        }
        .material-icon {
            width: 2.85rem;
            height: 2.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            background: rgba(10, 66, 130, 0.1);
            color: #0a4282;
            flex-shrink: 0;
        }
        .dark .material-icon {
            background: rgba(251, 185, 0, 0.15);
            color: #fbb900;
        }
        .material-info {
            flex: 1;
            min-width: 0;
        }
        .material-info h4 {
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            font-size: 1rem;
            line-height: 1.35;
        }
        .dark .material-info h4 { color: #f1f5f9; }
        .material-info p {
            font-size: 0.825rem;
            color: #64748b;
            margin: 0.2rem 0 0 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .dark .material-info p { color: #94a3b8; }

        .material-open-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem 0.9rem;
            border-radius: 9999px;
            background: #0a4282;
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }
        .dark .material-open-pill {
            background: rgba(251, 185, 0, 0.18);
            color: #fbb900;
            border: 1px solid rgba(251, 185, 0, 0.4);
        }
        .material-card:hover .material-open-pill {
            background: #fbb900;
            color: #0a4282;
        }
    </style>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($this->subjects as $subject)
            <div class="glass-panel">
                <div class="subject-header">
                    <div>
                        <h2 class="subject-title">{{ $subject->name }}</h2>
                        <span class="subject-level-badge">{{ $subject->level ?: 'General' }}</span>
                    </div>
                    <div class="subject-icon-box">
                        <x-heroicon-o-academic-cap style="width: 1.75rem; height: 1.75rem;" />
                    </div>
                </div>

                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                        <h3 style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; font-weight: 800; color: #64748b; margin: 0;">
                            Daftar Materi Belajar
                        </h3>
                        <span style="font-size: 0.75rem; font-weight: 700; color: #0a4282;" class="dark:text-amber-400">
                            {{ count($subject->studyMaterials) }} Materi
                        </span>
                    </div>

                    @foreach($subject->studyMaterials as $material)
                        <a href="{{ route('filament.student.pages.view-study-material', ['record' => $material->id]) }}" class="material-card">
                            <div style="display: flex; align-items: center; gap: 0.9rem; min-width: 0; flex: 1;">
                                <div class="material-icon">
                                    @if($material->type === 'video')
                                        <x-heroicon-s-play-circle style="width: 1.5rem; height: 1.5rem;" />
                                    @elseif($material->type === 'pdf')
                                        <x-heroicon-s-document-arrow-down style="width: 1.5rem; height: 1.5rem;" />
                                    @elseif($material->type === 'mindmap')
                                        <x-heroicon-s-photo style="width: 1.5rem; height: 1.5rem;" />
                                    @else
                                        <x-heroicon-s-book-open style="width: 1.5rem; height: 1.5rem;" />
                                    @endif
                                </div>
                                <div class="material-info">
                                    <h4>{{ $material->title }}</h4>
                                    @if($material->description)
                                        <p>{{ $material->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <span class="material-open-pill">
                                <span>Buka</span>
                                <x-heroicon-m-arrow-right style="width: 0.95rem; height: 0.95rem;" />
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="col-span-1 md:col-span-2">
                <div class="glass-panel" style="text-align: center; padding: 4rem 2rem;">
                    <div style="width: 4.5rem; height: 4.5rem; background: rgba(10, 66, 130, 0.1); border-radius: 1.25rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem auto; color: #0a4282;" class="dark:text-amber-400">
                        <x-heroicon-o-book-open style="width: 2.5rem; height: 2.5rem;" />
                    </div>
                    <h3 style="font-size: 1.35rem; font-weight: 800; color: #0f172a; margin-bottom: 0.4rem;" class="dark:text-white">Belum Ada Materi Tersedia</h3>
                    <p style="color: #64748b; margin: 0; font-size: 0.95rem;">Saat ini belum ada materi pendukung online yang dipublikasikan untuk kelas Anda.</p>
                </div>
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
