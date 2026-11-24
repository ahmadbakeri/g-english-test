<x-filament-widgets::widget>
    @php
        $groups = $this->shortcutGroups;
        $totalItems = collect($groups)->sum(fn ($g) => count($g['items']));
    @endphp

    <div
        class="gx-ql-shell"
        x-data="{
            activeTab: 'all',
            q: '',
            matchItem(label) {
                if (!this.q.trim()) return true;
                return label.toLowerCase().includes(this.q.trim().toLowerCase());
            },
            groupVisible(key, labels) {
                if (this.activeTab !== 'all' && this.activeTab !== key) return false;
                if (!this.q.trim()) return true;
                return labels.some((l) => l.toLowerCase().includes(this.q.trim().toLowerCase()));
            }
        }"
    >
        {{-- Header & Search --}}
        <div class="gx-ql-head">
            <div class="gx-ql-head-left">
                <span class="gx-ql-head-icon" aria-hidden="true">
                    <x-heroicon-o-squares-2x2 style="width: 1.15rem; height: 1.15rem;" />
                </span>
                <div>
                    <h3 class="gx-ql-title">{{ __('Quick Menu') }}</h3>
                    <p class="gx-ql-sub">{{ $totalItems }} {{ __('Admin shortcuts ready for 1-click access') }}</p>
                </div>
            </div>

            <div class="gx-ql-search-wrap">
                <x-heroicon-o-magnifying-glass class="gx-ql-search-icon" />
                <input
                    type="search"
                    x-model="q"
                    placeholder="{{ __('Filter shortcuts...') }}"
                    aria-label="{{ __('Filter shortcuts...') }}"
                    class="gx-ql-search"
                />
            </div>
        </div>

        {{-- Category Tabs --}}
        <div class="gx-ql-tabs" role="tablist">
            <button
                type="button"
                role="tab"
                @click="activeTab = 'all'"
                :class="activeTab === 'all' ? 'gx-ql-tab gx-ql-tab-active' : 'gx-ql-tab'"
            >
                {{ __('All') }}
                <span class="gx-ql-tab-cnt">{{ $totalItems }}</span>
            </button>

            @foreach ($groups as $group)
                <button
                    type="button"
                    role="tab"
                    @click="activeTab = '{{ $group['key'] }}'"
                    :class="activeTab === '{{ $group['key'] }}' ? 'gx-ql-tab gx-ql-tab-active' : 'gx-ql-tab'"
                >
                    {{ $group['label'] }}
                    <span class="gx-ql-tab-cnt">{{ count($group['items']) }}</span>
                </button>
            @endforeach
        </div>

        {{-- Grouped Shortcut Grid --}}
        <div class="gx-ql-body">
            @foreach ($groups as $group)
                @php
                    $groupLabelsJs = \Illuminate\Support\Js::from(collect($group['items'])->pluck('label')->values()->all());
                @endphp
                <div
                    class="gx-ql-group"
                    x-show="groupVisible('{{ $group['key'] }}', {{ $groupLabelsJs }})"
                    x-cloak
                >
                    <div class="gx-ql-group-title" x-show="activeTab === 'all'">
                        <span>{{ $group['label'] }}</span>
                    </div>

                    <div class="gx-ql-grid">
                        @foreach ($group['items'] as $item)
                            <a
                                href="{{ $item['url'] }}"
                                class="gx-ql-tile"
                                x-show="matchItem({{ \Illuminate\Support\Js::from($item['label']) }})"
                            >
                                <span class="gx-ql-icon ql-{{ $item['tone'] }}">
                                    <x-filament::icon :icon="$item['icon']" style="width: 1.15rem; height: 1.15rem;" />
                                </span>
                                <span class="gx-ql-label">{{ $item['label'] }}</span>
                                @if (!empty($item['badge']))
                                    <span class="gx-ql-badge {{ !empty($item['alert']) ? 'gx-ql-badge-alert' : '' }}">
                                        {{ $item['badge'] }}
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
