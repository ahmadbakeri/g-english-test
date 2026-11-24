<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Genza English Center</title>

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
        .login-input:focus {
            outline: none;
            border-color: #0a4282;
            box-shadow: 0 0 0 4px rgba(10, 66, 130, 0.16);
        }
        .dark .login-input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.18);
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 relative overflow-x-hidden">

    <!-- Theme Check -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Ambient background accents -->
    <div class="fixed inset-0 overflow-hidden -z-10 pointer-events-none">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-[#0a4282]/10 dark:bg-[#0a4282]/25 blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 rounded-full bg-[#fbb900]/15 dark:bg-[#fbb900]/10 blur-3xl"></div>
    </div>

    <div class="w-full max-w-[26rem]">
        <!-- Logo -->
        <div class="text-center mb-6">
            <a href="{{ route('landing') }}" class="inline-flex flex-col items-center gap-2 hover:opacity-85 transition-opacity">
                <img src="{{ asset('images/logo.png') }}" alt="Genza English Logo" class="h-14 sm:h-16 w-auto dark:brightness-110">
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl rounded-[1.5rem] shadow-2xl shadow-[#0a4282]/10 dark:shadow-black/50 border border-slate-200/80 dark:border-slate-700/80 overflow-hidden">
            <div class="h-1.5 w-full bg-gradient-to-r from-genza-blue via-genza-yellow to-genza-blue"></div>

            <div class="p-7 sm:p-8">
                <div class="text-center mb-7">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-blue-50 dark:bg-slate-900 text-genza-blue dark:text-genza-yellow border border-blue-200/70 dark:border-slate-700 mb-3">
                        Portal Akademik
                    </span>
                    <h1 class="text-2xl sm:text-[1.65rem] font-extrabold text-slate-900 dark:text-white tracking-tight">Selamat Datang</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Silakan masuk menggunakan Username / ID Anda.</p>
                </div>

                @if($errors->any())
                    <div class="bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/80 text-rose-700 dark:text-rose-300 px-4 py-3.5 rounded-xl mb-6 text-sm flex items-start gap-3 shadow-sm" role="alert">
                        <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <div class="font-medium leading-snug">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form id="login-form" action="{{ route('login.action') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label for="username" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Username / ID</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </span>
                            <input id="username" type="text" name="username" value="{{ old('username') }}" class="login-input w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl pl-11 pr-4 py-3 text-sm sm:text-base text-slate-800 dark:text-slate-100 placeholder-slate-400 transition-all" placeholder="Masukkan Username / NIS / NIP" required autofocus>
                        </div>
                    </div>

                    <div>
                        <label for="password-input" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </span>
                            <input id="password-input" type="password" name="password" class="login-input w-full bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-xl pl-11 pr-11 py-3 text-sm sm:text-base text-slate-800 dark:text-slate-100 placeholder-slate-400 transition-all" placeholder="••••••••" required>
                            <button type="button" id="toggle-password" aria-label="Tampilkan Password" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-genza-blue dark:hover:text-genza-yellow focus:outline-none transition-colors">
                                <svg id="eye-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg id="eye-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                    </div>

                    <div class="pt-1">
                        <button id="login-submit-btn" type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-genza-blue hover:bg-[#083568] disabled:opacity-65 disabled:cursor-not-allowed text-white text-base font-extrabold px-8 py-3.5 rounded-full hover:-translate-y-0.5 transition-all duration-200 shadow-lg shadow-[#0a4282]/25">
                            <span id="login-submit-label">Login</span>
                            <svg class="w-4 h-4 text-genza-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-slate-50/90 dark:bg-slate-900/60 px-7 py-5 border-t border-slate-200/70 dark:border-slate-700/80 text-center">
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    Belum terdaftar sebagai siswa?
                    <a href="{{ route('admission') }}" class="text-genza-blue dark:text-genza-yellow font-extrabold hover:underline ml-1">Registrasi Siswa Baru</a>
                </p>
            </div>
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('landing') }}" class="inline-flex items-center justify-center gap-2 text-sm text-slate-500 hover:text-genza-blue dark:hover:text-slate-200 font-semibold transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Kembali ke Beranda</span>
            </a>
        </div>
    </div>

    <script>
        (function () {
            var toggleBtn = document.getElementById('toggle-password');
            var passInput = document.getElementById('password-input');
            var eyeOpen = document.getElementById('eye-open');
            var eyeClosed = document.getElementById('eye-closed');

            if (toggleBtn && passInput) {
                toggleBtn.addEventListener('click', function () {
                    var isPassword = passInput.getAttribute('type') === 'password';
                    passInput.setAttribute('type', isPassword ? 'text' : 'password');
                    eyeOpen.classList.toggle('hidden', isPassword);
                    eyeClosed.classList.toggle('hidden', !isPassword);
                });
            }

            var loginForm = document.getElementById('login-form');
            var submitBtn = document.getElementById('login-submit-btn');
            var submitLabel = document.getElementById('login-submit-label');
            if (loginForm && submitBtn) {
                loginForm.addEventListener('submit', function () {
                    if (submitBtn.disabled) return;
                    submitBtn.disabled = true;
                    if (submitLabel) submitLabel.textContent = 'Memproses Login...';
                });
            }
        })();
    </script>
</body>
</html>
