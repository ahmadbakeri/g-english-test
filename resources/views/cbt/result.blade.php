@extends('cbt.layout')

@section('title', 'Hasil Ujian - ' . $exam->title)

@section('content')
<div class="h-screen w-screen overflow-y-auto bg-slate-50 flex items-center justify-center p-4">
    <!-- Confetti Canvas overlay -->
    <canvas id="confetti-canvas"></canvas>

    <div class="max-w-2xl w-full bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden animate-fade-in relative z-10">
        
        <!-- Header -->
        <div class="bg-indigo-600 p-8 text-center relative overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-32 h-32 rounded-full bg-indigo-500 opacity-50 blur-2xl"></div>
            <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-32 h-32 rounded-full bg-indigo-700 opacity-50 blur-2xl"></div>
            
            <div class="relative z-10">
                <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl mx-auto flex items-center justify-center mb-4 border border-white/30 shadow-inner">
                    <i class="ph ph-medal text-5xl text-yellow-300 drop-shadow-md"></i>
                </div>
                <h1 class="text-3xl font-black text-white mb-2">Ujian Selesai!</h1>
                <p class="text-indigo-100 font-medium">Terima kasih atas kerja keras Anda.</p>
            </div>
        </div>

        <!-- Body -->
        <div class="p-8">
            <div class="text-center mb-8">
                <h2 class="text-slate-500 font-semibold uppercase tracking-widest text-sm mb-2">Nilai Akhir Anda</h2>
                <div class="text-6xl font-black text-slate-800 drop-shadow-sm">
                    {{ rtrim(rtrim(number_format($attempt->score, 2), '0'), '.') }}
                </div>
            </div>

            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-slate-200 border-dashed last:border-0">
                    <span class="text-slate-500 font-medium">Mata Pelajaran</span>
                    <span class="text-slate-800 font-bold">{{ $exam->subject->name }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-200 border-dashed last:border-0">
                    <span class="text-slate-500 font-medium">Ujian</span>
                    <span class="text-slate-800 font-bold">{{ $exam->title }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-200 border-dashed last:border-0">
                    <span class="text-slate-500 font-medium">Waktu Selesai</span>
                    <span class="text-slate-800 font-bold">{{ $attempt->end_time ? $attempt->end_time->format('d M Y, H:i') : now()->format('d M Y, H:i') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-200 border-dashed last:border-0">
                    <span class="text-slate-500 font-medium">Status Pengumpulan</span>
                    @if($attempt->is_force_submitted)
                        <span class="px-2.5 py-1 rounded-md bg-rose-100 text-rose-700 text-xs font-bold uppercase tracking-wider">Dipaksa / Kehabisan Waktu</span>
                    @else
                        <span class="px-2.5 py-1 rounded-md bg-emerald-100 text-emerald-700 text-xs font-bold uppercase tracking-wider">Sukses</span>
                    @endif
                </div>
            </div>

            <div class="mt-8 flex gap-4">
                <a href="{{ url('/student') }}" class="flex-1 text-center py-4 rounded-xl bg-indigo-50 text-indigo-600 font-bold hover:bg-indigo-100 transition-colors">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const duration = 3 * 1000;
        const end = Date.now() + duration;

        (function frame() {
            confetti({
                particleCount: 5,
                angle: 60,
                spread: 55,
                origin: { x: 0 },
                colors: ['#4f46e5', '#10b981', '#f59e0b']
            });
            confetti({
                particleCount: 5,
                angle: 120,
                spread: 55,
                origin: { x: 1 },
                colors: ['#4f46e5', '#10b981', '#f59e0b']
            });

            if (Date.now() < end) {
                requestAnimationFrame(frame);
            }
        }());
    });
</script>
@endpush
@endsection
