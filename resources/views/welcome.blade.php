<!DOCTYPE html>
<html lang="id" class="light scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Genza English Center - Tanjung PHM Noor</title>

    <!-- Fonts -->
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
        .glass {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px) saturate(180%);
            -webkit-backdrop-filter: blur(12px) saturate(180%);
        }
        .dark .glass {
            background: rgba(15, 23, 42, 0.88);
        }
        .hero-badge-gold {
            background: linear-gradient(135deg, rgba(251, 185, 0, 0.16), rgba(251, 185, 0, 0.06));
            border: 1px solid rgba(251, 185, 0, 0.45);
        }
        .dark .hero-badge-gold {
            background: linear-gradient(135deg, rgba(251, 185, 0, 0.2), rgba(251, 185, 0, 0.08));
            border-color: rgba(251, 185, 0, 0.45);
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 antialiased transition-colors duration-300 overflow-x-hidden">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass border-b border-slate-200/80 dark:border-slate-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="{{ route('landing') }}" class="flex-shrink-0 flex items-center gap-3 focus:outline-none">
                    <img src="{{ asset('images/logo.png') }}" alt="Genza English Logo" class="h-11 sm:h-12 w-auto dark:brightness-110">
                </a>

                <div class="flex items-center gap-2.5 sm:gap-4 md:gap-5">
                    <button id="theme-toggle" type="button" aria-label="Toggle Dark Mode" class="p-2.5 rounded-full text-slate-600 dark:text-slate-300 bg-slate-100/80 dark:bg-slate-800/80 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200/70 dark:border-slate-700 transition-all">
                        <!-- Sun icon for Dark Mode -->
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5 text-genza-yellow" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                        <!-- Moon icon for Light Mode -->
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5 text-genza-blue" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    </button>

                    <a href="{{ route('login') }}" class="font-bold text-sm sm:text-base text-genza-blue dark:text-slate-200 hover:text-blue-900 dark:hover:text-genza-yellow px-3 py-2 rounded-full transition-colors">
                        Log In
                    </a>

                    <a href="{{ route('admission') }}" class="inline-flex items-center gap-1.5 bg-genza-blue hover:bg-[#083568] text-white font-bold px-4 py-2.5 sm:px-6 sm:py-2.5 text-xs sm:text-sm md:text-base rounded-full border-2 border-genza-blue hover:-translate-y-0.5 transition-all shadow-md shadow-[#0a4282]/25">
                        <span>Daftar</span>
                        <svg class="w-4 h-4 text-genza-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    @if(session('success'))
    <div class="fixed top-24 left-1/2 -translate-x-1/2 z-50 w-full max-w-2xl px-4">
        <div class="bg-emerald-50 dark:bg-emerald-950/90 border-2 border-emerald-500 text-emerald-900 dark:text-emerald-100 px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-300 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
                <h4 class="font-extrabold text-base sm:text-lg">Pendaftaran Berhasil!</h4>
                <p class="text-sm sm:text-base">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-emerald-600 dark:text-emerald-300 hover:text-emerald-800 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>
    @endif

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 sm:pt-36 sm:pb-24 lg:pt-44 lg:pb-32 overflow-hidden">
        <!-- Background decorative elements -->
        <div class="absolute inset-0 overflow-hidden -z-10 pointer-events-none">
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-[#0a4282]/10 dark:bg-[#0a4282]/25 blur-3xl"></div>
            <div class="absolute top-1/2 -left-24 w-80 h-80 rounded-full bg-[#fbb900]/15 dark:bg-[#fbb900]/10 blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center">
            <div class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full hero-badge-gold text-genza-blue dark:text-genza-yellow font-bold text-xs sm:text-sm mb-6 shadow-sm">
                <svg class="w-4 h-4 text-genza-blue dark:text-genza-yellow shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                <span>SPESIAL PROMO: DISKON HINGGA 25%!</span>
            </div>

            <h1 class="text-4xl sm:text-5xl md:text-7xl font-black text-genza-blue dark:text-white tracking-tight leading-[1.1] mb-6">
                Be More Native <br/>
                <span class="inline-block mt-1 px-4 py-1 rounded-2xl bg-genza-blue dark:bg-genza-yellow/15 text-genza-yellow border border-genza-yellow/40 shadow-sm">
                    With Genza English!
                </span>
            </h1>

            <p class="mt-5 text-lg sm:text-xl md:text-2xl text-slate-600 dark:text-slate-300 max-w-3xl mx-auto font-medium leading-relaxed">
                No more kamu malu ngomong Bahasa Inggris! Belajar natural seperti bahasa ibu, tanpa menghafal.
            </p>

            <div class="mt-10 flex flex-col sm:flex-row flex-wrap justify-center items-center gap-4">
                <a href="{{ route('admission') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 bg-genza-blue hover:bg-[#083568] text-white text-base sm:text-lg font-extrabold px-8 py-4 rounded-full border-2 border-genza-blue hover:-translate-y-0.5 transition-all shadow-xl shadow-[#0a4282]/30">
                    <span>Ambil Promo & Daftar</span>
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-genza-yellow text-genza-blue">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </span>
                </a>
                <a href="{{ route('brain-champion.register') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 bg-genza-yellow hover:bg-yellow-400 text-genza-blue text-base sm:text-lg font-black px-8 py-4 rounded-full border-2 border-genza-blue/15 hover:-translate-y-0.5 transition-all shadow-xl shadow-yellow-500/25">
                    <span>🏆 Daftar Brain Champion</span>
                </a>
                <a href="#program" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white dark:bg-slate-800 text-genza-blue dark:text-white text-base sm:text-lg font-bold px-8 py-4 rounded-full hover:bg-slate-50 dark:hover:bg-slate-700 border-2 border-genza-blue/20 dark:border-slate-700 hover:border-genza-yellow dark:hover:border-genza-yellow hover:-translate-y-0.5 transition-all shadow-sm">
                    <span>Lihat Program</span>
                    <svg class="w-5 h-5 text-genza-blue dark:text-genza-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </a>
            </div>

            <div class="mt-8 flex flex-col items-center gap-2.5">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-50 dark:bg-amber-950/50 border border-amber-300 dark:border-amber-700/80 text-xs sm:text-sm text-genza-blue dark:text-genza-yellow font-extrabold">
                    <span>🏆 G-ENGLISH BRAIN CHAMPION: Kompetisi Offline di Gedung • Sistem Gugur (Whiteboard atau Kertas — Soal Dibacakan Langsung)</span>
                </div>
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800/70 text-xs sm:text-sm text-rose-600 dark:text-rose-300 font-bold">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span>KUOTA SANGAT TERBATAS (Hanya 5–15 Siswa Reguler &amp; 35 Peserta Brain Champion per jenjang)</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Divider -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="h-1 w-full rounded-full bg-gradient-to-r from-transparent via-genza-blue/25 dark:via-genza-yellow/30 to-transparent"></div>
    </div>

    <!-- This is What You Get Section -->
    <section class="bg-white dark:bg-slate-800/90 py-20 sm:py-24 transition-colors duration-300" id="benefits">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14 sm:mb-16">
                <span class="inline-block text-xs font-extrabold uppercase tracking-widest text-genza-blue dark:text-genza-yellow bg-blue-50 dark:bg-slate-900 px-3.5 py-1.5 rounded-full border border-blue-200/80 dark:border-slate-700 mb-3">
                    Keunggulan Program
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-genza-blue dark:text-white tracking-tight">
                    This is <span class="underline decoration-genza-yellow decoration-4 underline-offset-8">WHAT you GET!</span>
                </h2>
                <p class="mt-4 text-base sm:text-lg text-slate-600 dark:text-slate-400">
                    Let's make English feel natural with Genza English.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                <!-- Feature 1 -->
                <div class="flex flex-col bg-slate-50 dark:bg-slate-900 p-7 sm:p-8 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 hover:border-genza-yellow dark:hover:border-genza-yellow hover:shadow-xl hover:-translate-y-1.5 transition-all group">
                    <div class="w-14 h-14 bg-genza-blue text-genza-yellow rounded-2xl flex items-center justify-center mb-6 shadow-md shadow-[#0a4282]/20 group-hover:scale-105 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    </div>
                    <h3 class="text-xl font-extrabold text-genza-blue dark:text-white mb-2.5">Materi Sesuai Level</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm sm:text-base leading-relaxed">Disesuaikan dengan kebutuhan dan kemampuanmu (SD, SMP, SMA).</p>
                </div>

                <!-- Feature 2 -->
                <div class="flex flex-col bg-slate-50 dark:bg-slate-900 p-7 sm:p-8 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 hover:border-genza-yellow dark:hover:border-genza-yellow hover:shadow-xl hover:-translate-y-1.5 transition-all group">
                    <div class="w-14 h-14 bg-genza-blue text-genza-yellow rounded-2xl flex items-center justify-center mb-6 shadow-md shadow-[#0a4282]/20 group-hover:scale-105 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-extrabold text-genza-blue dark:text-white mb-2.5">Latihan Seru & Interaktif</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm sm:text-base leading-relaxed">Banyak latihan praktis (Worksheet Seru) yang bikin belajar menyenangkan.</p>
                </div>

                <!-- Feature 3 -->
                <div class="flex flex-col bg-slate-50 dark:bg-slate-900 p-7 sm:p-8 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 hover:border-genza-yellow dark:hover:border-genza-yellow hover:shadow-xl hover:-translate-y-1.5 transition-all group">
                    <div class="w-14 h-14 bg-genza-blue text-genza-yellow rounded-2xl flex items-center justify-center mb-6 shadow-md shadow-[#0a4282]/20 group-hover:scale-105 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    </div>
                    <h3 class="text-xl font-extrabold text-genza-blue dark:text-white mb-2.5">Acquisition Learning</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm sm:text-base leading-relaxed">Metode belajar natural seperti bahasa ibu, membentuk karakter & wawasan.</p>
                </div>

                <!-- Feature 4 -->
                <div class="flex flex-col bg-slate-50 dark:bg-slate-900 p-7 sm:p-8 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 hover:border-genza-yellow dark:hover:border-genza-yellow hover:shadow-xl hover:-translate-y-1.5 transition-all group">
                    <div class="w-14 h-14 bg-genza-blue text-genza-yellow rounded-2xl flex items-center justify-center mb-6 shadow-md shadow-[#0a4282]/20 group-hover:scale-105 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"></path><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                    </div>
                    <h3 class="text-xl font-extrabold text-genza-blue dark:text-white mb-2.5">Tutor Profesional</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm sm:text-base leading-relaxed">Dibimbing oleh tutor yang ahli dan berpengalaman.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Fasilitas & Program -->
    <section class="py-20 sm:py-24 bg-genza-blue dark:bg-slate-950 border-t-4 border-genza-yellow transition-colors duration-300" id="program">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-16">
                <div class="lg:w-1/2 w-full">
                    <span class="inline-block text-xs font-extrabold uppercase tracking-widest bg-genza-yellow text-genza-blue px-3.5 py-1.5 rounded-full mb-4">
                        Kurikulum Unggulan
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-black text-white mb-5 leading-tight">
                        Membangun Fondasi <span class="text-genza-yellow">Survival English</span>
                    </h2>
                    <p class="text-blue-100 text-base sm:text-lg mb-8 leading-relaxed">
                        Kami merancang program khusus agar anak berani dan terbiasa menggunakan Bahasa Inggris sehari-hari. Jadwal kelas akan dibuat setelah kuota terpenuhi.
                    </p>

                    <ul class="space-y-4">
                        <li class="flex items-center gap-4 bg-white/10 dark:bg-slate-900/90 p-4 rounded-2xl border border-white/15 dark:border-slate-800">
                            <span class="bg-genza-yellow text-genza-blue font-black w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow">1</span>
                            <div class="text-white font-semibold text-base sm:text-lg">Jenjang: SD, SMP, SMA & Umum</div>
                        </li>
                        <li class="flex items-center gap-4 bg-white/10 dark:bg-slate-900/90 p-4 rounded-2xl border border-white/15 dark:border-slate-800">
                            <span class="bg-genza-yellow text-genza-blue font-black w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow">2</span>
                            <div class="text-white font-semibold text-base sm:text-lg">Waktu: 3x Pertemuan / Minggu (100 Menit)</div>
                        </li>
                        <li class="flex items-center gap-4 bg-white/10 dark:bg-slate-900/90 p-4 rounded-2xl border border-white/15 dark:border-slate-800">
                            <span class="bg-genza-yellow text-genza-blue font-black w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow">3</span>
                            <div class="text-white font-semibold text-base sm:text-lg">Total Belajar: 30x Pertemuan</div>
                        </li>
                    </ul>
                </div>

                <div class="lg:w-1/2 w-full">
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-2xl border-2 border-genza-yellow/50 relative">
                        <div class="inline-flex items-center gap-1.5 bg-genza-yellow text-genza-blue font-black text-xs uppercase tracking-wider px-3.5 py-1.5 rounded-full mb-4 shadow-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <span>Fasilitas Lengkap</span>
                        </div>

                        <h3 class="text-2xl font-extrabold text-genza-blue dark:text-white mb-6">Special Facilities</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex items-center gap-3.5 p-4 bg-slate-50 dark:bg-slate-800/90 rounded-2xl border border-slate-200/80 dark:border-slate-700">
                                <div class="w-10 h-10 rounded-xl bg-genza-blue/10 dark:bg-genza-yellow/15 text-genza-blue dark:text-genza-yellow flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                </div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-100 text-sm sm:text-base">Experiential Learning</h4>
                            </div>

                            <div class="flex items-center gap-3.5 p-4 bg-slate-50 dark:bg-slate-800/90 rounded-2xl border border-slate-200/80 dark:border-slate-700">
                                <div class="w-10 h-10 rounded-xl bg-genza-blue/10 dark:bg-genza-yellow/15 text-genza-blue dark:text-genza-yellow flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-100 text-sm sm:text-base">G-Book</h4>
                            </div>

                            <div class="flex items-center gap-3.5 p-4 bg-slate-50 dark:bg-slate-800/90 rounded-2xl border border-slate-200/80 dark:border-slate-700">
                                <div class="w-10 h-10 rounded-xl bg-genza-blue/10 dark:bg-genza-yellow/15 text-genza-blue dark:text-genza-yellow flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-100 text-sm sm:text-base">Seminar Belajar</h4>
                            </div>

                            <div class="flex items-center gap-3.5 p-4 bg-slate-50 dark:bg-slate-800/90 rounded-2xl border border-slate-200/80 dark:border-slate-700">
                                <div class="w-10 h-10 rounded-xl bg-genza-blue/10 dark:bg-genza-yellow/15 text-genza-blue dark:text-genza-yellow flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                </div>
                                <h4 class="font-bold text-slate-800 dark:text-slate-100 text-sm sm:text-base">G-Clinic</h4>
                            </div>
                        </div>

                        <div class="mt-8">
                            <a href="{{ route('admission') }}" class="flex items-center justify-center gap-2 w-full text-center bg-genza-blue hover:bg-[#083568] text-white font-extrabold py-4 px-6 rounded-full border-2 border-genza-blue shadow-lg shadow-[#0a4282]/25 hover:-translate-y-0.5 transition-all">
                                <span>Ambil Kuota Sekarang</span>
                                <svg class="w-5 h-5 text-genza-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA & Footer -->
    <footer class="bg-slate-900 text-slate-300 py-16 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 lg:gap-12 items-center mb-12">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-white mb-3">Hubungi Kami / Tanya-tanya</h2>
                    <p class="text-slate-400 mb-6 text-sm sm:text-base leading-relaxed">
                        Pendaftaran akan ditutup sewaktu-waktu jika kuota sudah terpenuhi. Hubungi tim admin kami segera!
                    </p>
                    <div class="flex flex-wrap items-center gap-4">
                        <a href="https://wa.me/6281151100048" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2.5 bg-genza-yellow hover:bg-yellow-400 text-genza-blue px-6 py-3.5 rounded-full font-extrabold shadow-lg shadow-yellow-500/20 hover:-translate-y-0.5 transition-all">
                            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.888-.788-1.487-1.761-1.663-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            <span>WhatsApp: 0811-5110-0048</span>
                        </a>
                    </div>
                </div>

                <div class="bg-slate-800/90 rounded-2xl p-6 border border-slate-700/80 shadow-lg">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-genza-blue text-genza-yellow rounded-2xl flex items-center justify-center shrink-0 shadow">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-white font-bold text-lg mb-1.5">Genza Education Tanjung PHM Noor</h4>
                            <p class="text-slate-400 text-sm sm:text-base leading-relaxed">
                                Jl. Ir. PHM Noor. RT.4 No. 119, Pembataan<br/>
                                Kec. Murung Pudak, Kab. Tabalong 71571
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} Genza English Center. All rights reserved.</p>
                <div class="text-slate-500 text-sm">
                    Supported by <span class="font-bold text-genza-yellow">genza EDUCATION</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Theme Toggle Script -->
    <script>
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
            document.documentElement.classList.add('dark');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
            document.documentElement.classList.remove('dark');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');

        themeToggleBtn.addEventListener('click', function() {
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
        });
    </script>
</body>
</html>
