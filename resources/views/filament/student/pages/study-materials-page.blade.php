<x-filament-panels::page>
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 1.25rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            padding: 2rem;
            margin-bottom: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .dark .glass-panel {
            background: rgba(30, 41, 59, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .glass-panel:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .subject-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        .dark .subject-title { color: #f8fafc; }
        
        .material-card {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: rgba(241, 245, 249, 0.5);
            border-radius: 0.75rem;
            margin-top: 0.75rem;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }
        .dark .material-card {
            background: rgba(15, 23, 42, 0.5);
        }
        .material-card:hover {
            background: #eff6ff;
            border-color: #bfdbfe;
        }
        .dark .material-card:hover {
            background: rgba(30, 58, 138, 0.3);
            border-color: #1e3a8a;
        }
        .material-icon {
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            background: #dbeafe;
            color: #2563eb;
            margin-right: 1rem;
        }
        .dark .material-icon { background: #1e3a8a; color: #60a5fa; }
        .material-info h4 {
            font-weight: 700;
            color: #334155;
            margin: 0;
            font-size: 1.125rem;
        }
        .dark .material-info h4 { color: #e2e8f0; }
        .material-info p {
            font-size: 0.875rem;
            color: #64748b;
            margin: 0;
            margin-top: 0.25rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .dark .material-info p { color: #94a3b8; }
    </style>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($this->subjects as $subject)
            <div class="glass-panel">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                    <div>
                        <h2 class="subject-title">{{ $subject->name }}</h2>
                        <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #e0e7ff; color: #4338ca; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">{{ $subject->level }}</span>
                    </div>
                    <div style="width: 3.5rem; height: 3.5rem; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #64748b;">
                        <x-heroicon-o-academic-cap style="width: 2rem; height: 2rem;" />
                    </div>
                </div>

                <div>
                    <h3 style="font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; color: #94a3b8; margin-bottom: 0.5rem;">Available Materials</h3>
                    
                    @foreach($subject->studyMaterials as $material)
                        <a href="{{ route('filament.student.pages.view-study-material', ['record' => $material->id]) }}" class="material-card">
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
                                <p>{{ $material->description }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="col-span-1 md:col-span-2">
                <div class="glass-panel" style="text-align: center; padding: 4rem 2rem;">
                    <div style="width: 5rem; height: 5rem; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto; color: #94a3b8;">
                        <x-heroicon-o-book-open style="width: 3rem; height: 3rem;" />
                    </div>
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: #334155; margin-bottom: 0.5rem;">Belum ada materi</h3>
                    <p style="color: #64748b;">Saat ini tidak ada materi pendukung online yang tersedia untuk mata pelajaran Anda.</p>
                </div>
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
