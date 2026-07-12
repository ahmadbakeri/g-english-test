<x-filament-panels::page>
    <style>
        /* Sembunyikan Header bawaan Filament agar lebih luas */
        header.fi-header {
            display: none !important;
        }

        .custom-qr-page {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            min-height: 100%;
        }
        
        .custom-header {
            background-color: #0369a1; /* Biru gelap elegan */
            color: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .custom-header-title {
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0;
            line-height: 1.2;
        }
        .custom-header-date {
            color: #e0f2fe;
            font-size: 1rem;
            margin-top: 0.25rem;
        }
        .custom-header-time {
            font-size: 2.25rem;
            font-weight: 900;
            letter-spacing: 0.05em;
        }

        .custom-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        @media (min-width: 1024px) {
            .custom-grid {
                grid-template-columns: 6fr 6fr; /* Lebih seimbang agar chat tidak sempit */
            }
        }

        .custom-card {
            background-color: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .dark .custom-card {
            background-color: #1f2937;
            border-color: #374151;
        }

        /* Kolom Kiri: Jadwal */
        .left-col {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .schedules-wrapper {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        @media (min-width: 1280px) {
            .schedules-wrapper {
                grid-template-columns: 1fr 1fr; /* Dua kolom jadwal jika layar sangat lebar */
            }
        }

        .custom-card-header {
            background-color: #f0f9ff;
            border-bottom: 1px solid #e0f2fe;
            padding: 0.75rem 1rem;
            text-align: center;
            border-top: 4px solid #0284c7;
        }
        .dark .custom-card-header {
            background-color: rgba(31, 41, 55, 0.5);
            border-bottom-color: #374151;
        }
        .custom-card-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
            text-transform: uppercase;
        }
        .dark .custom-card-title {
            color: #f9fafb;
        }
        .custom-card-body {
            padding: 1rem;
        }
        
        .schedule-item {
            text-align: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .dark .schedule-item {
            border-bottom-color: #374151;
        }
        .schedule-item:last-child { border-bottom: none; padding-bottom: 0; }
        .schedule-item:first-child { padding-top: 0; }
        .schedule-jam { font-size: 1rem; font-weight: 900; color: #1f2937; margin-bottom: 0.125rem; }
        .dark .schedule-jam { color: #e5e7eb; }
        .schedule-tutor { font-size: 0.75rem; font-weight: 600; color: #4b5563; text-transform: uppercase; }
        .dark .schedule-tutor { color: #9ca3af; }
        .schedule-time { display: inline-block; background-color: #f3f4f6; color: #4b5563; font-size: 0.75rem; font-weight: 500; padding: 0.125rem 0.5rem; border-radius: 9999px; margin-top: 0.25rem; }
        .dark .schedule-time { background-color: #374151; color: #d1d5db; }

        /* Kolom Kanan: QR & Chat */
        .right-col {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .qr-section {
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding: 1rem;
            background-color: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            border-top: 4px solid #0284c7;
        }
        .dark .qr-section {
            background-color: #1f2937;
            border-color: #374151;
        }
        .qr-text-part {
            text-align: center;
            padding: 0 1rem;
        }
        .qr-title { font-size: 1.25rem; font-weight: 800; color: #111827; letter-spacing: 0.05em; margin-bottom: 0.5rem; }
        .dark .qr-title { color: #f9fafb; }
        .qr-subtitle { font-size: 0.75rem; color: #6b7280; margin-bottom: 1rem; }
        .qr-instruction {
            background-color: #111827;
            color: #ffffff;
            text-align: center;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            font-size: 0.75rem;
            display: inline-block;
        }
        
        .qr-box-part {
            background-color: #ffffff;
            padding: 0.75rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #f3f4f6;
        }

        .chat-section {
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }
        .dark .chat-section {
            background-color: #1f2937;
            border-color: #374151;
        }
        .chat-header {
            display: flex; justify-content: space-between; align-items: center; 
            padding: 0.75rem 1rem; background-color: #f8fafc; border-bottom: 1px solid #e5e7eb;
        }
        .dark .chat-header { background-color: rgba(31, 41, 55, 0.5); border-bottom-color: #374151; }
        
        .chat-container {
            padding: 1rem;
            background-color: #f8fafc;
        }
        .dark .chat-container { background-color: rgba(17, 24, 39, 0.2); }
        
        .chat-item {
            background-color: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 0.5rem;
            padding: 0.75rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .dark .chat-item { background-color: #1f2937; border-color: #374151; }
        
        .chat-icon { width: 1.75rem; height: 1.75rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 0.125rem; }
        .icon-late { background-color: #fee2e2; color: #ef4444; }
        .dark .icon-late { background-color: #7f1d1d; color: #f87171; }
        .icon-ontime { background-color: #dcfce3; color: #22c55e; }
        .dark .icon-ontime { background-color: #14532d; color: #4ade80; }
        
        .chat-content { flex: 1; min-width: 0; }
        .chat-name { font-weight: 700; font-size: 0.875rem; margin: 0; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .dark .chat-name { color: #f9fafb; }
        .chat-meta { display: flex; align-items: center; justify-content: space-between; margin-top: 0.125rem; }
        .badge { font-size: 0.625rem; font-weight: 700; padding: 0.125rem 0.375rem; border-radius: 0.25rem; text-transform: uppercase; }
        .badge-student { background-color: #dbeafe; color: #1e40af; }
        .badge-tutor { background-color: #f3e8ff; color: #6b21a8; }
        .badge-employee { background-color: #ffedd5; color: #c2410c; }
        .status-late { font-size: 0.625rem; font-weight: 700; color: #ef4444; }
        .status-ontime { font-size: 0.625rem; font-weight: 700; color: #22c55e; }
    </style>

    <div class="custom-qr-page" wire:poll.3s>
        
        <!-- Header -->
        <div x-data="clock()" class="custom-header">
            <div>
                <h1 class="custom-header-title">{{ __('SCHEDULE AND ATTENDANCE') }}</h1>
                <p class="custom-header-date" x-text="dateString"></p>
            </div>
            <div class="custom-header-time" x-text="timeString"></div>
        </div>

        <!-- Grid Utama -->
        <div class="custom-grid">
            
            <!-- Kolom Kiri: Jadwal Kelas -->
            <div class="left-col">
                @if($this->schedules->isEmpty())
                    <div class="custom-card" style="padding: 4rem 2rem; text-align: center; color: #6b7280;">
                        <svg style="width: 4rem; height: 4rem; margin: 0 auto 1rem auto; opacity: 0.5;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p style="font-size: 1.25rem; font-weight: 600;">{{ __('No class schedule today.') }}</p>
                    </div>
                @else
                    <div class="schedules-wrapper">
                        @php
                            $groupedSchedules = $this->schedules->groupBy('class_id');
                        @endphp

                        @foreach($groupedSchedules as $classId => $classSchedules)
                            @php
                                $className = $classSchedules->first()->classRoom?->name ?? __('Unknown Class');
                            @endphp
                            <div class="custom-card">
                                <div class="custom-card-header">
                                    <h2 class="custom-card-title">{{ $className }}</h2>
                                </div>
                                <div class="custom-card-body">
                                    @foreach($classSchedules as $index => $schedule)
                                        @php
                                            $subject = $schedule->classRoom?->subject?->name ?? __('Subject');
                                            $tutorName = $schedule->tutor?->name ?? __('Unknown Tutor');
                                            $start = $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '-';
                                            $end = $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '-';
                                        @endphp
                                        <div class="schedule-item">
                                            <h3 class="schedule-jam" style="text-transform: uppercase;">{{ $subject }}</h3>
                                            <div class="schedule-tutor">{{ $tutorName }}</div>
                                            <div class="schedule-time">{{ $start }} - {{ $end }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Kolom Kanan: QR & Live Chat -->
            <div class="right-col">
                
                <!-- QR Code Box (Sekarang Menyamping agar hemat ruang vertikal) -->
                <div class="qr-section">
                    <div class="qr-text-part">
                        <h2 class="qr-title">{!! __('SCAN RFID<br>/ QR CODE') !!}</h2>
                        <p class="qr-subtitle">{!! __('Students and Tutors must<br>Check-in & Check-out') !!}</p>
                        <div class="qr-instruction">
                            {{ __('SCAN CARD / APP') }}
                        </div>
                    </div>
                    <div class="qr-box-part">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(140)->margin(0)->generate($this->qrCodeUrl) !!}
                    </div>
                </div>

                <!-- Live Chat Box -->
                <div class="chat-section">
                    <div class="chat-header">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="position: relative; display: flex; width: 0.75rem; height: 0.75rem;">
                                <span style="position: absolute; display: inline-flex; height: 100%; width: 100%; border-radius: 9999px; background-color: #4ade80; opacity: 0.75; animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
                                <span style="position: relative; display: inline-flex; border-radius: 9999px; height: 0.75rem; width: 0.75rem; background-color: #22c55e;"></span>
                            </span>
                            <h3 class="custom-card-title" style="font-size: 1rem; text-transform: none;">{{ __('Live Incoming Chat') }}</h3>
                        </div>
                        <span style="font-size: 0.75rem; font-weight: 700; background-color: #e0f2fe; color: #0369a1; padding: 0.125rem 0.5rem; border-radius: 9999px;">
                            {{ $this->recentAttendances->count() }}
                        </span>
                    </div>
                    <div class="chat-container">
                        @if($this->recentAttendances->isEmpty())
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: #9ca3af;">
                                <svg style="width: 2.5rem; height: 2.5rem; margin-bottom: 0.5rem; opacity: 0.5;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                                </svg>
                                <p style="font-size: 0.75rem;">{{ __('No attendance yet.') }}</p>
                            </div>
                        @else
                            @foreach($this->recentAttendances as $attendance)
                                @php
                                    $name = '';
                                    if ($attendance->role_type === 'student') $name = $attendance->student?->name;
                                    elseif ($attendance->role_type === 'tutor') $name = $attendance->tutor?->name;
                                    elseif ($attendance->role_type === 'employee') $name = $attendance->user?->name;
                                    
                                    $badgeClass = match($attendance->role_type) {
                                        'student' => 'badge-student',
                                        'tutor' => 'badge-tutor',
                                        'employee' => 'badge-employee',
                                        default => ''
                                    };

                                    $isLate = ($attendance->status === 'late');
                                @endphp
                                
                                <div class="chat-item" wire:key="chat-item-{{ $attendance->id }}">
                                    <div class="chat-icon {{ $isLate ? 'icon-late' : 'icon-ontime' }}">
                                        @if($isLate)
                                            <svg style="width: 1rem; height: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        @else
                                            <svg style="width: 1rem; height: 1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        @endif
                                    </div>
                                    <div class="chat-content">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <p class="chat-name">{{ $name ?? __('Unknown') }}</p>
                                            <span style="font-size: 0.625rem; color: #9ca3af;">{{ $attendance->created_at->format('H:i:s') }}</span>
                                        </div>
                                        <div class="chat-meta">
                                            <span class="badge {{ $badgeClass }}">
                                                {{ $attendance->role_type }}
                                                @if($attendance->classRoom)
                                                    • {{ $attendance->classRoom->name }}
                                                @endif
                                            </span>
                                            <span class="{{ $isLate ? 'status-late' : 'status-ontime' }}">
                                                {{ $isLate ? __('Late') : __('On Time') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- AlpineJS for Clock and Tailwind Animation support -->
    <style>
        @keyframes ping {
            75%, 100% { transform: scale(2); opacity: 0; }
        }
    </style>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('clock', () => ({
                timeString: '',
                dateString: '',
                init() {
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);
                },
                updateTime() {
                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    this.timeString = `${hours}.${minutes}.${seconds}`;

                    const days = ['{{ __('Sunday') }}', '{{ __('Monday') }}', '{{ __('Tuesday') }}', '{{ __('Wednesday') }}', '{{ __('Thursday') }}', '{{ __('Friday') }}', '{{ __('Saturday') }}'];
                    const months = ['{{ __('January') }}', '{{ __('February') }}', '{{ __('March') }}', '{{ __('April') }}', '{{ __('May') }}', '{{ __('June') }}', '{{ __('July') }}', '{{ __('August') }}', '{{ __('September') }}', '{{ __('October') }}', '{{ __('November') }}', '{{ __('December') }}'];
                    
                    const dayName = days[now.getDay()];
                    const dayNum = now.getDate();
                    const monthName = months[now.getMonth()];
                    const year = now.getFullYear();
                    
                    this.dateString = `${dayName}, ${dayNum} ${monthName} ${year}`;
                }
            }))
        })
    </script>
</x-filament-panels::page>
