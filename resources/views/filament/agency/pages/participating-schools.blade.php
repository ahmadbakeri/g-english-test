<x-filament-panels::page>
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="text-sm text-slate-600 dark:text-slate-300">
                {{ __('Total') }}: <strong>{{ count($this->schools) }}</strong> {{ __('Participating Schools') }}
            </div>
            <div class="w-full sm:w-80">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search') }}..."
                    class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3.5 py-2 text-sm text-slate-900 dark:text-white focus:border-[#0a4282] focus:outline-none"
                />
            </div>
        </div>

        <x-filament::section>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <th class="py-3 px-3">#</th>
                            <th class="py-3 px-3">{{ __('School Name') }}</th>
                            <th class="py-3 px-3">{{ __('Coordinator') }}</th>
                            <th class="py-3 px-3">{{ __('Coordinator Phone') }}</th>
                            <th class="py-3 px-3">{{ __('Class (Kelas)') }}</th>
                            <th class="py-3 px-3 text-right">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse ($this->schools as $index => $school)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40">
                                <td class="py-3 px-3 text-slate-500">{{ $index + 1 }}</td>
                                <td class="py-3 px-3 font-semibold text-slate-900 dark:text-white">{{ $school['school_name'] }}</td>
                                <td class="py-3 px-3 text-slate-700 dark:text-slate-300">{{ $school['coordinator_name'] }}</td>
                                <td class="py-3 px-3 text-slate-700 dark:text-slate-300">{{ $school['coordinator_phone'] }}</td>
                                <td class="py-3 px-3">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach ($school['grades'] as $grade => $count)
                                            <span class="inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900/40 px-2.5 py-0.5 text-xs font-semibold text-amber-800 dark:text-amber-200">
                                                {{ $grade }}: {{ $count }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-right font-bold text-[#0a4282] dark:text-[#fbb900]">
                                    {{ $school['total'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500 dark:text-slate-400">
                                    {{ __('No participating schools yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
