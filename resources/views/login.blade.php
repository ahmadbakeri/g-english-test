<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Genza English Center</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700,800&display=swap" rel="stylesheet" />
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
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen flex items-center justify-center p-4">

    <!-- Theme Check -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="{{ route('landing') }}" class="inline-flex flex-col items-center gap-1 hover:opacity-80 transition-opacity">
                <img src="{{ asset('images/logo.png') }}" alt="Genza English Logo" class="h-16 w-auto mb-2 dark:brightness-110">
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl shadow-blue-900/5 border border-slate-100 dark:border-slate-700 overflow-hidden">
            <div class="p-8">
                <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-2 text-center">Selamat Datang</h3>
                <p class="text-slate-500 dark:text-slate-400 text-center mb-8">Silakan masuk menggunakan akun Anda.</p>
                
                @if($errors->any())
                    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-4 py-3 rounded-xl mb-6 text-sm text-center">
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login.action') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Username / ID</label>
                        <input type="text" name="username" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-genza-blue" placeholder="Masukkan Username" required autofocus>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Password</label>
                        <input type="password" name="password" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-genza-blue" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="w-full bg-genza-blue text-white text-lg font-bold px-8 py-3.5 rounded-xl hover:bg-blue-800 hover:-translate-y-0.5 transition-transform shadow-lg shadow-blue-900/30">
                        Login
                    </button>
                </form>
            </div>
            
            <div class="bg-slate-50 dark:bg-slate-900/50 p-6 border-t border-slate-100 dark:border-slate-700 text-center">
                <p class="text-sm text-slate-600 dark:text-slate-400">Belum mendaftar?</p>
                <a href="{{ route('admission') }}" class="text-genza-blue dark:text-blue-400 font-bold hover:underline mt-1 inline-block">Registrasi Siswa Baru</a>
            </div>
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('landing') }}" class="text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-medium flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>

</body>
</html>
