@php
    $previewUrl = $editPhoto ? $editPhoto->temporaryUrl() : $this->photoUrl($editForm['photo'] ?? null);
    $initials = strtoupper(collect(explode(' ', trim((string) ($editForm['name'] ?? 'S'))))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->join(''));
@endphp

<div class="gx-edit-box">
    {{-- Foto Profil Siswa --}}
    <div style="display:flex;flex-direction:column;align-items:center;gap:0.55rem;padding-bottom:0.85rem;border-bottom:1px dashed rgba(15,23,42,0.1);">
        <div class="gx-photo-preview">
            @if ($previewUrl)
                <img src="{{ $previewUrl }}" alt="Foto Siswa" style="width:100%;height:100%;object-fit:cover;" />
            @else
                <span>{{ $initials ?: 'S' }}</span>
            @endif
        </div>
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:0.45rem;">
            <label class="gx-btn gx-btn-card" style="cursor:pointer;">
                Ganti Foto Profil
                <input type="file" wire:model="editPhoto" accept="image/*" style="display:none;" />
            </label>
            @if ($previewUrl)
                <button type="button" wire:click="removePhoto" class="gx-btn gx-btn-cancel" style="padding:0.3rem 0.7rem;font-size:0.74rem;">Hapus Foto</button>
            @endif
        </div>
        <div wire:loading wire:target="editPhoto" style="font-size:0.75rem;color:#0a4282;font-weight:600;">Mengunggah foto...</div>
        @error('editPhoto') <div style="font-size:0.75rem;color:#f43f5e;font-weight:600;">{{ $message }}</div> @enderror
    </div>

    {{-- Section 1: Data Siswa Lengkap --}}
    <div class="gx-section-title">Data Siswa</div>
    <div class="gx-edit-grid">
        <div>
            <label class="gx-edit-label">Nama Lengkap</label>
            <input type="text" wire:model="editForm.name" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">No. Siswa (NIS)</label>
            <input type="text" wire:model="editForm.nis" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">NISN (10 Digit)</label>
            <input type="text" wire:model="editForm.nisn" placeholder="Opsional" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">Kelas (Survival English)</label>
            <select wire:model="editForm.class_name" class="gx-edit-input">
                <option value="Survival English SD/MI">Survival English SD/MI</option>
                <option value="Survival English SMP/MTs">Survival English SMP/MTs</option>
                <option value="Survival English SMA/MA">Survival English SMA/MA</option>
            </select>
        </div>
        <div>
            <label class="gx-edit-label">Asal Sekolah</label>
            <input type="text" wire:model="editForm.school_name" placeholder="Nama sekolah asal" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">Jenis Kelamin</label>
            <select wire:model="editForm.gender" class="gx-edit-input">
                <option value="L">Laki-laki (L)</option>
                <option value="P">Perempuan (P)</option>
            </select>
        </div>
        <div>
            <label class="gx-edit-label">Tempat Lahir</label>
            <input type="text" wire:model="editForm.birth_place" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">Tanggal Lahir</label>
            <input type="date" wire:model="editForm.birth_date" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">Agama</label>
            <input type="text" wire:model="editForm.religion" placeholder="Islam / Kristen / ..." class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">No. HP / WA Siswa</label>
            <input type="text" wire:model="editForm.phone" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">Media Sosial (IG / TikTok)</label>
            <input type="text" wire:model="editForm.social_media" class="gx-edit-input" />
        </div>
        <div style="display:flex;align-items:flex-end;">
            <label style="display:inline-flex;align-items:center;gap:0.45rem;cursor:pointer;padding:0.42rem 0;font-weight:700;font-size:0.83rem;">
                <input type="checkbox" wire:model="editForm.is_active" />
                <span>Siswa Aktif</span>
            </label>
        </div>
        <div style="grid-column:1 / -1;">
            <label class="gx-edit-label">Alamat Lengkap</label>
            <textarea rows="2" wire:model="editForm.address" class="gx-edit-input"></textarea>
        </div>
    </div>

    {{-- Section 2: Data Ayah --}}
    <div class="gx-section-title">Data Ayah</div>
    <div class="gx-edit-grid">
        <div>
            <label class="gx-edit-label">Nama Ayah</label>
            <input type="text" wire:model="editForm.father_name" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">No. HP / WA Ayah</label>
            <input type="text" wire:model="editForm.father_phone" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">Pekerjaan Ayah</label>
            <input type="text" wire:model="editForm.father_job" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">Email Ayah</label>
            <input type="email" wire:model="editForm.father_email" class="gx-edit-input" />
        </div>
    </div>

    {{-- Section 3: Data Ibu --}}
    <div class="gx-section-title">Data Ibu</div>
    <div class="gx-edit-grid">
        <div>
            <label class="gx-edit-label">Nama Ibu</label>
            <input type="text" wire:model="editForm.mother_name" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">No. HP / WA Ibu</label>
            <input type="text" wire:model="editForm.mother_phone" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">Pekerjaan Ibu</label>
            <input type="text" wire:model="editForm.mother_job" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">Email Ibu</label>
            <input type="email" wire:model="editForm.mother_email" class="gx-edit-input" />
        </div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:0.5rem;margin-top:1rem;padding-top:0.7rem;border-top:1px solid rgba(15,23,42,0.08);">
        <button type="button" wire:click="cancelInlineEdit" class="gx-btn gx-btn-cancel">Batal</button>
        <button type="button" wire:click="saveInlineEdit" class="gx-btn gx-btn-save">Simpan Perubahan</button>
    </div>
</div>
