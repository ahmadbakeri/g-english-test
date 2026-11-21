<div class="min-h-screen flex flex-col">
    <!-- Top Navbar with Dark/Light Mode Toggle -->
    <nav class="no-print sticky top-0 z-40 glass-card border-b border-slate-200/80 dark:border-slate-700/80">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('landing') }}" class="flex items-center gap-2.5 text-genza-blue dark:text-blue-400 font-black text-xl hover:opacity-85 transition-opacity">
                    <img src="{{ asset('images/logo.png') }}" alt="Genza English Logo" class="h-10 w-auto dark:brightness-110">
                    <span class="hidden sm:inline-block text-sm font-extrabold tracking-tight text-genza-blue dark:text-white">
                        BRAIN <span class="text-genza-yellow">CHAMPION</span>
                    </span>
                </a>
                <div class="flex items-center gap-2.5 sm:gap-3.5">
                    <button type="button"
                            onclick="toggleBrainChampionTheme()"
                            aria-label="Toggle Dark/Light Mode"
                            class="p-2.5 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-genza-blue dark:text-genza-yellow border border-slate-200/80 dark:border-slate-700 transition-all">
                        <svg class="hidden dark:block w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                        <svg class="block dark:hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    </button>

                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs sm:text-sm font-bold text-genza-blue dark:text-genza-yellow bg-blue-50 dark:bg-slate-800 hover:bg-genza-blue hover:text-white border border-blue-200/70 dark:border-slate-700 transition-all">
                        <span>Login Sekolah</span>
                    </a>

                    <a href="{{ route('landing') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs sm:text-sm font-bold text-slate-600 dark:text-slate-300 bg-slate-100/80 dark:bg-slate-800/80 hover:bg-genza-blue hover:text-white dark:hover:bg-genza-yellow dark:hover:text-genza-blue border border-slate-200/70 dark:border-slate-700 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Beranda</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow py-8 sm:py-10 px-4 sm:px-6">
        <div class="max-w-5xl mx-auto">

            <!-- 1. HERO HEADER -->
            <div class="no-print bg-gradient-to-br from-[#0a4282] via-[#0d4f9c] to-[#083366] text-white rounded-3xl p-6 sm:p-8 mb-8 shadow-2xl border-2 border-genza-yellow/60 relative overflow-hidden">
                <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full bg-genza-yellow/15 blur-3xl pointer-events-none"></div>

                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative">
                    <div>
                        <div class="inline-flex flex-wrap items-center gap-2 mb-3">
                            <span class="inline-flex items-center gap-1.5 bg-genza-yellow text-genza-blue font-black text-xs uppercase tracking-wider px-3.5 py-1.5 rounded-full shadow">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                <span>Kompetisi Offline di Gedung • Sistem Gugur (Whiteboard &amp; Kertas — Soal Dibacakan Langsung)</span>
                            </span>
                        </div>

                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight">
                            G-ENGLISH BRAIN CHAMPION
                        </h1>
                        <p class="text-genza-yellow font-black text-base sm:text-xl tracking-wide uppercase mt-1">
                            "ONE MIND, ONE CHAMPION"
                        </p>
                        <p class="text-blue-100 text-xs sm:text-sm mt-2.5 max-w-2xl leading-relaxed">
                            Kompetisi Bahasa Inggris OFFLINE di gedung antar sekolah tingkat SD/MI (Kelas 4, 5, 6) dan SMP/MTs (Kelas 7, 8, 9) menggunakan sistem gugur (Whiteboard &amp; Kertas — soal dibacakan langsung, tanpa CBT online). Kuota terbatas hanya <strong>35 peserta per jenjang kelas</strong>!
                        </p>
                    </div>

                    <div class="shrink-0 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 sm:p-5 text-xs sm:text-sm space-y-2">
                        <div class="font-extrabold text-genza-yellow uppercase tracking-wider text-xs flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Kuota Utusan Per Sekolah</span>
                        </div>
                        <div class="text-white font-semibold">&bull; SD / MI: Maks. <strong>2 Siswa / Kelas</strong> (Kelas 4, 5, 6)</div>
                        <div class="text-white font-semibold">&bull; SMP / MTs: Maks. <strong>1 Siswa / Kelas</strong> (Kelas 7, 8, 9)</div>
                        <div class="text-blue-200 text-xs pt-1 border-t border-white/15">&bull; Kuota Global: <strong>35 Siswa / Kelas</strong></div>
                    </div>
                </div>
            </div>

            <!-- 2. LIVE QUOTA MONITOR (6 CARDS: KELAS 4, 5, 6 & KELAS 7, 8, 9) -->
            <div class="no-print glass-card rounded-3xl p-5 sm:p-6 mb-8">
                <div class="flex items-center justify-between flex-wrap gap-2 mb-4">
                    <h2 class="text-sm sm:text-base font-extrabold text-genza-blue dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>Live Quota Monitor (6 Jenjang Kelas)</span>
                    </h2>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">
                        Update Real-Time &bull; Maksimal 35 Peserta / Kelas
                    </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
                    @foreach($quotaSummary as $grade => $q)
                        @php
                            $used = $q['global_used'];
                            $max = $q['max_global'];
                            $remaining = $q['global_remaining'];
                            $isFull = $q['is_full'];
                            $isWarning = !$isFull && $used >= 28;
                            $pct = min(100, round(($used / max(1, $max)) * 100));
                            $levelTag = $grade <= 6 ? 'SD/MI' : 'SMP/MTs';
                        @endphp
                        <div class="p-3.5 rounded-2xl border transition-all {{ $isFull ? 'bg-rose-50/90 dark:bg-rose-950/40 border-rose-300 dark:border-rose-800' : ($isWarning ? 'bg-amber-50/80 dark:bg-amber-950/30 border-amber-300 dark:border-amber-800' : 'bg-slate-50/90 dark:bg-slate-900/70 border-slate-200/80 dark:border-slate-700') }}">
                            <div class="flex items-center justify-between gap-1 mb-1">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ $levelTag }}</span>
                                @if($isFull)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-rose-600 text-white">
                                        PENUH
                                    </span>
                                @elseif($isWarning)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-500 text-slate-950">
                                        SISA {{ $remaining }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300">
                                        SISA {{ $remaining }}
                                    </span>
                                @endif
                            </div>

                            <div class="font-black text-base {{ $isFull ? 'text-rose-700 dark:text-rose-300' : 'text-genza-blue dark:text-white' }}">
                                {{ $q['class_label'] }}
                            </div>

                            <div class="mt-1 text-xs font-bold {{ $isFull ? 'text-rose-600 dark:text-rose-400' : ($isWarning ? 'text-amber-700 dark:text-amber-300' : 'text-slate-600 dark:text-slate-300') }}">
                                Terisi {{ $used }} / {{ $max }}
                            </div>

                            <div class="mt-2 w-full h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-300 {{ $isFull ? 'bg-rose-600' : ($isWarning ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 3. SUCCESS CREDENTIAL CARD & PRINTABLE PARTICIPANT SUMMARY -->
            @if(count($registeredResults) > 0)
                <div class="print-card glass-card rounded-3xl p-6 sm:p-8 mb-8 border-2 border-emerald-500 dark:border-emerald-500">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 mb-6 border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-lg">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <span class="inline-block px-3 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300 mb-1">
                                    Registrasi &amp; Akun Sekolah Berhasil Dibuat
                                </span>
                                <h3 class="text-xl sm:text-2xl font-black text-genza-blue dark:text-white">
                                    Kartu Peserta &amp; Akses Login Sekolah
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-0.5">
                                    Simpan atau cetak kredensial login sekolah dan kode peserta di bawah ini untuk daftar ulang.
                                </p>
                            </div>
                        </div>

                        <div class="no-print flex items-center gap-2.5 shrink-0">
                            <button type="button"
                                    onclick="window.print()"
                                    class="inline-flex items-center gap-2 bg-genza-yellow hover:bg-yellow-400 text-genza-blue font-black text-xs sm:text-sm px-5 py-2.5 rounded-full shadow-md transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                <span>Cetak / Print Bukti</span>
                            </button>
                        </div>
                    </div>

                    <!-- Auto-Provisioned School Login Credential Card -->
                    @if(!empty($schoolCredentials))
                        <div class="mb-6 p-5 rounded-2xl bg-gradient-to-r from-[#0a4282] to-[#0f529e] text-white border-2 border-genza-yellow/60 shadow-lg">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-genza-yellow text-genza-blue mb-1.5">
                                        Akun Login Koordinator Sekolah
                                    </span>
                                    <h4 class="text-lg font-black text-white">{{ $schoolCredentials['school_name'] ?? $lastSubmittedSchool }}</h4>
                                    <p class="text-xs text-blue-100">Pendamping: {{ $schoolCredentials['pic_name'] ?? $lastSubmittedPicName }}</p>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-3.5 py-2.5 border border-white/20">
                                        <div class="text-blue-200 font-semibold">URL Login Portal</div>
                                        <a href="{{ $schoolCredentials['login_url'] ?? route('login') }}" class="font-black text-genza-yellow hover:underline break-all">
                                            {{ url('/login') }}
                                        </a>
                                    </div>
                                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-3.5 py-2.5 border border-white/20">
                                        <div class="text-blue-200 font-semibold">Username (No. WA)</div>
                                        <div class="font-mono font-black text-white text-sm mt-0.5">{{ $schoolCredentials['username'] }}</div>
                                    </div>
                                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-3.5 py-2.5 border border-white/20">
                                        <div class="text-blue-200 font-semibold">Password / PIN</div>
                                        <div class="font-mono font-black text-genza-yellow text-sm mt-0.5">{{ $schoolCredentials['password'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Summary Table Ready to Screenshot / Print -->
                    <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-700 mb-6">
                        <table class="w-full text-left border-collapse text-xs sm:text-sm">
                            <thead>
                                <tr class="bg-genza-blue text-white uppercase text-[11px] font-extrabold tracking-wider">
                                    <th class="py-3 px-4">Kode Peserta</th>
                                    <th class="py-3 px-4">NISN</th>
                                    <th class="py-3 px-4">Nama Lengkap Siswa</th>
                                    <th class="py-3 px-4 text-center">Kelas</th>
                                    <th class="py-3 px-4 text-center">L/P</th>
                                    <th class="py-3 px-4">Orang Tua &amp; Kontak</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-900">
                                @foreach($registeredResults as $res)
                                    <tr>
                                        <td class="py-3.5 px-4">
                                            <span class="inline-block px-3 py-1 rounded-full bg-genza-blue text-genza-yellow font-black text-xs tracking-wider">
                                                {{ $res['competition_code'] ?? $res['registration_number'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 font-mono font-bold text-slate-700 dark:text-slate-200">
                                            {{ $res['nisn'] ?? '-' }}
                                        </td>
                                        <td class="py-3.5 px-4 font-extrabold text-slate-900 dark:text-white">
                                            {{ $res['name'] }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-sky-100 text-sky-800 dark:bg-sky-900/60 dark:text-sky-300">
                                                {{ $res['class_name'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-bold">
                                            {{ $res['gender'] ?? 'L' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300">
                                            <div class="font-bold text-slate-800 dark:text-slate-100">{{ $res['parent_name'] ?? '-' }}</div>
                                            <div class="text-xs text-slate-500">{{ $res['parent_phone'] ?? '-' }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="no-print flex items-center justify-between flex-wrap gap-3">
                        <button type="button"
                                wire:click="resetRegistrationSuccess"
                                class="inline-flex items-center gap-2 bg-genza-blue hover:bg-[#083568] text-white font-bold text-xs sm:text-sm px-6 py-3 rounded-full transition-all shadow">
                            <span>+ Daftarkan Peserta / Sekolah Lain</span>
                        </button>
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-extrabold text-genza-blue dark:text-genza-yellow hover:underline">
                            <span>Masuk ke Portal Login ({{ url('/login') }})</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            @endif

            <!-- 4. SCHOOL REGISTRATION FORM -->
            <div class="no-print glass-card rounded-3xl overflow-hidden">
                <div class="bg-genza-blue px-6 py-5 sm:px-8 border-b-4 border-genza-yellow flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-white">Formulir Pendaftaran Delegasi Sekolah</h2>
                        <p class="text-blue-100 text-xs sm:text-sm">Pilih jenjang kompetisi, buat kredensial sekolah, dan lengkapi biodata siswa peserta.</p>
                    </div>
                    <span class="px-3.5 py-1.5 rounded-full bg-genza-yellow text-genza-blue font-black text-xs uppercase shadow-sm">
                        {{ $school_level === 'SD' ? 'SD / MI (Kelas 4, 5, 6)' : 'SMP / MTs (Kelas 7, 8, 9)' }}
                    </span>
                </div>

                <form wire:submit="submit" class="p-6 sm:p-8 space-y-8">
                    @if ($errors->any())
                        <div class="bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 px-5 py-4 rounded-2xl text-sm" role="alert">
                            <div class="font-extrabold mb-1">Mohon periksa kembali isian formulir:</div>
                            <ul class="list-disc pl-5 space-y-0.5 text-xs sm:text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- SECTION A: SCHOOL & COORDINATOR CARD -->
                    <section class="p-5 sm:p-6 rounded-2xl bg-slate-50/80 dark:bg-slate-900/55 border border-slate-200/80 dark:border-slate-700 space-y-5">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-200 dark:border-slate-700">
                            <div class="w-9 h-9 rounded-xl bg-genza-blue text-genza-yellow flex items-center justify-center font-black text-sm shrink-0 shadow-sm">01</div>
                            <div>
                                <h3 class="text-lg font-extrabold text-genza-blue dark:text-white">Data Sekolah &amp; Akun Login Pendamping</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">No. WhatsApp Pendamping otomatis menjadi Username login sekolah di <code class="font-bold">/login</code></p>
                            </div>
                        </div>

                        <!-- Reactive Jenjang Toggle (SD/MI vs SMP/MTs) -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jenjang Sekolah <span class="text-rose-500">*</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <label class="relative flex items-start gap-3.5 p-4 rounded-2xl border-2 cursor-pointer transition-all bg-white dark:bg-slate-900 {{ $school_level === 'SD' ? 'border-genza-blue dark:border-genza-yellow ring-2 ring-[#0a4282]/15 dark:ring-[#fbb900]/20' : 'border-slate-200 dark:border-slate-700' }}">
                                    <input type="radio" wire:model.live="school_level" value="SD" class="mt-1 accent-[#0a4282]">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold text-sm sm:text-base text-slate-900 dark:text-white">Tingkat SD / MI</span>
                                            <span class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase rounded-full bg-sky-100 text-sky-700 dark:bg-sky-900/60 dark:text-sky-300">Kelas 4, 5, 6</span>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Setiap sekolah dapat mendaftarkan maks. <strong>2 peserta per kelas</strong>.</p>
                                    </div>
                                </label>

                                <label class="relative flex items-start gap-3.5 p-4 rounded-2xl border-2 cursor-pointer transition-all bg-white dark:bg-slate-900 {{ $school_level === 'SMP' ? 'border-genza-blue dark:border-genza-yellow ring-2 ring-[#0a4282]/15 dark:ring-[#fbb900]/20' : 'border-slate-200 dark:border-slate-700' }}">
                                    <input type="radio" wire:model.live="school_level" value="SMP" class="mt-1 accent-[#0a4282]">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold text-sm sm:text-base text-slate-900 dark:text-white">Tingkat SMP / MTs</span>
                                            <span class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300">Kelas 7, 8, 9</span>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Setiap sekolah dapat mendaftarkan maks. <strong>1 peserta per kelas</strong>.</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Sekolah <span class="text-rose-500">*</span></label>
                                <input type="text"
                                       wire:model.live.debounce.300ms="school_name"
                                       class="bc-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm"
                                       placeholder="{{ $school_level === 'SD' ? 'Contoh: SDN 1 Tanjung' : 'Contoh: SMPN 1 Tanjung' }}"
                                       required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Guru Pendamping <span class="text-rose-500">*</span></label>
                                <input type="text"
                                       wire:model="pic_name"
                                       class="bc-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm"
                                       placeholder="Nama Lengkap Pendamping"
                                       required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">No. WA Pendamping (Username) <span class="text-rose-500">*</span></label>
                                <input type="text"
                                       wire:model="pic_phone"
                                       class="bc-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm"
                                       placeholder="08..."
                                       required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Password / PIN Sekolah</label>
                                <input type="text"
                                       wire:model="school_password"
                                       class="bc-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm"
                                       placeholder="Kosongkan = Auto PIN 6 Digit">
                            </div>
                        </div>
                    </section>

                    <!-- SECTION B: GRADE PARTICIPANT CARDS -->
                    <section class="space-y-5">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-genza-blue text-genza-yellow flex items-center justify-center font-black text-sm shrink-0 shadow-sm">02</div>
                                <div>
                                    <h3 class="text-lg font-extrabold text-genza-blue dark:text-white">
                                        Biodata Peserta Per Kelas ({{ $school_level === 'SD' ? 'SD/MI: Kelas 4, 5, 6' : 'SMP/MTs: Kelas 7, 8, 9' }})
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        Lengkapi NISN (10 digit), Nama Siswa, Jenis Kelamin, Nama Orang Tua, dan No. HP Orang Tua.
                                    </p>
                                </div>
                            </div>
                        </div>

                        @foreach($activeGrades as $grade)
                            @php
                                $q = $quotaSummary[$grade];
                                $maxSlots = $q['max_per_school'];
                                $availableSlots = $q['available_slots'];
                                $isFull = $q['is_full'];
                            @endphp

                            <div class="p-5 sm:p-6 rounded-2xl border-2 transition-all {{ $isFull ? 'bg-rose-50/50 dark:bg-rose-950/25 border-rose-300 dark:border-rose-800/80' : 'bg-slate-50/80 dark:bg-slate-900/55 border-slate-200/80 dark:border-slate-700' }}">
                                <!-- Card Header -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-4 mb-4 border-b border-slate-200 dark:border-slate-700">
                                    <div class="flex items-center gap-2.5 flex-wrap">
                                        <span class="px-3.5 py-1 rounded-full bg-genza-blue text-genza-yellow font-black text-sm shadow-sm">
                                            {{ $q['class_label'] }}
                                        </span>
                                        <span class="text-xs sm:text-sm font-extrabold text-slate-700 dark:text-slate-200">
                                            {{ $maxSlots === 2 ? 'Maks. 2 Peserta / Sekolah' : 'Maks. 1 Peserta / Sekolah' }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($isFull)
                                            <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-rose-600 text-white shadow-sm">
                                                KUOTA PENUH (35/35)
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300">
                                                Terisi {{ $q['global_used'] }} / 35 &bull; Sisa {{ $q['global_remaining'] }} Kursi
                                            </span>
                                        @endif

                                        @if(trim($school_name) !== '' && !$isFull)
                                            <span class="px-3 py-1 rounded-full text-xs font-extrabold {{ $q['school_remaining'] === 0 ? 'bg-rose-600 text-white' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300' }}">
                                                Sisa Slot Sekolah: {{ $availableSlots }} / {{ $maxSlots }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if($isFull)
                                    <div class="p-4 rounded-xl bg-rose-100/90 dark:bg-rose-950/70 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs sm:text-sm font-bold flex items-center gap-2.5 mb-3">
                                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                        <span>KUOTA PENUH (35/35) — Pendaftaran untuk {{ $q['class_label'] }} telah ditutup. Silakan isi peserta pada kelas lain yang masih terbuka.</span>
                                    </div>
                                @elseif(trim($school_name) !== '' && $q['school_remaining'] === 0)
                                    <div class="p-4 rounded-xl bg-amber-100/90 dark:bg-amber-950/70 border border-amber-300 dark:border-amber-800 text-amber-900 dark:text-amber-200 text-xs sm:text-sm font-bold mb-3">
                                        Sekolah <strong>{{ trim($school_name) }}</strong> telah mencapai batas maksimal {{ $maxSlots }} peserta pada {{ $q['class_label'] }}.
                                    </div>
                                @endif

                                <!-- Participant Slots -->
                                <div class="space-y-4">
                                    @for($slotIdx = 0; $slotIdx < $maxSlots; $slotIdx++)
                                        @php
                                            $slotLocked = $slotIdx >= $availableSlots;
                                        @endphp
                                        <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-700 {{ $slotLocked ? 'opacity-60' : '' }}">
                                            <div class="flex items-center justify-between mb-3.5 pb-2.5 border-b border-slate-100 dark:border-slate-800">
                                                <span class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-wider {{ $slotLocked ? 'text-rose-600 dark:text-rose-400' : 'text-genza-blue dark:text-genza-yellow' }}">
                                                    <span>Peserta {{ $slotIdx + 1 }} ({{ $q['class_label'] }})</span>
                                                </span>

                                                @if($isFull)
                                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-rose-600 text-white">
                                                        KUOTA PENUH (35/35)
                                                    </span>
                                                @elseif($slotLocked)
                                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-rose-100 text-rose-700 dark:bg-rose-900/60 dark:text-rose-300">
                                                        SLOT TERKUNCI (SISA KUOTA {{ $q['global_remaining'] }})
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300">
                                                        Slot Tersedia
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">NISN (10 Digit) <span class="text-rose-500">*</span></label>
                                                    <input type="text"
                                                           maxlength="10"
                                                           inputmode="numeric"
                                                           wire:model="participants.{{ $grade }}.{{ $slotIdx }}.nisn"
                                                           @disabled($slotLocked)
                                                           class="bc-input w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm font-mono disabled:cursor-not-allowed"
                                                           placeholder="{{ $slotLocked ? 'Terkunci' : 'Contoh: 0123456789' }}">
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                                                    <input type="text"
                                                           wire:model="participants.{{ $grade }}.{{ $slotIdx }}.name"
                                                           @disabled($slotLocked)
                                                           class="bc-input w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm disabled:cursor-not-allowed"
                                                           placeholder="{{ $slotLocked ? 'KUOTA PENUH (35/35)' : 'Nama Lengkap Peserta' }}">
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                                                    <select wire:model="participants.{{ $grade }}.{{ $slotIdx }}.gender"
                                                            @disabled($slotLocked)
                                                            class="bc-input w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm disabled:cursor-not-allowed">
                                                        <option value="L">Laki-laki (L)</option>
                                                        <option value="P">Perempuan (P)</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Nama Orang Tua <span class="text-rose-500">*</span></label>
                                                    <input type="text"
                                                           wire:model="participants.{{ $grade }}.{{ $slotIdx }}.parent_name"
                                                           @disabled($slotLocked)
                                                           class="bc-input w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm disabled:cursor-not-allowed"
                                                           placeholder="{{ $slotLocked ? 'Terkunci' : 'Nama Ayah / Ibu / Wali' }}">
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">No. HP Orang Tua <span class="text-rose-500">*</span></label>
                                                    <input type="text"
                                                           wire:model="participants.{{ $grade }}.{{ $slotIdx }}.parent_phone"
                                                           @disabled($slotLocked)
                                                           class="bc-input w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm disabled:cursor-not-allowed"
                                                           placeholder="{{ $slotLocked ? 'Terkunci' : '08...' }}">
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">No. HP Siswa (Opsional)</label>
                                                    <input type="text"
                                                           wire:model="participants.{{ $grade }}.{{ $slotIdx }}.phone"
                                                           @disabled($slotLocked)
                                                           class="bc-input w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm disabled:cursor-not-allowed"
                                                           placeholder="{{ $slotLocked ? 'Terkunci' : '08... (Opsional)' }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        @endforeach
                    </section>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="w-full inline-flex items-center justify-center gap-2.5 bg-genza-blue hover:bg-[#083568] disabled:opacity-60 text-white text-base sm:text-lg font-extrabold px-8 py-4 rounded-full hover:-translate-y-0.5 transition-all duration-200 shadow-xl shadow-[#0a4282]/25">
                            <span wire:loading.remove wire:target="submit">Daftarkan Peserta &amp; Buat Akun Login Sekolah</span>
                            <span wire:loading wire:target="submit">Memvalidasi Kuota &amp; Menyimpan Data...</span>
                            <svg class="w-5 h-5 text-genza-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </main>

    <footer class="no-print py-6 text-center text-slate-500 dark:text-slate-400 text-xs sm:text-sm border-t border-slate-200/60 dark:border-slate-800">
        &copy; {{ date('Y') }} Genza English Center — G-English Brain Champion <span class="font-bold text-genza-blue dark:text-genza-yellow">"ONE MIND, ONE CHAMPION"</span>.
    </footer>
</div>
