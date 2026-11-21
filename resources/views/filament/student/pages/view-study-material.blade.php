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
            margin-bottom: 2rem;
        }
        .dark .glass-panel {
            background: rgba(30, 41, 59, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .material-title {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        .dark .material-title { color: #f8fafc; }
        
        .material-desc {
            font-size: 1.125rem;
            color: #475569;
            line-height: 1.7;
            margin-bottom: 2rem;
        }
        .dark .material-desc { color: #cbd5e1; }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        .dark .section-title { color: #e2e8f0; }
        .section-title svg {
            width: 1.5rem;
            height: 1.5rem;
            margin-right: 0.5rem;
            color: #3b82f6;
        }

        .video-wrapper {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 */
            height: 0;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            margin-bottom: 2rem;
        }
        .video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .mindmap-container {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .dark .mindmap-container {
            border-color: #334155;
            background: #0f172a;
        }
        .mindmap-container img {
            width: 100%;
            height: auto;
            display: block;
        }

        .btn-download {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            font-weight: 600;
            font-size: 1.125rem;
            border-radius: 9999px;
            text-decoration: none;
            box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.39);
            transition: all 0.2s ease;
        }
        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }
        .btn-download svg {
            width: 1.5rem;
            height: 1.5rem;
            margin-right: 0.75rem;
        }
    </style>

    <div class="glass-panel">
        <h1 class="material-title">{{ $material->title }}</h1>
        <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; align-items: center;">
            <span style="padding: 0.25rem 0.75rem; background: #e0e7ff; color: #4338ca; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
                {{ $material->subject->name }}
            </span>
            <span style="color: #94a3b8; font-size: 0.875rem;">
                Diupload pada: {{ $material->created_at->format('d M Y') }}
            </span>
        </div>
        
        @if($material->description)
        <div class="material-desc">
            {!! nl2br(e($material->description)) !!}
        </div>
        @endif

        @if($material->youtube_url)
        <h2 class="section-title">
            <x-heroicon-o-video-camera />
            Video Pembelajaran
        </h2>
        @php
            // Extract Video ID to use embed URL
            $url = $material->youtube_url;
            $videoId = '';
            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $match)) {
                $videoId = $match[1];
            }
        @endphp
        @if($videoId)
        <div class="video-wrapper">
            <iframe src="https://www.youtube.com/embed/{{ $videoId }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
        @else
        <div style="padding: 1rem; background: #fee2e2; color: #b91c1c; border-radius: 0.5rem; margin-bottom: 2rem;">
            Format URL YouTube tidak valid.
        </div>
        @endif
        @endif

        @if($material->mindmap_image)
        <h2 class="section-title">
            <x-heroicon-o-map />
            Mindmap / Infografis
        </h2>
        <div class="mindmap-container">
            <a href="{{ Storage::url($material->mindmap_image) }}" target="_blank" title="Klik untuk memperbesar">
                <img src="{{ Storage::url($material->mindmap_image) }}" alt="Mindmap {{ $material->title }}" />
            </a>
        </div>
        @endif

        @if($material->attachment_file)
        <h2 class="section-title" style="margin-top: 3rem;">
            <x-heroicon-o-document-arrow-down />
            Materi Lampiran
        </h2>
        <div style="background: rgba(241, 245, 249, 0.5); border: 1px solid #e2e8f0; padding: 2rem; border-radius: 1rem; text-align: center; margin-bottom: 1rem;" class="dark:bg-slate-800 dark:border-slate-700">
            <x-heroicon-o-document-text style="width: 4rem; height: 4rem; color: #94a3b8; margin: 0 auto 1rem auto;" />
            <h3 style="font-size: 1.125rem; font-weight: 600; color: #334155; margin-bottom: 1.5rem;" class="dark:text-slate-200">Unduh dokumen materi untuk dipelajari secara offline.</h3>
            <a href="{{ Storage::url($material->attachment_file) }}" target="_blank" class="btn-download" download>
                <x-heroicon-s-arrow-down-tray />
                Download Materi
            </a>
        </div>
        @endif
    </div>
</x-filament-panels::page>
