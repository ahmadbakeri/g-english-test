<x-filament-panels::page>
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px) saturate(180%);
            -webkit-backdrop-filter: blur(12px) saturate(180%);
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 1.25rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            padding: 2rem;
            margin-bottom: 1.75rem;
        }
        .dark .glass-panel {
            background: rgba(30, 41, 59, 0.78);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        }

        .material-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .btn-back-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.1rem;
            border-radius: 9999px;
            background: #f1f5f9;
            color: #0a4282;
            font-weight: 700;
            font-size: 0.85rem;
            text-decoration: none;
            border: 1px solid rgba(10, 66, 130, 0.15);
            transition: all 0.2s ease;
        }
        .dark .btn-back-pill {
            background: rgba(15, 23, 42, 0.65);
            color: #e2e8f0;
            border-color: rgba(255, 255, 255, 0.12);
        }
        .btn-back-pill:hover {
            background: #0a4282;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .material-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0a4282;
            margin: 0 0 0.75rem 0;
            line-height: 1.25;
        }
        .dark .material-title { color: #f8fafc; }

        .material-meta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            align-items: center;
        }
        .subject-pill {
            padding: 0.3rem 0.85rem;
            background: rgba(10, 66, 130, 0.1);
            color: #0a4282;
            border: 1px solid rgba(10, 66, 130, 0.2);
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 700;
        }
        .dark .subject-pill {
            background: rgba(251, 185, 0, 0.15);
            color: #fbb900;
            border-color: rgba(251, 185, 0, 0.35);
        }

        .material-desc {
            font-size: 1.025rem;
            color: #334155;
            line-height: 1.75;
            padding: 1.25rem 1.5rem;
            background: rgba(248, 250, 252, 0.8);
            border-left: 4px solid #0a4282;
            border-radius: 0.85rem;
            margin-bottom: 2rem;
        }
        .dark .material-desc {
            color: #cbd5e1;
            background: rgba(15, 23, 42, 0.55);
            border-left-color: #fbb900;
        }

        .section-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .dark .section-title { color: #f1f5f9; }
        .section-title-icon {
            width: 2.15rem;
            height: 2.15rem;
            border-radius: 0.65rem;
            background: rgba(10, 66, 130, 0.1);
            color: #0a4282;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .dark .section-title-icon {
            background: rgba(251, 185, 0, 0.15);
            color: #fbb900;
        }

        .video-wrapper {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.12);
            margin-bottom: 2rem;
            border: 1px solid rgba(15, 23, 42, 0.08);
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
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
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

        .attachment-box {
            background: rgba(248, 250, 252, 0.85);
            border: 1px solid rgba(148, 163, 184, 0.25);
            padding: 2rem;
            border-radius: 1.25rem;
            text-align: center;
            margin-bottom: 0.5rem;
        }
        .dark .attachment-box {
            background: rgba(15, 23, 42, 0.55);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .btn-download {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            padding: 0.85rem 1.85rem;
            background: linear-gradient(135deg, #0a4282, #1a368b);
            color: #ffffff;
            font-weight: 700;
            font-size: 1rem;
            border-radius: 9999px;
            text-decoration: none;
            box-shadow: 0 6px 16px -3px rgba(10, 66, 130, 0.4);
            transition: all 0.2s ease;
        }
        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -4px rgba(10, 66, 130, 0.5);
        }
        .btn-secondary-gold {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.65rem 1.35rem;
            background: #fbb900;
            color: #0a4282;
            font-weight: 800;
            font-size: 0.875rem;
            border-radius: 9999px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-secondary-gold:hover {
            transform: translateY(-2px);
            background: #f59e0b;
            color: #ffffff;
        }
    </style>

    <div class="glass-panel">
        <div class="material-header-top">
            <a href="{{ route('filament.student.pages.study-materials-page') }}" class="btn-back-pill">
                <x-heroicon-m-arrow-left style="width: 1rem; height: 1rem;" />
                <span>Kembali ke Daftar Materi</span>
            </a>
            <span style="color: #64748b; font-size: 0.825rem; font-weight: 600;">
                Diupload: {{ $material->created_at->format('d M Y') }}
            </span>
        </div>

        <h1 class="material-title">{{ $material->title }}</h1>

        <div class="material-meta-row">
            <span class="subject-pill">
                {{ $material->subject->name }}
            </span>
            @if($material->type)
                <span style="padding: 0.25rem 0.75rem; background: rgba(251, 185, 0, 0.18); color: #92400e; border-radius: 9999px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase;" class="dark:text-amber-300">
                    {{ strtoupper($material->type) }}
                </span>
            @endif
        </div>

        @if($material->description)
            <div class="material-desc">
                {!! nl2br(e($material->description)) !!}
            </div>
        @endif

        @if($material->youtube_url)
            <h2 class="section-title">
                <span class="section-title-icon">
                    <x-heroicon-o-video-camera style="width: 1.25rem; height: 1.25rem;" />
                </span>
                <span>Video Pembelajaran</span>
            </h2>
            @php
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
                <div style="padding: 1rem; background: #fee2e2; color: #b91c1c; border-radius: 0.75rem; margin-bottom: 2rem; font-weight: 600;">
                    Format URL YouTube tidak valid.
                </div>
            @endif
        @endif

        @if($material->mindmap_image)
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem;">
                <h2 class="section-title" style="margin-bottom: 0;">
                    <span class="section-title-icon">
                        <x-heroicon-o-map style="width: 1.25rem; height: 1.25rem;" />
                    </span>
                    <span>Mindmap / Infografis</span>
                </h2>
                <a href="{{ Storage::url($material->mindmap_image) }}" target="_blank" class="btn-secondary-gold">
                    <x-heroicon-m-arrows-pointing-out style="width: 1rem; height: 1rem;" />
                    <span>Buka Ukuran Penuh</span>
                </a>
            </div>
            <div class="mindmap-container">
                <a href="{{ Storage::url($material->mindmap_image) }}" target="_blank" title="Klik untuk memperbesar">
                    <img src="{{ Storage::url($material->mindmap_image) }}" alt="Mindmap {{ $material->title }}" />
                </a>
            </div>
        @endif

        @if($material->attachment_file)
            <h2 class="section-title" style="margin-top: 2.25rem;">
                <span class="section-title-icon">
                    <x-heroicon-o-document-arrow-down style="width: 1.25rem; height: 1.25rem;" />
                </span>
                <span>Materi Lampiran</span>
            </h2>
            <div class="attachment-box">
                <div style="width: 3.75rem; height: 3.75rem; border-radius: 1rem; background: rgba(10, 66, 130, 0.1); color: #0a4282; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;" class="dark:text-amber-400">
                    <x-heroicon-o-document-text style="width: 2.25rem; height: 2.25rem;" />
                </div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 1.25rem;" class="dark:text-slate-200">
                    Unduh dokumen materi untuk dipelajari secara mandiri.
                </h3>
                <a href="{{ Storage::url($material->attachment_file) }}" target="_blank" class="btn-download" download>
                    <x-heroicon-s-arrow-down-tray style="width: 1.25rem; height: 1.25rem;" />
                    <span>Download Materi</span>
                </a>
            </div>
        @endif
    </div>
</x-filament-panels::page>
