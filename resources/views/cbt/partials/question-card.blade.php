@foreach($questions as $index => $q)
    @php $qNum = $index + 1; @endphp
    <div id="question-card-{{ $qNum }}" class="question-card hidden flex-col h-full animate-fade-in">
        
        <!-- Question Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-3">
                <span class="flex items-center justify-center w-10 h-10 rounded-full bg-[#1a368b]/10 text-[#1a368b] font-black">
                    {{ $qNum }}
                </span>
                Soal Pilihan Ganda
            </h2>
            
            <button 
                type="button" 
                onclick="window.examEngine.toggleDoubt({{ $qNum }})"
                id="btn-doubt-{{ $qNum }}"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 border-2 border-slate-200 text-slate-600 hover:bg-slate-50 focus:outline-none"
            >
                <i class="ph ph-flag text-lg"></i>
                Ragu-ragu
            </button>
        </div>

        <!-- Question Body -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6 flex-1 overflow-y-auto exam-scroll">
            <div class="prose max-w-none text-slate-700 text-lg leading-relaxed question-content">
                {!! $q->question_text !!}
            </div>
            
            @if($q->attachment)
                @php
                    $extension = strtolower(pathinfo($q->attachment, PATHINFO_EXTENSION));
                    $isAudio = in_array($extension, ['mp3', 'wav', 'ogg', 'm4a', 'aac']);
                    $isVideo = in_array($extension, ['mp4', 'webm', 'ogg']);
                @endphp
                <div class="mt-6 border border-slate-100 rounded-xl overflow-hidden bg-slate-50 p-4">
                    @if($isAudio)
                        <audio controls class="w-full mt-2 mb-2">
                            <source src="{{ Storage::url($q->attachment) }}" type="audio/{{ $extension == 'mp3' ? 'mpeg' : $extension }}">
                            Browser Anda tidak mendukung elemen audio. (Tautan: <a href="{{ Storage::url($q->attachment) }}">Download Audio</a>)
                        </audio>
                    @elseif($isVideo)
                        <video controls class="w-full max-w-2xl mx-auto rounded-lg">
                            <source src="{{ Storage::url($q->attachment) }}" type="video/{{ $extension }}">
                            Browser Anda tidak mendukung elemen video.
                        </video>
                    @else
                        <img src="{{ Storage::url($q->attachment) }}" alt="Lampiran Soal" class="max-w-full rounded-lg mx-auto shadow-sm">
                    @endif
                </div>
            @endif

            <!-- Options -->
            <div class="mt-8 space-y-3">
                @php
                    $options = [
                        'A' => $q->option_a,
                        'B' => $q->option_b,
                        'C' => $q->option_c,
                        'D' => $q->option_d,
                        'E' => $q->option_e,
                    ];
                    // Optional shuffling could be applied here if shuffle_options is true
                @endphp

                @foreach($options as $key => $text)
                    @if($text)
                        <label class="group relative flex items-start gap-4 p-4 rounded-xl border-2 border-slate-200 cursor-pointer transition-all duration-200 hover:border-[#1a368b]/30 hover:bg-[#1a368b]/5 has-[:checked]:border-[#1a368b] has-[:checked]:bg-[#1a368b]/10 shadow-sm">
                            <div class="flex items-center h-6 mt-0.5">
                                <input 
                                    type="radio" 
                                    name="answer_{{ $q->id }}" 
                                    value="{{ $key }}"
                                    class="option-radio w-5 h-5 border-slate-300 text-[#1a368b] focus:ring-[#1a368b]"
                                    onchange="window.examEngine.selectAnswer({{ $qNum }}, {{ $q->id }}, '{{ $key }}')"
                                    {{ isset($answers[$q->id]) && $answers[$q->id] === $key ? 'checked' : '' }}
                                >
                            </div>
                            <div class="flex-1 text-slate-700 group-has-[:checked]:text-[#1a368b] group-has-[:checked]:font-semibold">
                                <span class="font-bold text-slate-400 group-has-[:checked]:text-[#1a368b] mr-2">{{ $key }}.</span>
                                {!! $text !!}
                            </div>
                        </label>
                    @endif
                @endforeach
            </div>
        </div>

    </div>
@endforeach
