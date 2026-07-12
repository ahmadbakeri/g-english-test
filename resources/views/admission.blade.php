<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran Siswa Baru - Genza English Center</title>
    
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
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen flex flex-col">

    <!-- Navbar Minimal -->
    <nav class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('landing') }}" class="flex items-center gap-2 text-genza-blue dark:text-blue-400 font-black text-xl hover:opacity-80 transition-opacity">
                    <img src="{{ asset('images/logo.png') }}" alt="Genza English Logo" class="h-10 w-auto dark:brightness-110">
                </a>
                <a href="{{ route('landing') }}" class="text-sm font-semibold text-slate-500 hover:text-genza-blue dark:hover:text-blue-400">Kembali ke Beranda</a>
            </div>
        </div>
    </nav>

    <!-- Main Form Content -->
    <main class="flex-grow py-12 px-4 sm:px-6">
        <div class="max-w-2xl mx-auto">
            
            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-2xl p-4 mb-8 flex items-start gap-4">
                <span class="text-2xl mt-1">🚀</span>
                <div>
                    <h3 class="font-bold text-genza-blue dark:text-blue-300">Promo Diskon Hingga 25%!</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Daftar sekarang sebelum kehabisan. Kuota sangat terbatas hanya 5-15 siswa per jenjang.</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div class="bg-genza-blue p-6 text-center">
                    <h1 class="text-2xl font-black text-white">Formulir Pendaftaran</h1>
                    <p class="text-blue-100 text-sm mt-1">Genza English Center Tanjung PHM Noor</p>
                </div>
                
                <div class="p-8">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('admission.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jenjang Pendidikan <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <input type="radio" name="level" id="level_sd" value="SD" class="peer hidden" required>
                                    <label for="level_sd" class="block cursor-pointer text-center bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-xl py-3 text-sm font-bold text-slate-500 peer-checked:bg-blue-50 peer-checked:border-genza-blue peer-checked:text-genza-blue dark:peer-checked:bg-blue-900/50 dark:peer-checked:text-blue-400 hover:bg-slate-100 transition-colors">SD</label>
                                </div>
                                <div>
                                    <input type="radio" name="level" id="level_smp" value="SMP" class="peer hidden" required>
                                    <label for="level_smp" class="block cursor-pointer text-center bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-xl py-3 text-sm font-bold text-slate-500 peer-checked:bg-blue-50 peer-checked:border-genza-blue peer-checked:text-genza-blue dark:peer-checked:bg-blue-900/50 dark:peer-checked:text-blue-400 hover:bg-slate-100 transition-colors">SMP</label>
                                </div>
                                <div>
                                    <input type="radio" name="level" id="level_sma" value="SMA" class="peer hidden" required>
                                    <label for="level_sma" class="block cursor-pointer text-center bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-xl py-3 text-sm font-bold text-slate-500 peer-checked:bg-blue-50 peer-checked:border-genza-blue peer-checked:text-genza-blue dark:peer-checked:bg-blue-900/50 dark:peer-checked:text-blue-400 hover:bg-slate-100 transition-colors">SMA</label>
                                </div>
                            </div>
                            @error('level') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap Siswa <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-genza-blue" placeholder="Contoh: Budi Santoso" required value="{{ old('name') }}">
                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">No. HP / WhatsApp Siswa <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-genza-blue" placeholder="08..." required value="{{ old('phone') }}">
                            @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                          <div class="mb-4">
                              <h3 class="text-lg font-bold text-slate-800 dark:text-white">Data Ibu</h3>
                          </div>
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                              <div>
                                  <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Ibu <span class="text-red-500">*</span></label>
                                  <input type="text" name="mother_name" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-genza-blue" placeholder="Nama Ibu" required value="{{ old('mother_name') }}">
                                  @error('mother_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                              </div>
                              <div>
                                  <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">No. WA Ibu <span class="text-red-500">*</span></label>
                                  <input type="text" name="mother_phone" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-genza-blue" placeholder="08..." required value="{{ old('mother_phone') }}">
                                  @error('mother_phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                              </div>
                              <div>
                                  <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Email Ibu</label>
                                  <input type="email" name="mother_email" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-genza-blue" placeholder="Email Ibu" value="{{ old('mother_email') }}">
                                  @error('mother_email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                              </div>
                              <div>
                                  <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Pekerjaan Ibu</label>
                                  <input type="text" name="mother_job" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-genza-blue" placeholder="Pekerjaan Ibu" value="{{ old('mother_job') }}">
                                  @error('mother_job') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                              </div>
                          </div>

                          <div class="mb-4">
                              <h3 class="text-lg font-bold text-slate-800 dark:text-white">Data Ayah</h3>
                          </div>
                          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                              <div>
                                  <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Ayah</label>
                                  <input type="text" name="father_name" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-genza-blue" placeholder="Nama Ayah" value="{{ old('father_name') }}">
                                  @error('father_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                              </div>
                              <div>
                                  <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">No. WA Ayah</label>
                                  <input type="text" name="father_phone" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-genza-blue" placeholder="08..." value="{{ old('father_phone') }}">
                                  @error('father_phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                              </div>
                              <div>
                                  <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Email Ayah</label>
                                  <input type="email" name="father_email" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-genza-blue" placeholder="Email Ayah" value="{{ old('father_email') }}">
                                  @error('father_email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                              </div>
                              <div>
                                  <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Pekerjaan Ayah</label>
                                  <input type="text" name="father_job" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-genza-blue" placeholder="Pekerjaan Ayah" value="{{ old('father_job') }}">
                                  @error('father_job') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                              </div>
                          </div>

                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                            <button type="submit" class="w-full bg-genza-yellow text-genza-blue text-lg font-bold px-8 py-4 rounded-xl hover:bg-yellow-400 hover:-translate-y-1 transition-transform shadow-lg shadow-yellow-500/30">
                                Kirim Data Pendaftaran
                            </button>
                            <p class="text-center text-xs text-slate-500 mt-4">Dengan mengirim form ini, Anda menyetujui kami untuk menghubungi Anda via WhatsApp.</p>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </main>

    <footer class="py-6 text-center text-slate-500 text-sm">
        &copy; {{ date('Y') }} Genza English Center. Supported by genza EDUCATION.
    </footer>
    
    <!-- Theme Check -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</body>
</html>
