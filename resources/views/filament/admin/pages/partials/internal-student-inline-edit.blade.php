<div class="gx-edit-box">
    <div class="gx-edit-grid">
        <div>
            <label class="gx-edit-label">Nama Lengkap</label>
            <input type="text" wire:model="editForm.name" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">NIS</label>
            <input type="text" wire:model="editForm.nis" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">Kelas</label>
            <input type="text" wire:model="editForm.class_name" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">Jenis Kelamin</label>
            <select wire:model="editForm.gender" class="gx-edit-input">
                <option value="L">Laki-laki (L)</option>
                <option value="P">Perempuan (P)</option>
            </select>
        </div>
        <div>
            <label class="gx-edit-label">No. HP Siswa</label>
            <input type="text" wire:model="editForm.phone" class="gx-edit-input" />
        </div>
        <div style="display:flex;align-items:flex-end;">
            <label style="display:inline-flex;align-items:center;gap:0.4rem;cursor:pointer;padding:0.4rem 0;font-weight:700;font-size:0.82rem;">
                <input type="checkbox" wire:model="editForm.is_active" />
                <span>Siswa Aktif</span>
            </label>
        </div>
        <div>
            <label class="gx-edit-label">Nama Ayah</label>
            <input type="text" wire:model="editForm.father_name" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">No. HP Ayah</label>
            <input type="text" wire:model="editForm.father_phone" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">Nama Ibu</label>
            <input type="text" wire:model="editForm.mother_name" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">No. HP Ibu</label>
            <input type="text" wire:model="editForm.mother_phone" class="gx-edit-input" />
        </div>
    </div>
    <div style="display:flex;justify-content:flex-end;gap:0.5rem;margin-top:0.75rem;">
        <button type="button" wire:click="cancelInlineEdit" class="gx-btn gx-btn-cancel">Batal</button>
        <button type="button" wire:click="saveInlineEdit" class="gx-btn gx-btn-save">Simpan Perubahan</button>
    </div>
</div>
