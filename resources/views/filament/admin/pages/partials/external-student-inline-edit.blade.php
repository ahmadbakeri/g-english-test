<div class="space-y-3 text-xs sm:text-sm">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Nama Siswa</label>
            <input type="text" wire:model="editForm.name" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2" />
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">NISN (10 digit)</label>
            <input type="text" wire:model="editForm.nisn" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2" />
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Kelas</label>
            <select wire:model="editForm.class_name" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2">
                @foreach (['Kelas 4', 'Kelas 5', 'Kelas 6', 'Kelas 7', 'Kelas 8', 'Kelas 9'] as $cls)
                    <option value="{{ $cls }}">{{ $cls }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Sekolah</label>
            <input type="text" wire:model="editForm.school_name" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2" />
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Jenis Kelamin</label>
            <select wire:model="editForm.gender" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2">
                <option value="L">Laki-laki (L)</option>
                <option value="P">Perempuan (P)</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">No HP Siswa</label>
            <input type="text" wire:model="editForm.phone" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2" />
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">Nama Orang Tua / Wali</label>
            <input type="text" wire:model="editForm.parent_name" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2" />
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">No HP Orang Tua</label>
            <input type="text" wire:model="editForm.parent_phone" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2" />
        </div>
        <div class="flex items-end gap-2">
            <label class="inline-flex items-center gap-2 cursor-pointer py-2">
                <input type="checkbox" wire:model="editForm.is_active" class="rounded border-slate-300 text-primary-600" />
                <span class="font-semibold">Aktif</span>
            </label>
        </div>
    </div>

    <div class="flex justify-end gap-2 pt-2">
        <button type="button" wire:click="cancelInlineEdit" class="px-3.5 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 font-semibold">
            Batal
        </button>
        <button type="button" wire:click="saveInlineEdit" class="px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold">
            Simpan Perubahan
        </button>
    </div>
</div>
