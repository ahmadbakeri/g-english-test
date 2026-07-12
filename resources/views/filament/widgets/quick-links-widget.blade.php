<x-filament-widgets::widget>
    <x-filament::section heading="{{ __('Quick Menu') }}" icon="heroicon-o-squares-2x2">
        <style>
            .g-quick-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
                gap: 1rem;
            }
            .g-quick-card {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 1rem;
                border-radius: 0.75rem;
                border: 1px solid rgba(0,0,0,0.05);
                background-color: #f8fafc;
                text-decoration: none;
                transition: all 0.2s ease-in-out;
            }
            .dark .g-quick-card {
                background-color: rgba(255,255,255,0.05);
                border-color: rgba(255,255,255,0.1);
            }
            .g-quick-card:hover {
                background-color: #f1f5f9;
                transform: translateY(-2px);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
            .dark .g-quick-card:hover {
                background-color: rgba(255,255,255,0.1);
            }
            .g-quick-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 0.5rem;
                flex-shrink: 0;
                color: white;
            }
            .g-quick-text {
                font-size: 0.875rem;
                font-weight: 600;
                color: #334155;
            }
            .dark .g-quick-text {
                color: #e2e8f0;
            }
            
            /* Custom Colors */
            .c-blue { background-color: #3b82f6; }
            .c-emerald { background-color: #10b981; }
            .c-violet { background-color: #8b5cf6; }
            .c-amber { background-color: #f59e0b; }
            .c-sky { background-color: #0ea5e9; }
            .c-rose { background-color: #f43f5e; }
            .c-indigo { background-color: #6366f1; }
            .c-teal { background-color: #14b8a6; }
            .c-orange { background-color: #f97316; }
            .c-gray { background-color: #64748b; }
        </style>

        <div class="g-quick-grid">


            {{-- {{ __('Student Management') }} --}}
            @if(auth()->user()->isAdmin())
            <a href="{{ \App\Filament\Resources\HR\StudentResource::getUrl('index') }}" class="g-quick-card">
                <span class="g-quick-icon c-emerald">
                    <x-heroicon-s-user-group style="width: 1.25rem; height: 1.25rem;" />
                </span>
                <span class="g-quick-text">{{ __('Student Management') }}</span>
            </a>

            {{-- Kelola Guru --}}
            <a href="{{ \App\Filament\Resources\HR\TutorResource::getUrl('index') }}" class="g-quick-card">
                <span class="g-quick-icon c-violet">
                    <x-heroicon-s-academic-cap style="width: 1.25rem; height: 1.25rem;" />
                </span>
                <span class="g-quick-text">{{ __('Tutor Management') }}</span>
            </a>
            @endif

            {{-- {{ __('Tests & Scores') }} --}}
            <a href="{{ \App\Filament\Resources\HR\Evaluations\EvaluationResource::getUrl('index') }}" class="g-quick-card">
                <span class="g-quick-icon c-amber">
                    <x-heroicon-s-document-check style="width: 1.25rem; height: 1.25rem;" />
                </span>
                <span class="g-quick-text">{{ __('Tests & Scores') }}</span>
            </a>

            {{-- {{ __('Journal') }} --}}
            <a href="{{ \App\Filament\Resources\Operations\JournalResource::getUrl('index') }}" class="g-quick-card">
                <span class="g-quick-icon c-sky">
                    <x-heroicon-s-book-open style="width: 1.25rem; height: 1.25rem;" />
                </span>
                <span class="g-quick-text">{{ __('Journal') }}</span>
            </a>

            {{-- {{ __('Honors') }} --}}
            @if(auth()->user()->isAdmin())
            <a href="{{ \App\Filament\Resources\Finance\HonorResource::getUrl('index') }}" class="g-quick-card">
                <span class="g-quick-icon c-rose">
                    <x-heroicon-s-trophy style="width: 1.25rem; height: 1.25rem;" />
                </span>
                <span class="g-quick-text">{{ __('Honors') }}</span>
            </a>
            @endif

            {{-- {{ __('Class Room') }} --}}
            <a href="{{ \App\Filament\Resources\Academic\ClassRoomResource::getUrl('index') }}" class="g-quick-card">
                <span class="g-quick-icon c-indigo">
                    <x-heroicon-s-building-library style="width: 1.25rem; height: 1.25rem;" />
                </span>
                <span class="g-quick-text">{{ __('Class Room') }}</span>
            </a>

            {{-- {{ __('Subjects') }} --}}
            @if(auth()->user()->isAdmin())
            <a href="{{ \App\Filament\Resources\Academic\SubjectResource::getUrl('index') }}" class="g-quick-card">
                <span class="g-quick-icon c-teal">
                    <x-heroicon-s-bookmark style="width: 1.25rem; height: 1.25rem;" />
                </span>
                <span class="g-quick-text">{{ __('Subjects') }}</span>
            </a>

            {{-- {{ __('Journal Validations') }} --}}
            <a href="{{ \App\Filament\Resources\Operations\JournalValidationResource::getUrl('index') }}" class="g-quick-card">
                <span class="g-quick-icon c-orange">
                    <x-heroicon-s-shield-check style="width: 1.25rem; height: 1.25rem;" />
                </span>
                <span class="g-quick-text">{{ __('Journal Validations') }}</span>
            </a>

            {{-- {{ __('Settings') }} --}}
            <a href="{{ \App\Filament\Resources\Settings\SettingResource::getUrl('index') }}" class="g-quick-card">
                <span class="g-quick-icon c-gray">
                    <x-heroicon-s-cog-6-tooth style="width: 1.25rem; height: 1.25rem;" />
                </span>
                <span class="g-quick-text">{{ __('Settings') }}</span>
            </a>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
