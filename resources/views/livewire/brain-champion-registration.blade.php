<div class="min-h-screen flex flex-col">
    <!-- Top Navbar -->
    <nav class="sticky top-0 z-40 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-700/80 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('landing') }}" class="flex items-center gap-2.5 text-genza-blue dark:text-blue-400 font-black text-xl hover:opacity-85 transition-opacity">
                    <img src="{{ asset('images/logo.png') }}" alt="Genza English Logo" class="h-10 w-auto dark:brightness-110">
                </a>
                <div class="flex items-center gap-3">
                    <a href="{{ route('landing') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-genza-blue dark:hover:text-genza-yellow transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="flex-grow py-8 sm:py-12 px-4 sm:px-6">
        <div class="max-w-4xl mx-auto">

            <!-- Hero Competition Header -->
            <div class="bg-gradient-to-br from-[#0a4282] via-[#0d4f9c] to-[#083366] text-white rounded-3xl p-6 sm:p-8 mb-8 shadow-xl border-2 border-genza-yellow/60 relative overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 bg-genza-yellow text-genza-blue font-black text-xs uppercase tracking-widest px-3.5 py-1.5 rounded-full mb-3 shadow">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <span>Official English Competition</span>
                        </div>
                        <h1 class="text-2xl sm:text-4xl font-black tracking-tight text-white leading-tight">
                            G-ENGLISH BRAIN CHAMPION
                        </h1>
                        <p class="text-genza-yellow font-extrabold text-sm sm:text-lg tracking-wide uppercase mt-1">
                            "ONE MIND, ONE CHAMPION"
                        </p>
                        <p class="text-blue-100 text-xs sm:text-sm mt-2 max-w-2xl leading-relaxed">
                            Kompetisi Bahasa Inggris tingkat SD/MI (Kelas 4, 5, 6) &amp; SMP/MTs (Kelas 7, 8, 9). Kuota terbatas maksimal <strong>35 peserta per jenjang kelas</strong>.
                        </p>
                    </div>
                    <div class="shrink-0 bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-4 text-xs sm:text-sm space-y-1.5">
                        <div class="font-extrabold text-genza-yellow uppercase tracking-wider text-xs">Aturan Utusan Sekolah</div>
                        <div class="text-white font-semibold">&bull; SD / MI: Maks. <strong>2 Siswa / Kelas</strong> (4, 5, 6)</div>
                        <div class="text-white font-semibold">&bull; SMP / MTs: Maks. <strong>1 Siswa / Kelas</strong> (7, 8, 9)</div>
                        <div class="text-blue-200 text-xs">&bull; Kuota Global: <strong>35 Siswa / Kelas</strong></div>
                    </div>
                </div>
            </div>

            <!-- Live Quota Dashboard Bar (Grades 4 - 9) -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 sm:p-6 mb-8 shadow-md border border-slate-200/80 dark:border-slate-700">
                <div class="flex items-center justify-between flex-wrap gap-2 mb-4">
                    <h2 class="text-sm sm:text-base font-extrabold text-genza-blue dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>Status Kuota Real-Time (Maks. 35 Siswa / Kelas)</span>
                    </h2>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Otomatis terkunci saat mencapai 35/35</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    @foreach($quotaSummary as $grade => $q)
                        <div class="p-3 rounded-xl border {{ $q['is_full'] ? 'bg-rose-50/80 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800' : 'bg-slate-50 dark:bg-slate-900/70 border-slate-200/80 dark:border-slate-700' }}">
                            <div class="flex items-center justify-between">
                                <span class="font-extrabold text-xs sm:text-sm {{ $q['is_full'] ? 'text-rose-700 dark:text-rose-300' : 'text-genza-blue dark:text-white' }}">
                                    {{ $q['class_label'] }}
                                </span>
                                <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded-full {{ $q['is_full'] ? 'bg-rose-600 text-white' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300' }}">
                                    {{ $q['is_full'] ? 'PENUH' : 'BUKA' }}
                                </span>
                            </div>
                            <div class="mt-1.5 flex items-baseline justify-between">
                                <span class="text-lg font-black {{ $q['is_full'] ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-white' }}">
                                    {{ $q['global_used'] }}<span class="text-xs font-bold text-slate-400">/{{ $q['max_global'] }}</span>
                                </span>
                                <span class="text-[11px] font-bold {{ $q['is_full'] ? 'text-rose-500' : 'text-sky-600 dark:text-sky-400' }}">
                                    Sisa {{ $q['global_remaining'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Registration Success Receipt -->
            @if(count($registeredResults) > 0)
                <div class="bg-emerald-50 dark:bg-emerald-950/70 border-2 border-emerald-500 rounded-3xl p-6 sm:p-8 mb-8 shadow-xl">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-emerald-900 dark:text-emerald-100">Pendaftaran Brain Champion Berhasil!</h3>
                            <p class="text-sm text-emerald-700 dark:text-emerald-300 mt-0.5">
                                Simpan Nomor Peserta di bawah ini untuk registrasi ulang dan login CBT.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mb-6">
                        @foreach($registeredResults as $res)
                            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-emerald-200 dark:border-emerald-800 flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ $res['school_name'] }} &bull; {{ $res['class_name'] }}</div>
                                    <div class="font-extrabold text-base text-slate-900 dark:text-white mt-0.5">{{ $res['name'] }}</div>
                                    @if(!empty($res['username']))
                                        <div class="text-xs text-slate-500 mt-0.5">ID Portal CBT: <strong class="text-genza-blue dark:text-genza-yellow">{{ $res['username'] }}</strong></div>
                                    @endif
                                </div>
                                <span class="px-3 py-1.5 rounded-xl bg-genza-blue text-genza-yellow font-black text-sm tracking-wider shrink-0">
                                    {{ $res['registration_number'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" wire:click="resetRegistrationSuccess" class="inline-flex items-center gap-2 bg-genza-blue hover:bg-[#083568] text-white font-bold text-sm px-6 py-3 rounded-full transition-all">
                        <span>Daftarkan Peserta / Sekolah Lain</span>
                    </button>
                </div>
            @endif

            <!-- Main Registration Form Card -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl border border-slate-200/80 dark:border-slate-700 overflow-hidden">
                <div class="bg-genza-blue px-6 py-5 sm:px-8 border-b-4 border-genza-yellow flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-white">Formulir Delegasi Sekolah</h2>
                        <p class="text-blue-100 text-xs sm:text-sm">Isi nama sekolah terlebih dahulu untuk mengecek sisa kuota delegasi sekolah Anda.</p>
                    </div>
                    <span class="px-3.5 py-1 rounded-full bg-genza-yellow text-genza-blue font-black text-xs uppercase">
                        {{ $school_level === 'SD' ? 'Tingkat SD / MI (Kelas 4–6)' : 'Tingkat SMP / MTs (Kelas 7–9)' }}
                    </span>
                </div>

                <form wire:submit="submit" class="p-6 sm:p-8 space-y-8">
                    @if ($errors->any())
                        <div class="bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 px-5 py-4 rounded-2xl text-sm" role="alert">
                            <div class="font-extrabold mb-1">Mohon periksa kembali:</div>
                            <ul class="list-disc pl-5 space-y-0.5 text-xs sm:text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- STEP 1: JENJANG & DATA SEKOLAH -->
                    <section class="p-5 sm:p-6 rounded-2xl bg-slate-50/80 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-700 space-y-5">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-200 dark:border-slate-700">
                            <div class="w-9 h-9 rounded-xl bg-genza-blue text-genza-yellow flex items-center justify-center font-black text-sm shrink-0">01</div>
                            <div>
                                <h3 class="text-lg font-extrabold text-genza-blue dark:text-white">Data Sekolah &amp; Pendamping (PIC)</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Pilih jenjang kompetisi dan masukkan nama resmi sekolah</p>
                            </div>
                        </div>

                        <!-- Level Switcher -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kategori Jenjang Sekolah <span class="text-rose-500">*</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <label class="relative flex items-start gap-3.5 p-4 rounded-xl border-2 cursor-pointer transition-all bg-white dark:bg-slate-900 {{ $school_level === 'SD' ? 'border-genza-blue dark:border-genza-yellow bg-blue-50/40 dark:bg-slate-800' : 'border-slate-200 dark:border-slate-700' }}">
                                    <input type="radio" wire:model.live="school_level" value="SD" class="mt-1 accent-[#0a4282]">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold text-sm text-slate-900 dark:text-white">Tingkat SD / MI</span>
                                            <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded-full bg-sky-100 text-sky-700 dark:bg-sky-900/60 dark:text-sky-300">Kelas 4, 5, 6</span>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Maksimal 2 siswa per kelas (Total 6 siswa per sekolah).</p>
                                    </div>
                                </label>

                                <label class="relative flex items-start gap-3.5 p-4 rounded-xl border-2 cursor-pointer transition-all bg-white dark:bg-slate-900 {{ $school_level === 'SMP' ? 'border-genza-blue dark:border-genza-yellow bg-blue-50/40 dark:bg-slate-800' : 'border-slate-200 dark:border-slate-700' }}">
                                    <input type="radio" wire:model.live="school_level" value="SMP" class="mt-1 accent-[#0a4282]">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold text-sm text-slate-900 dark:text-white">Tingkat SMP / MTs</span>
                                            <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300">Kelas 7, 8, 9</span>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Maksimal 1 siswa per kelas (Total 3 siswa per sekolah).</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-1">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Sekolah Asal <span class="text-rose-500">*</span></label>
                                <input type="text" wire:model.live.debounce.300ms="school_name" class="bc-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm" placeholder="{{ $school_level === 'SD' ? 'Contoh: SDN 1 Tanjung' : 'Contoh: SMPN 1 Tanjung' }}" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Guru / Orang Tua PIC <span class="text-rose-500">*</span></label>
                                <input type="text" wire:model="pic_name" class="bc-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm" placeholder="Nama Pendamping" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">No. WhatsApp PIC <span class="text-rose-500">*</span></label>
                                <input type="text" wire:model="pic_phone" class="bc-input w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm" placeholder="08..." required>
                            </div>
                        </div>
                    </section>

                    <!-- STEP 2: DAFTAR PESERTA PER KELAS -->
                    <section class="space-y-5">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-genza-blue text-genza-yellow flex items-center justify-center font-black text-sm shrink-0">02</div>
                                <div>
                                    <h3 class="text-lg font-extrabold text-genza-blue dark:text-white">Daftar Peserta Delegasi ({{ $school_level === 'SD' ? 'Kelas 4, 5, 6' : 'Kelas 7, 8, 9' }})</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Kosongkan kolom kelas apabila sekolah tidak mengirimkan wakil pada kelas tersebut.</p>
                                </div>
                            </div>
                        </div>

                        @foreach($activeGrades as $grade)
                            @php
                                $q = $quotaSummary[$grade];
                                $maxSlots = $q['max_per_school'];
                                $availableSlots = $q['available_slots'];
                            @endphp

                            <div class="p-5 sm:p-6 rounded-2xl border-2 transition-all {{ $q['is_full'] ? 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200 dark:border-rose-800/70' : 'bg-slate-50/80 dark:bg-slate-900/50 border-slate-200/80 dark:border-slate-700' }}">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3.5 mb-4 border-b border-slate-200 dark:border-slate-700">
                                    <div class="flex items-center gap-2.5">
                                        <span class="px-3 py-1 rounded-xl bg-genza-blue text-genza-yellow font-black text-sm">
                                            {{ $q['class_label'] }}
                                        </span>
                                        <span class="text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-200">
                                            Maks. {{ $maxSlots }} Siswa / Sekolah
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-extrabold {{ $q['is_full'] ? 'bg-rose-600 text-white' : 'bg-sky-100 text-sky-800 dark:bg-sky-900/50 dark:text-sky-300' }}">
                                            Kuota Global: {{ $q['global_used'] }}/{{ $q['max_global'] }} (Sisa {{ $q['global_remaining'] }})
                                        </span>
                                        @if(trim($school_name) !== '')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-extrabold {{ $q['school_remaining'] === 0 ? 'bg-amber-500 text-white' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300' }}">
                                                Sisa Slot Sekolah Anda: {{ $availableSlots }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if($q['is_full'])
                                    <div class="p-4 rounded-xl bg-rose-100/80 dark:bg-rose-950/60 text-rose-800 dark:text-rose-200 text-xs sm:text-sm font-bold flex items-center gap-2.5">
                                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                        <span>Mohon maaf, kuota {{ $q['class_label'] }} sudah penuh (35/35). Anda tetap dapat mendaftarkan peserta pada kelas lain yang masih tersedia.</span>
                                    </div>
                                @elseif(trim($school_name) !== '' && $q['school_remaining'] === 0)
                                    <div class="p-4 rounded-xl bg-amber-100/80 dark:bg-amber-950/60 text-amber-900 dark:text-amber-200 text-xs sm:text-sm font-bold">
                                        Sekolah <strong>{{ trim($school_name) }}</strong> sudah memenuhi batas maksimal {{ $maxSlots }} siswa untuk {{ $q['class_label'] }}.
                                    </div>
                                @endif

                                <div class="space-y-3 mt-3">
                                    @for($slotIdx = 0; $slotIdx < $maxSlots; $slotIdx++)
                                        @php
                                            $slotLocked = $slotIdx >= $availableSlots;
                                        @endphp
                                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center p-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700 {{ $slotLocked ? 'opacity-55' : '' }}">
                                            <div class="sm:col-span-2">
                                                <span class="inline-flex items-center gap-1.5 text-xs font-extrabold uppercase tracking-wider {{ $slotLocked ? 'text-rose-500' : 'text-genza-blue dark:text-genza-yellow' }}">
                                                    @if($slotLocked)
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                                        <span>Terkunci</span>
                                                    @else
                                                        <span>Peserta #{{ $slotIdx + 1 }}</span>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="sm:col-span-5">
                                                <input type="text"
                                                       wire:model="participants.{{ $grade }}.{{ $slotIdx }}.name"
                                                       @disabled($slotLocked)
                                                       class="bc-input w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm disabled:cursor-not-allowed"
                                                       placeholder="{{ $slotLocked ? 'Slot Kuota Tidak Tersedia' : 'Nama Lengkap Siswa (Kelas ' . $grade . ')' }}">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <select wire:model="participants.{{ $grade }}.{{ $slotIdx }}.gender"
                                                        @disabled($slotLocked)
                                                        class="bc-input w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm disabled:cursor-not-allowed">
                                                    <option value="L">Laki-laki (L)</option>
                                                    <option value="P">Perempuan (P)</option>
                                                </select>
                                            </div>
                                            <div class="sm:col-span-3">
                                                <input type="text"
                                                       wire:model="participants.{{ $grade }}.{{ $slotIdx }}.phone"
                                                       @disabled($slotLocked)
                                                       class="bc-input w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-sm disabled:cursor-not-allowed"
                                                       placeholder="No. WA Siswa (Opsional)">
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        @endforeach
                    </section>

                    <!-- Submit CTA -->
                    <div class="pt-2">
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="w-full inline-flex items-center justify-center gap-2.5 bg-genza-blue hover:bg-[#083568] disabled:opacity-60 text-white text-base sm:text-lg font-extrabold px-8 py-4 rounded-full hover:-translate-y-0.5 transition-all duration-200 shadow-xl shadow-[#0a4282]/25">
                            <span wire:loading.remove wire:target="submit">Daftarkan Peserta Brain Champion</span>
                            <span wire:loading wire:target="submit">Memproses &amp; Mengunci Kuota...</span>
                            <svg class="w-5 h-5 text-genza-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="py-6 text-center text-slate-500 dark:text-slate-400 text-xs sm:text-sm border-t border-slate-200/60 dark:border-slate-800">
        &copy; {{ date('Y') }} Genza English Center — G-English Brain Champion "One Mind, One Champion".
    </footer>
</div>
