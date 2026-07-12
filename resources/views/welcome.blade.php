<!DOCTYPE html>
<html lang="id" class="light">
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
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
        .dark .glass {
            background: rgba(15, 23, 42, 0.9);
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 transition-colors duration-300 overflow-x-hidden">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass border-b border-slate-200 dark:border-slate-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Genza English Logo" class="h-12 w-auto dark:brightness-110">
                </div>
                
                <div class="flex items-center gap-2 sm:gap-4 md:space-x-6">
                    <button id="theme-toggle" class="p-2 rounded-full hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors">
                        <!-- Sun icon for Dark Mode -->
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5 sm:w-6 sm:h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                        <!-- Moon icon for Light Mode -->
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5 sm:w-6 sm:h-6 text-slate-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    </button>
                    <a href="{{ route('login') }}" class="font-semibold text-sm sm:text-base text-slate-600 hover:text-genza-blue dark:text-slate-300 dark:hover:text-blue-400">Log In</a>
                    <a href="{{ route('admission') }}" class="bg-genza-yellow text-genza-blue font-bold px-4 py-2 sm:px-6 sm:py-2.5 text-xs sm:text-base rounded-full hover:bg-yellow-400 hover:-translate-y-0.5 transition-all shadow-lg shadow-yellow-500/30">Daftar</a>
                </div>
            </div>
        </div>
    </nav>

    @if(session('success'))
    <div class="fixed top-24 left-1/2 -translate-x-1/2 z-50 w-full max-w-2xl px-4 animate-bounce">
        <div class="bg-green-100 border-2 border-green-500 text-green-800 px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4">
            <span class="text-3xl">🎉</span>
            <div>
                <h4 class="font-bold text-lg">Hore!</h4>
                <p>{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-green-600 hover:text-green-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>
    @endif

    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <!-- Background decorative elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10">
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-blue-100 dark:bg-blue-900/30 blur-3xl opacity-50"></div>
            <div class="absolute top-1/2 -left-24 w-72 h-72 rounded-full bg-yellow-100 dark:bg-yellow-900/20 blur-3xl opacity-50"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-blue-100 text-genza-blue dark:bg-blue-900/50 dark:text-blue-300 font-semibold text-sm mb-6 border border-blue-200 dark:border-blue-800">
                🚀 SPESIAL PROMO: DISKON HINGGA 25%!
            </span>
            <h1 class="text-5xl md:text-7xl font-black text-genza-blue dark:text-white tracking-tight mb-6">
                Be More Native <br/>
                <span class="text-genza-yellow">With Genza English!</span>
            </h1>
            <p class="mt-4 text-xl md:text-2xl text-slate-600 dark:text-slate-300 max-w-3xl mx-auto font-medium">
                No more kamu malu ngomong Bahasa Inggris! Belajar natural seperti bahasa ibu, tanpa menghafal.
            </p>
            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('admission') }}" class="bg-genza-yellow text-genza-blue text-lg font-bold px-8 py-4 rounded-full hover:bg-yellow-400 hover:scale-105 transition-all shadow-xl shadow-yellow-500/40">
                    Ambil Promo & Daftar
                </a>
                <a href="#program" class="bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-lg font-bold px-8 py-4 rounded-full hover:bg-slate-50 dark:hover:bg-slate-700 border-2 border-slate-200 dark:border-slate-700 hover:scale-105 transition-all">
                    Lihat Program
                </a>
            </div>
            
            <p class="mt-6 text-sm text-red-500 dark:text-red-400 font-bold animate-pulse">
                ⚠️ KUOTA SANGAT TERBATAS (Hanya 5–15 Siswa per jenjang)
            </p>
        </div>
    </div>

    <!-- This is What You Get Section -->
    <div class="bg-white dark:bg-slate-800 py-24 transition-colors duration-300" id="benefits">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-4xl font-black text-genza-blue dark:text-white">This is <span class="text-genza-yellow">WHAT you GET!</span></h2>
                <p class="mt-4 text-lg text-slate-600 dark:text-slate-400">Let's make English feel natural with Genza English.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="bg-slate-50 dark:bg-slate-900 p-8 rounded-3xl border border-slate-100 dark:border-slate-700 hover:shadow-xl hover:-translate-y-2 transition-all group">
                    <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <span class="text-2xl">💬</span>
                    </div>
                    <h3 class="text-xl font-bold text-genza-blue dark:text-white mb-3">Materi Sesuai Level</h3>
                    <p class="text-slate-600 dark:text-slate-400">Disesuaikan dengan kebutuhan dan kemampuanmu (SD, SMP, SMA).</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-slate-50 dark:bg-slate-900 p-8 rounded-3xl border border-slate-100 dark:border-slate-700 hover:shadow-xl hover:-translate-y-2 transition-all group">
                    <div class="w-14 h-14 bg-yellow-100 dark:bg-yellow-900/30 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <span class="text-2xl">🎮</span>
                    </div>
                    <h3 class="text-xl font-bold text-genza-blue dark:text-white mb-3">Latihan Seru & Interaktif</h3>
                    <p class="text-slate-600 dark:text-slate-400">Banyak latihan praktis (Worksheet Seru) yang bikin belajar menyenangkan.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-slate-50 dark:bg-slate-900 p-8 rounded-3xl border border-slate-100 dark:border-slate-700 hover:shadow-xl hover:-translate-y-2 transition-all group">
                    <div class="w-14 h-14 bg-green-100 dark:bg-green-900/30 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <span class="text-2xl">💡</span>
                    </div>
                    <h3 class="text-xl font-bold text-genza-blue dark:text-white mb-3">Acquisition Learning</h3>
                    <p class="text-slate-600 dark:text-slate-400">Metode belajar natural seperti bahasa ibu, membentuk karakter & wawasan.</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-slate-50 dark:bg-slate-900 p-8 rounded-3xl border border-slate-100 dark:border-slate-700 hover:shadow-xl hover:-translate-y-2 transition-all group">
                    <div class="w-14 h-14 bg-purple-100 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <span class="text-2xl">👨‍🏫</span>
                    </div>
                    <h3 class="text-xl font-bold text-genza-blue dark:text-white mb-3">Tutor Profesional</h3>
                    <p class="text-slate-600 dark:text-slate-400">Dibimbing oleh tutor yang ahli dan berpengalaman.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Fasilitas & Program -->
    <div class="py-24 bg-genza-blue dark:bg-slate-900 transition-colors duration-300" id="program">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <div class="lg:w-1/2">
                    <h2 class="text-4xl font-black text-white mb-6">Membangun Fondasi <span class="text-genza-yellow">Survival English</span></h2>
                    <p class="text-blue-100 text-lg mb-8">
                        Kami merancang program khusus agar anak berani dan terbiasa menggunakan Bahasa Inggris sehari-hari. Jadwal kelas akan dibuat setelah kuota terpenuhi.
                    </p>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center gap-4 bg-blue-800/50 dark:bg-slate-800/80 p-4 rounded-xl border border-blue-700/50 dark:border-slate-700">
                            <span class="bg-genza-yellow text-genza-blue font-bold w-10 h-10 rounded-lg flex items-center justify-center shrink-0">1</span>
                            <div class="text-white font-medium text-lg">Jenjang: SD, SMP, SMA</div>
                        </li>
                        <li class="flex items-center gap-4 bg-blue-800/50 dark:bg-slate-800/80 p-4 rounded-xl border border-blue-700/50 dark:border-slate-700">
                            <span class="bg-genza-yellow text-genza-blue font-bold w-10 h-10 rounded-lg flex items-center justify-center shrink-0">2</span>
                            <div class="text-white font-medium text-lg">Waktu: 3x Pertemuan / Minggu (100 Menit)</div>
                        </li>
                        <li class="flex items-center gap-4 bg-blue-800/50 dark:bg-slate-800/80 p-4 rounded-xl border border-blue-700/50 dark:border-slate-700">
                            <span class="bg-genza-yellow text-genza-blue font-bold w-10 h-10 rounded-lg flex items-center justify-center shrink-0">3</span>
                            <div class="text-white font-medium text-lg">Total Belajar: 60x Pertemuan</div>
                        </li>
                    </ul>
                </div>
                
                <div class="lg:w-1/2 w-full">
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-2xl relative">
                        <div class="absolute -top-6 -right-6 w-24 h-24 bg-genza-yellow rounded-full flex items-center justify-center shadow-lg transform rotate-12">
                            <span class="font-black text-genza-blue text-center leading-tight">Fasilitas<br/>Lengkap</span>
                        </div>
                        <h3 class="text-2xl font-bold text-genza-blue dark:text-white mb-6">Special Facilities</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-700">
                                <span class="text-2xl">✨</span>
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-slate-200">Experiential Learning</h4>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-700">
                                <span class="text-2xl">📖</span>
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-slate-200">G-Book</h4>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-700">
                                <span class="text-2xl">🎓</span>
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-slate-200">Seminar Belajar</h4>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-700">
                                <span class="text-2xl">🏥</span>
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-slate-200">G-Clinic</h4>
                                </div>
                            </div>
                        </div>
                        <div class="mt-8">
                            <a href="{{ route('admission') }}" class="block w-full text-center bg-genza-blue dark:bg-blue-600 text-white font-bold py-4 rounded-xl hover:bg-blue-800 dark:hover:bg-blue-500 transition-colors">
                                Ambil Kuota Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA & Footer -->
    <footer class="bg-slate-900 text-slate-300 py-16 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-12">
                <div>
                    <h2 class="text-3xl font-black text-white mb-4">Hubungi Kami / Tanya-tanya</h2>
                    <p class="text-slate-400 mb-6">Pendaftaran akan ditutup sewaktu-waktu jika kuota sudah terpenuhi. Hubungi kami segera!</p>
                    <div class="flex items-center gap-4">
                        <a href="https://wa.me/6281151100048" target="_blank" class="flex items-center gap-2 bg-green-500 text-white px-6 py-3 rounded-full font-bold hover:bg-green-600 transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.888-.788-1.487-1.761-1.663-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            WhatsApp: 0811-5110-0048
                        </a>
                    </div>
                </div>
                <div class="bg-slate-800 rounded-2xl p-6 border border-slate-700">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-slate-700 rounded-full flex items-center justify-center shrink-0">
                            📍
                        </div>
                        <div>
                            <h4 class="text-white font-bold text-lg mb-2">Genza Education Tanjung PHM Noor</h4>
                            <p class="text-slate-400">Jl. Ir. PHM Noor. RT.4 No. 119, Pembataan<br/>Kec. Murung Pudak, Kab. Tabalong 71571</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-slate-500">&copy; {{ date('Y') }} Genza English Center. All rights reserved.</p>
                <div class="text-slate-500 text-sm">
                    Supported by <span class="font-bold text-white">genza EDUCATION</span>
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
