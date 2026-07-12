<x-filament-panels::page>
    <form wire:submit.prevent="$refresh" class="flex gap-4 items-end mb-6">
        <div>
            <label class="block text-sm font-medium mb-1">Bulan</label>
            <select wire:model.defer="month" class="border-gray-300 rounded-lg shadow-sm">
                @for($i=1; $i<=12; $i++)
                    <option value="{{ $i }}">{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Tahun</label>
            <input type="number" wire:model.defer="year" class="border-gray-300 rounded-lg shadow-sm w-32" />
        </div>
        <x-filament::button type="submit">
            Tampilkan
        </x-filament::button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-filament::section>
            <div class="text-sm text-gray-500 font-medium">Total Pemasukan (SPP Lunas)</div>
            <div class="text-3xl font-bold text-success-600 mt-2">
                Rp {{ number_format($this->income, 0, ',', '.') }}
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 font-medium">Total Pengeluaran (Gaji Tutor Lunas)</div>
            <div class="text-3xl font-bold text-danger-600 mt-2">
                Rp {{ number_format($this->expense, 0, ',', '.') }}
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm text-gray-500 font-medium">Laba/Rugi Bersih</div>
            <div class="text-3xl font-bold mt-2 {{ $this->profit >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                Rp {{ number_format($this->profit, 0, ',', '.') }}
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
