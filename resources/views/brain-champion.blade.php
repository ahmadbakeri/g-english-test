<!DOCTYPE html>
<html lang="id" class="light scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'G-English Brain Champion — One Mind, One Champion' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        genza: {
                            blue: '#0a4282',
                            yellow: '#fbb900',
                            dark: '#0f172a',
                            light: '#f8fafc',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px) saturate(180%);
            -webkit-backdrop-filter: blur(12px) saturate(180%);
            border: 1px solid rgba(15, 23, 42, 0.07);
            box-shadow: 0 10px 25px -5px rgba(10, 66, 130, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
        }
        .dark .glass-card {
            background: rgba(15, 23, 42, 0.82);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.55);
        }
        .bc-input:focus {
            outline: none;
            border-color: #0a4282;
            box-shadow: 0 0 0 4px rgba(10, 66, 130, 0.15);
        }
        .dark .bc-input:focus {
            border-color: #fbb900;
            box-shadow: 0 0 0 4px rgba(251, 185, 0, 0.18);
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #0f172a !important;
            }
            .print-card {
                box-shadow: none !important;
                border: 2px solid #0a4282 !important;
                background: #ffffff !important;
            }
        }
    </style>
    @livewireStyles
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen flex flex-col antialiased transition-colors duration-300">
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function toggleBrainChampionTheme() {
            var html = document.documentElement;
            var isDark = html.classList.contains('dark');
            if (isDark) {
                html.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
        }
    </script>

    @if(isset($slot))
        {{ $slot }}
    @else
        @livewire('brain-champion-registration')
    @endif

    @livewireScripts
</body>
</html>
