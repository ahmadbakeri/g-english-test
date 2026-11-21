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

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="list-disc pl-5 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admission.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <!-- DATA SISWA -->
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white border-b pb-2">Data Diri Siswa</h3>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Lengkap Siswa <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-genza-blue" placeholder="Contoh: Budi Santoso" required value="{{ old('name') }}">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tempat Lahir <span class="text-red-500">*</span></label>
                                <input type="text" name="birth_place" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3" required value="{{ old('birth_place') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tanggal Lahir <span class="text-red-500">*</span></label>
                                <input type="date" name="birth_date" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3" required value="{{ old('birth_date') }}">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <select name="gender" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3" required>
                                    <option value="" disabled selected>Pilih...</option>
                                    <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Agama <span class="text-red-500">*</span></label>
                                <input type="text" name="religion" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3" required value="{{ old('religion') }}">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Alamat Rumah Lengkap <span class="text-red-500">*</span></label>
                            <textarea name="address" rows="3" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3" required>{{ old('address') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Email Siswa</label>
                                <input type="email" name="email" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3" value="{{ old('email') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">No. HP / WA Siswa <span class="text-red-500">*</span></label>
                                <input type="text" name="phone" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3" placeholder="08..." required value="{{ old('phone') }}">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Akun Sosmed (IG/Tiktok)</label>
                            <input type="text" name="social_media" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3" placeholder="@username" value="{{ old('social_media') }}">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tingkat <span class="text-red-500">*</span></label>
                                <select name="education_level" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3" required>
                                    <option value="" disabled selected>Pilih Tingkat</option>
                                    <option value="SD" {{ old('education_level') == 'SD' ? 'selected' : '' }}>SD</option>
                                    <option value="SMP" {{ old('education_level') == 'SMP' ? 'selected' : '' }}>SMP</option>
                                    <option value="SMA" {{ old('education_level') == 'SMA' ? 'selected' : '' }}>SMA</option>
                                    <option value="Umum" {{ old('education_level') == 'Umum' ? 'selected' : '' }}>Umum</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Level Course <span class="text-red-500">*</span></label>
                                <input type="text" name="course_level" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3" placeholder="Contoh: Basic/Intermediate" required value="{{ old('course_level') }}">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Sekolah Asal <span class="text-red-500">*</span></label>
                                <input type="text" name="school_name" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3" required value="{{ old('school_name') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kelas <span class="text-red-500">*</span></label>
                                <input type="text" name="class_name" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3" placeholder="Contoh: 5A" required value="{{ old('class_name') }}">
                            </div>
                        </div>

                        <!-- DATA ORANG TUA -->
                        <div class="mb-4 mt-8">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white border-b pb-2">Data Orang Tua</h3>
                        </div>
                        
                        <div class="p-4 border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50/50 dark:bg-slate-800/50 space-y-4 mb-6">
                            <h4 class="font-bold text-enza-blue text-sm">Data Ibu</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Ibu <span class="text-red-500">*</span></label>
                                    <input type="text" name="mother_name" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm" required value="{{ old('mother_name') }}">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pekerjaan Ibu</label>
                                    <input type="text" name="mother_job" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm" value="{{ old('mother_job') }}">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">No. HP / WA Ibu <span class="text-red-500">*</span></label>
                                    <input type="text" name="mother_phone" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm" placeholder="08..." required value="{{ old('mother_phone') }}">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email Ibu</label>
                                    <input type="email" name="mother_email" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm" value="{{ old('mother_email') }}">
                                </div>
                            </div>
                        </div>

                        <div class="p-4 border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50/50 dark:bg-slate-800/50 space-y-4">
                            <h4 class="font-bold text-enza-blue text-sm">Data Ayah</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Ayah</label>
                                    <input type="text" name="father_name" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm" value="{{ old('father_name') }}">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pekerjaan Ayah</label>
                                    <input type="text" name="father_job" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm" value="{{ old('father_job') }}">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">No. HP / WA Ayah</label>
                                    <input type="text" name="father_phone" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm" placeholder="08..." value="{{ old('father_phone') }}">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email Ayah</label>
                                    <input type="email" name="father_email" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm" value="{{ old('father_email') }}">
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                            <button type="submit" class="w-full bg-genza-blue text-white text-lg font-semibold px-8 py-4 rounded-full hover:bg-blue-800 hover:-translate-y-0.5 transition-all duration-300 shadow-md">
                                Kirim Data Pendaftaran
                            </button>
                            <p class="text-center text-xs text-slate-500 mt-4">Dengan mengirim form ini, Anda menyetujui kami untuk menghubungi Anda via WhatsApp. Akun orang tua akan otomatis dibuatkan oleh Admin saat Pendaftaran diterima.</p>
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
