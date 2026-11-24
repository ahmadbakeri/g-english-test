<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran Siswa Baru - Genza English Center</title>

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
        .form-input:focus {
            outline: none;
            border-color: #0a4282;
            box-shadow: 0 0 0 4px rgba(10, 66, 130, 0.15);
        }
        .dark .form-input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.18);
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen flex flex-col antialiased">

    <!-- Theme Check -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Navbar Minimal -->
    <nav class="sticky top-0 z-40 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-700/80 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('landing') }}" class="flex items-center gap-2 text-genza-blue dark:text-blue-400 font-black text-xl hover:opacity-85 transition-opacity">
                    <img src="{{ asset('images/logo.png') }}" alt="Genza English Logo" class="h-10 w-auto dark:brightness-110">
                </a>
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-genza-blue dark:hover:text-genza-yellow transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Form Content -->
    <main class="flex-grow py-8 sm:py-12 px-4 sm:px-6">
        <div class="max-w-3xl mx-auto">

            <!-- Promo Banner -->
            <div class="bg-gradient-to-r from-[#0a4282] to-[#0d52a0] text-white rounded-2xl p-5 sm:p-6 mb-8 flex items-start sm:items-center gap-4 shadow-lg border border-genza-yellow/40">
                <div class="w-12 h-12 rounded-2xl bg-genza-yellow text-genza-blue flex items-center justify-center shrink-0 font-black shadow">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                </div>
                <div>
                    <span class="inline-block text-[11px] font-extrabold uppercase tracking-wider bg-genza-yellow text-genza-blue px-2.5 py-0.5 rounded-full mb-1">
                        Penawaran Terbatas
                    </span>
                    <h2 class="font-extrabold text-base sm:text-lg text-white">Promo Diskon Pendaftaran Hingga 25%!</h2>
                    <p class="text-xs sm:text-sm text-blue-100 mt-0.5">Daftar sekarang sebelum kehabisan. Kuota eksklusif hanya 5–15 siswa per jenjang kelas.</p>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl border border-slate-200/80 dark:border-slate-700 overflow-hidden">
                <!-- Header -->
                <div class="bg-genza-blue px-6 py-7 sm:px-8 text-center border-b-4 border-genza-yellow">
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Formulir Pendaftaran Siswa Baru</h1>
                    <p class="text-blue-100 text-sm mt-1">Genza English Center — Tanjung PHM Noor</p>
                </div>

                <div class="p-6 sm:p-8 lg:p-10">
                    @if(session('success'))
                        <div class="bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 px-5 py-4 rounded-2xl mb-6 flex items-center gap-3" role="alert">
                            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-sm font-semibold">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 px-5 py-4 rounded-2xl mb-8" role="alert">
                            <div class="flex items-center gap-2 font-extrabold text-sm mb-1.5">
                                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                <span>Mohon periksa kembali isian formulir Anda:</span>
                            </div>
                            <ul class="list-disc pl-6 text-xs sm:text-sm space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admission.store') }}" method="POST" class="space-y-8">
                        @csrf

                        <!-- SECTION 1: BIODATA SISWA -->
                        <section class="p-5 sm:p-6 rounded-2xl bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-700/80 space-y-5">
                            <div class="flex items-center gap-3 pb-3 border-b border-slate-200 dark:border-slate-700">
                                <div class="w-9 h-9 rounded-xl bg-genza-blue text-genza-yellow flex items-center justify-center font-black text-sm shrink-0 shadow-sm">
                                    01
                                </div>
                                <div>
                                    <h3 class="text-lg font-extrabold text-genza-blue dark:text-white leading-tight">Biodata Siswa</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Identitas lengkap dan kontak aktif calon siswa</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" class="form-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm sm:text-base" placeholder="Contoh: Budi Santoso" required value="{{ old('name') }}">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tempat Lahir <span class="text-rose-500">*</span></label>
                                    <input type="text" name="birth_place" class="form-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm sm:text-base" placeholder="Contoh: Tanjung" required value="{{ old('birth_place') }}">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Lahir <span class="text-rose-500">*</span></label>
                                    <input type="date" name="birth_date" class="form-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm sm:text-base" required value="{{ old('birth_date') }}">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Jenis Kelamin <span class="text-rose-500">*</span></label>
                                    <select name="gender" class="form-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm sm:text-base" required>
                                        <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Pilih Jenis Kelamin...</option>
                                        <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Agama <span class="text-rose-500">*</span></label>
                                    <input type="text" name="religion" class="form-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm sm:text-base" placeholder="Contoh: Islam" required value="{{ old('religion') }}">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Alamat Rumah Lengkap <span class="text-rose-500">*</span></label>
                                <textarea name="address" rows="2" class="form-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm sm:text-base" placeholder="Nama Jalan, RT/RW, Kelurahan, Kecamatan" required>{{ old('address') }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">No. HP / WA Siswa <span class="text-rose-500">*</span></label>
                                    <input type="text" name="phone" class="form-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm sm:text-base" placeholder="08..." required value="{{ old('phone') }}">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email Siswa</label>
                                    <input type="email" name="email" class="form-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm sm:text-base" placeholder="siswa@email.com" value="{{ old('email') }}">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Akun Sosmed (IG/TikTok)</label>
                                    <input type="text" name="social_media" class="form-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm sm:text-base" placeholder="@username" value="{{ old('social_media') }}">
                                </div>
                            </div>
                        </section>

                        <!-- SECTION 2: PENDIDIKAN & PROGRAM (INTERNAL / EXTERNAL) -->
                        <section class="p-5 sm:p-6 rounded-2xl bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-700/80 space-y-5">
                            <div class="flex items-center gap-3 pb-3 border-b border-slate-200 dark:border-slate-700">
                                <div class="w-9 h-9 rounded-xl bg-genza-blue text-genza-yellow flex items-center justify-center font-black text-sm shrink-0 shadow-sm">
                                    02
                                </div>
                                <div>
                                    <h3 class="text-lg font-extrabold text-genza-blue dark:text-white leading-tight">Pendidikan & Program</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Kategori program kursus, sekolah asal, dan jenjang kelas</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kategori Program Siswa <span class="text-rose-500">*</span></label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <label class="relative flex items-start gap-3.5 p-4 rounded-xl border-2 cursor-pointer transition-all bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 has-[:checked]:border-genza-blue dark:has-[:checked]:border-sky-400 has-[:checked]:bg-blue-50/50 dark:has-[:checked]:bg-sky-950/30">
                                        <input type="radio" name="student_type" value="internal" class="mt-1 accent-[#0a4282]" {{ old('student_type', 'internal') === 'internal' ? 'checked' : '' }}>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-extrabold text-sm text-slate-900 dark:text-white">Siswa Internal</span>
                                                <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded-full bg-sky-100 text-sky-700 dark:bg-sky-900/60 dark:text-sky-300">Reguler</span>
                                            </div>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Belajar intensif di kelas utama Genza English Center.</p>
                                        </div>
                                    </label>

                                    <label class="relative flex items-start gap-3.5 p-4 rounded-xl border-2 cursor-pointer transition-all bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 has-[:checked]:border-genza-blue dark:has-[:checked]:border-amber-400 has-[:checked]:bg-amber-50/40 dark:has-[:checked]:bg-amber-950/20">
                                        <input type="radio" name="student_type" value="external" class="mt-1 accent-[#0a4282]" {{ old('student_type') === 'external' ? 'checked' : '' }}>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-extrabold text-sm text-slate-900 dark:text-white">Siswa Eksternal</span>
                                                <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300">Mitra Sekolah</span>
                                            </div>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Program ekstrakurikuler / kelas kerjasama sekolah mitra.</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Jenjang Pendidikan <span class="text-rose-500">*</span></label>
                                    <select name="education_level" class="form-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm sm:text-base" required>
                                        <option value="" disabled {{ old('education_level') ? '' : 'selected' }}>Pilih Tingkat...</option>
                                        <option value="SD" {{ old('education_level') == 'SD' ? 'selected' : '' }}>SD</option>
                                        <option value="SMP" {{ old('education_level') == 'SMP' ? 'selected' : '' }}>SMP</option>
                                        <option value="SMA" {{ old('education_level') == 'SMA' ? 'selected' : '' }}>SMA</option>
                                        <option value="Umum" {{ old('education_level') == 'Umum' ? 'selected' : '' }}>Umum</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Level Course <span class="text-rose-500">*</span></label>
                                    <input type="text" name="course_level" class="form-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm sm:text-base" placeholder="Contoh: Survival English / Basic" required value="{{ old('course_level') }}">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Sekolah Asal <span class="text-rose-500">*</span></label>
                                    <input type="text" name="school_name" class="form-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm sm:text-base" placeholder="Contoh: SMAN 1 Tanjung" required value="{{ old('school_name') }}">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Kelas di Sekolah <span class="text-rose-500">*</span></label>
                                    <input type="text" name="class_name" class="form-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm sm:text-base" placeholder="Contoh: 5A / 10 IPA 1" required value="{{ old('class_name') }}">
                                </div>
                            </div>
                        </section>

                        <!-- SECTION 3: DATA ORANG TUA -->
                        <section class="p-5 sm:p-6 rounded-2xl bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-700/80 space-y-5">
                            <div class="flex items-center gap-3 pb-3 border-b border-slate-200 dark:border-slate-700">
                                <div class="w-9 h-9 rounded-xl bg-genza-blue text-genza-yellow flex items-center justify-center font-black text-sm shrink-0 shadow-sm">
                                    03
                                </div>
                                <div>
                                    <h3 class="text-lg font-extrabold text-genza-blue dark:text-white leading-tight">Data Orang Tua / Wali</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Digunakan untuk akses Parent Portal & notifikasi kehadiran siswa</p>
                                </div>
                            </div>

                            <!-- Data Ibu -->
                            <div class="p-4 sm:p-5 border border-slate-200 dark:border-slate-700 rounded-2xl bg-white dark:bg-slate-900 space-y-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-genza-blue dark:bg-genza-yellow"></span>
                                    <h4 class="font-extrabold text-genza-blue dark:text-genza-yellow text-sm uppercase tracking-wider">Data Ibu</h4>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Ibu <span class="text-rose-500">*</span></label>
                                        <input type="text" name="mother_name" class="form-input w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm" required value="{{ old('mother_name') }}">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pekerjaan Ibu</label>
                                        <input type="text" name="mother_job" class="form-input w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm" value="{{ old('mother_job') }}">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">No. HP / WA Ibu <span class="text-rose-500">*</span></label>
                                        <input type="text" name="mother_phone" class="form-input w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm" placeholder="08..." required value="{{ old('mother_phone') }}">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email Ibu</label>
                                        <input type="email" name="mother_email" class="form-input w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm" value="{{ old('mother_email') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Data Ayah -->
                            <div class="p-4 sm:p-5 border border-slate-200 dark:border-slate-700 rounded-2xl bg-white dark:bg-slate-900 space-y-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-genza-blue dark:bg-genza-yellow"></span>
                                    <h4 class="font-extrabold text-genza-blue dark:text-genza-yellow text-sm uppercase tracking-wider">Data Ayah</h4>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Ayah <span class="text-rose-500">*</span></label>
                                        <input type="text" name="father_name" class="form-input w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm" required value="{{ old('father_name') }}">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pekerjaan Ayah</label>
                                        <input type="text" name="father_job" class="form-input w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm" value="{{ old('father_job') }}">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">No. HP / WA Ayah <span class="text-rose-500">*</span></label>
                                        <input type="text" name="father_phone" class="form-input w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm" placeholder="08..." required value="{{ old('father_phone') }}">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Email Ayah</label>
                                        <input type="email" name="father_email" class="form-input w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm" value="{{ old('father_email') }}">
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- SECTION 4: RINCIAN BIAYA & FASILITAS -->
                        <section class="p-5 sm:p-6 rounded-2xl bg-blue-50/60 dark:bg-slate-900/80 border-2 border-genza-blue/20 dark:border-slate-700 space-y-4">
                            <div class="flex items-center justify-between gap-3 pb-3 border-b border-genza-blue/15 dark:border-slate-700">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-genza-blue text-genza-yellow flex items-center justify-center font-black text-sm shrink-0 shadow-sm">
                                        04
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-extrabold text-genza-blue dark:text-white leading-tight">Rincian Biaya & Fasilitas</h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Informasi paket belajar yang termasuk dalam pendaftaran</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-genza-yellow text-genza-blue shadow-sm">
                                    Diskon s/d 25%
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs sm:text-sm">
                                <div class="p-3.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700">
                                    <div class="text-slate-500 dark:text-slate-400 font-semibold">Frekuensi Belajar</div>
                                    <div class="font-extrabold text-genza-blue dark:text-white mt-0.5">3x / Minggu (100 Menit)</div>
                                </div>
                                <div class="p-3.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700">
                                    <div class="text-slate-500 dark:text-slate-400 font-semibold">Total Pertemuan</div>
                                    <div class="font-extrabold text-genza-blue dark:text-white mt-0.5">30x Sesi + Evaluasi CBT</div>
                                </div>
                                <div class="p-3.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700">
                                    <div class="text-slate-500 dark:text-slate-400 font-semibold">Fasilitas Termasuk</div>
                                    <div class="font-extrabold text-genza-blue dark:text-white mt-0.5">G-Book, G-Clinic & Sertifikat</div>
                                </div>
                            </div>

                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                                * Rincian tagihan SPP dan konfirmasi jadwal kelas akan dikirimkan oleh Admin melalui WhatsApp setelah formulir diverifikasi.
                            </p>
                        </section>

                        <!-- Submit CTA -->
                        <div class="pt-2">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2.5 bg-genza-blue hover:bg-[#083568] text-white text-base sm:text-lg font-extrabold px-8 py-4 rounded-full hover:-translate-y-0.5 transition-all duration-200 shadow-xl shadow-[#0a4282]/25">
                                <span>Kirim Data Pendaftaran</span>
                                <svg class="w-5 h-5 text-genza-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                            <p class="text-center text-xs text-slate-500 dark:text-slate-400 mt-4">
                                Dengan mengirim formulir ini, Anda menyetujui tim Genza English menghubungi Anda via WhatsApp. Akun Siswa & Orang Tua akan otomatis dibuatkan saat pendaftaran diterima.
                            </p>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <footer class="py-6 text-center text-slate-500 dark:text-slate-400 text-xs sm:text-sm border-t border-slate-200/60 dark:border-slate-800">
        &copy; {{ date('Y') }} Genza English Center. Supported by <span class="font-bold text-genza-blue dark:text-genza-yellow">genza EDUCATION</span>.
    </footer>
</body>
</html>
