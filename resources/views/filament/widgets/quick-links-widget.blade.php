<x-filament-widgets::widget>
    <x-filament::section heading="{{ __('Quick Menu') }}" icon="heroicon-o-squares-2x2">
        <div class="ql-grid">

            {{-- {{ __('Student Management') }} --}}
            @if(auth()->user()->isAdmin())
            <a href="{{ \App\Filament\Resources\HR\StudentResource::getUrl('index') }}" class="ql-card">
                <span class="ql-icon-box ql-emerald">
                    <x-heroicon-s-user-group style="width: 1.5rem; height: 1.5rem;" />
                </span>
                <span class="ql-label">{{ __('Student Management') }}</span>
            </a>

            {{-- Kelola Guru --}}
            <a href="{{ \App\Filament\Resources\HR\TutorResource::getUrl('index') }}" class="ql-card">
                <span class="ql-icon-box ql-violet">
                    <x-heroicon-s-academic-cap style="width: 1.5rem; height: 1.5rem;" />
                </span>
                <span class="ql-label">{{ __('Tutor Management') }}</span>
            </a>
            @endif

            {{-- {{ __('Tests & Scores') }} --}}
            <a href="{{ \App\Filament\Resources\HR\Evaluations\EvaluationResource::getUrl('index') }}" class="ql-card">
                <span class="ql-icon-box ql-amber">
                    <x-heroicon-s-document-check style="width: 1.5rem; height: 1.5rem;" />
                </span>
                <span class="ql-label">{{ __('Tests & Scores') }}</span>
            </a>

            {{-- {{ __('Journal') }} --}}
            <a href="{{ \App\Filament\Resources\Operations\JournalResource::getUrl('index') }}" class="ql-card">
                <span class="ql-icon-box ql-sky">
                    <x-heroicon-s-book-open style="width: 1.5rem; height: 1.5rem;" />
                </span>
                <span class="ql-label">{{ __('Journal') }}</span>
            </a>

            {{-- {{ __('Honors') }} --}}
            @if(auth()->user()->isAdmin())
            <a href="{{ \App\Filament\Resources\Finance\HonorResource::getUrl('index') }}" class="ql-card">
                <span class="ql-icon-box ql-rose">
                    <x-heroicon-s-trophy style="width: 1.5rem; height: 1.5rem;" />
                </span>
                <span class="ql-label">{{ __('Honors') }}</span>
            </a>
            @endif

            {{-- {{ __('Class Room') }} --}}
            <a href="{{ \App\Filament\Resources\Academic\ClassRoomResource::getUrl('index') }}" class="ql-card">
                <span class="ql-icon-box ql-indigo">
                    <x-heroicon-s-building-library style="width: 1.5rem; height: 1.5rem;" />
                </span>
                <span class="ql-label">{{ __('Class Room') }}</span>
            </a>

            {{-- {{ __('Subjects') }} --}}
            @if(auth()->user()->isAdmin())
            <a href="{{ \App\Filament\Resources\Academic\SubjectResource::getUrl('index') }}" class="ql-card">
                <span class="ql-icon-box ql-teal">
                    <x-heroicon-s-bookmark style="width: 1.5rem; height: 1.5rem;" />
                </span>
                <span class="ql-label">{{ __('Subjects') }}</span>
            </a>

            {{-- {{ __('Journal Validations') }} --}}
            <a href="{{ \App\Filament\Resources\Operations\JournalValidationResource::getUrl('index') }}" class="ql-card">
                <span class="ql-icon-box ql-orange">
                    <x-heroicon-s-shield-check style="width: 1.5rem; height: 1.5rem;" />
                </span>
                <span class="ql-label">{{ __('Journal Validations') }}</span>
            </a>

            {{-- {{ __('Settings') }} --}}
            <a href="{{ \App\Filament\Resources\Settings\SettingResource::getUrl('index') }}" class="ql-card">
                <span class="ql-icon-box ql-slate">
                    <x-heroicon-s-cog-6-tooth style="width: 1.5rem; height: 1.5rem;" />
                </span>
                <span class="ql-label">{{ __('Settings') }}</span>
            </a>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
