@extends('cbt.layout')

@section('title', $exam->title)

@section('content')
    <!-- Topbar -->
    <header class="h-16 bg-[#1a368b] shrink-0 flex items-center justify-between px-6 z-20 shadow-md">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white font-bold">
                <i class="ph ph-student text-xl"></i>
            </div>
            <div>
                <h1 class="text-white font-bold tracking-wide">{{ $exam->title }}</h1>
                <p class="text-indigo-200 text-xs font-medium">{{ Auth::user()->name }} - {{ Auth::user()->username }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <!-- Timer Component -->
            @include('cbt.partials.timer')
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 flex overflow-hidden">
        
        <!-- Questions Area (Left) -->
        <div class="flex-1 flex flex-col p-6 lg:p-8 overflow-hidden relative">
            <div class="w-full max-w-4xl mx-auto h-full flex flex-col relative">
                
                <!-- Loading State (Initial) -->
                <div id="loading-overlay" class="absolute inset-0 bg-slate-50 z-10 flex flex-col items-center justify-center rounded-2xl">
                    <i class="ph ph-spinner-gap text-[#0a4282] text-4xl animate-spin mb-4"></i>
                    <p class="text-slate-500 font-medium">Mempersiapkan Ujian...</p>
                </div>

                <!-- Questions Container -->
                <div class="flex-1 overflow-hidden relative" id="questions-container">
                    @if($questions->isEmpty())
                        <div class="flex flex-col items-center justify-center h-full text-slate-500 text-center px-4">
                            <i class="ph ph-warning-circle text-6xl text-slate-300 mb-4"></i>
                            <h3 class="text-xl font-bold text-slate-700 mb-2">Ujian Belum Memiliki Soal</h3>
                            <p class="text-slate-500">Silakan hubungi administrator atau guru Anda.</p>
                        </div>
                    @else
                        @include('cbt.partials.question-card')
                    @endif
                </div>

                <!-- Bottom Navigation -->
                <div class="h-20 shrink-0 flex items-center justify-between mt-4">
                    <button 
                        type="button" 
                        onclick="window.examEngine.prevQuestion()"
                        id="btn-prev"
                        class="flex items-center gap-2 px-6 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold shadow-sm transition-all hover:bg-slate-50 hover:text-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <i class="ph ph-caret-left text-lg"></i>
                        Soal Sebelumnya
                    </button>
                    
                    <button 
                        type="button" 
                        onclick="window.examEngine.nextQuestion()"
                        id="btn-next"
                        class="flex items-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 text-white font-semibold shadow-md shadow-indigo-200 transition-all hover:bg-indigo-700 hover:shadow-indigo-300 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Soal Berikutnya
                        <i class="ph ph-caret-right text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Navigation (Right) -->
        <aside class="w-80 bg-slate-50 border-l border-slate-200 shrink-0 p-6 flex flex-col relative z-10 hidden lg:flex shadow-[-4px_0_15px_rgba(0,0,0,0.02)]">
            <div class="flex-1 overflow-hidden">
                @include('cbt.partials.navigation')
            </div>

            <div class="mt-6 shrink-0">
                <button 
                    type="button" 
                    onclick="window.examEngine.requestSubmit()"
                    class="w-full flex items-center justify-center gap-2 bg-emerald-500 text-white px-6 py-4 rounded-xl font-bold text-lg shadow-md shadow-emerald-200 transition-all hover:bg-emerald-600 hover:shadow-emerald-300 active:scale-95"
                >
                    <i class="ph ph-check-circle text-xl"></i>
                    Selesai & Kumpulkan
                </button>
            </div>
        </aside>
        
        <!-- Mobile Toggle Sidebar -->
        <button onclick="document.getElementById('mobile-sidebar').classList.toggle('translate-x-full')" class="lg:hidden fixed bottom-6 right-6 w-14 h-14 bg-indigo-600 text-white rounded-full shadow-lg shadow-indigo-300 flex items-center justify-center z-40">
            <i class="ph ph-grid-four text-2xl"></i>
        </button>

        <!-- Mobile Sidebar Overlay -->
        <div id="mobile-sidebar" class="lg:hidden fixed inset-y-0 right-0 w-80 bg-slate-50 border-l border-slate-200 shadow-2xl z-50 transform translate-x-full transition-transform duration-300 flex flex-col p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-slate-800">Menu Ujian</h3>
                <button onclick="document.getElementById('mobile-sidebar').classList.add('translate-x-full')" class="p-2 text-slate-500 hover:bg-slate-200 rounded-full">
                    <i class="ph ph-x text-xl"></i>
                </button>
            </div>
            
            <div class="flex-1 overflow-hidden">
                @include('cbt.partials.navigation')
            </div>

            <div class="mt-6 shrink-0">
                <button 
                    type="button" 
                    onclick="window.examEngine.requestSubmit()"
                    class="w-full flex items-center justify-center gap-2 bg-emerald-500 text-white px-6 py-4 rounded-xl font-bold text-lg shadow-md shadow-emerald-200 transition-all hover:bg-emerald-600 hover:shadow-emerald-300"
                >
                    <i class="ph ph-check-circle text-xl"></i>
                    Selesai & Kumpulkan
                </button>
            </div>
        </div>
    </main>
@endsection

@push('modals')
    <!-- Confirm Submit Modal -->
    <div id="submit-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300 flex items-center justify-center">
        <div class="bg-white w-full max-w-md mx-4 rounded-2xl shadow-2xl p-6 transform scale-95 transition-transform duration-300" id="submit-modal-content">
            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4">
                <i class="ph ph-question text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-slate-800 text-center mb-2">Selesai Ujian?</h3>
            <p class="text-slate-500 text-center mb-6">
                Pastikan semua jawaban sudah terisi dengan benar. <br>
                <span id="unanswered-warning" class="text-rose-500 font-semibold hidden mt-2 block">Anda memiliki soal yang belum dijawab!</span>
            </p>
            
            <div class="flex gap-3">
                <button onclick="window.examEngine.closeSubmitModal()" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50">
                    Batal
                </button>
                <button onclick="window.examEngine.submitExam()" id="btn-confirm-submit" class="flex-1 px-4 py-3 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 flex items-center justify-center gap-2">
                    <i class="ph ph-check text-lg"></i>
                    Ya, Kumpulkan
                </button>
            </div>
        </div>
    </div>
    
    <!-- Safelist for dynamic JS classes -->
    <div class="hidden bg-[#1a368b] text-white border-[#1a368b] bg-[#fbb900] border-[#fbb900] bg-slate-50 text-slate-700"></div>
@endpush

@push('scripts')
    <script>
        window.CBT_CONFIG = {
            baseUrl: '{{ url("/") }}',
            csrfToken: '{{ csrf_token() }}',
            attemptId: '{{ $attempt->id ?? "" }}',
            examTitle: {!! json_encode($exam->title ?? "") !!},
            autoSaveInterval: {{ $exam->auto_save_interval ?? 30 }},
            maxViolations: {{ $attempt->max_violations ?? 3 }},
            timeRemaining: {{ isset($attempt) ? app(\App\Services\ExamService::class)->getTimeRemaining($attempt) : 0 }},
        };
        console.log("CBT_CONFIG initialized:", window.CBT_CONFIG);
    </script>
    @vite(['resources/js/exam-engine.js'])
@endpush
