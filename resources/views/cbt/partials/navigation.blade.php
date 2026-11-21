<div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col h-full overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <h3 class="font-semibold text-slate-800 flex items-center gap-2">
            <i class="ph ph-grid-four text-indigo-500"></i>
            Navigasi Soal
        </h3>
        <span class="text-xs font-medium text-slate-500 px-2.5 py-1 bg-white rounded-md border border-slate-200 shadow-sm">
            <span id="answered-count">0</span>/{{ count($questions) }}
        </span>
    </div>
    
    <div class="p-4 flex-1 overflow-y-auto exam-scroll">
        <div class="grid grid-cols-5 gap-2" id="question-nav-grid">
            @foreach($questions as $index => $q)
                @php $qNum = $index + 1; @endphp
                <button 
                    type="button"
                    onclick="window.examEngine.jumpToQuestion({{ $qNum }})"
                    id="nav-btn-{{ $qNum }}"
                    data-status="unanswered"
                    class="nav-btn group relative w-10 h-10 mx-auto rounded-full text-sm font-bold flex items-center justify-center transition-all duration-300 shadow-sm focus:outline-none hover:scale-105"
                >
                    {{ $qNum }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="p-4 border-t border-slate-100 bg-slate-50/50 space-y-2">
        <div class="flex items-center gap-2 text-xs text-slate-600 font-medium">
            <div class="w-4 h-4 rounded-full border-2 border-slate-300 bg-white shadow-sm"></div>
            <span>Belum dijawab</span>
        </div>
        <div class="flex items-center gap-2 text-xs text-slate-600 font-medium">
            <div class="w-4 h-4 rounded-full border-2 border-[#1a368b] bg-[#1a368b] shadow-sm"></div>
            <span>Sudah dijawab</span>
        </div>
        <div class="flex items-center gap-2 text-xs text-slate-600 font-medium">
            <div class="w-4 h-4 rounded-full border-2 border-[#fbb900] bg-[#fbb900] shadow-sm"></div>
            <span>Ragu-ragu</span>
        </div>
    </div>
</div>
