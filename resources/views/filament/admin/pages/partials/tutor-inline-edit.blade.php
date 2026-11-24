<div class="gx-edit-box">
    {{-- Foto Profil + Identitas Tutor --}}
    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:1rem;padding-bottom:0.85rem;border-bottom:1px dashed rgba(15,23,42,0.1);margin-bottom:0.85rem;">
        @php
            $previewUrl = null;
            if ($editPhoto) {
                try { $previewUrl = $editPhoto->temporaryUrl(); } catch (\Throwable) {}
            } elseif (!empty($editForm['current_photo'])) {
                $previewUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($editForm['current_photo']);
            }
        @endphp
        @if ($previewUrl)
            <img src="{{ $previewUrl }}" alt="Foto Tutor" class="gx-photo-preview" style="width:4.5rem;height:4.5rem;border-radius:9999px;" />
        @else
            <div class="gx-photo-preview" style="width:4.5rem;height:4.5rem;border-radius:9999px;font-size:1.15rem;">
                {{ mb_strtoupper(mb_substr(trim((string) ($editForm['name'] ?? 'GT')), 0, 2)) }}
            </div>
        @endif

        <div style="flex:1 1 220px;">
            <label class="gx-edit-label">Foto Profil Tutor (Tampil di Beranda &amp; Profil)</label>
            <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.5rem;">
                <input type="file" wire:model="editPhoto" accept="image/*" class="gx-edit-input" style="max-width:260px;padding:0.35rem 0.55rem;" />
                @if (!empty($editForm['current_photo']))
                    <button type="button" wire:click="removeTutorPhoto" class="gx-btn gx-btn-del">Hapus Foto</button>
                @endif
            </div>
            <div wire:loading wire:target="editPhoto" style="font-size:0.74rem;color:#0a4282;margin-top:0.25rem;">Mengunggah foto...</div>
        </div>
    </div>

    <div class="gx-section-title" style="margin-top:0;">Biodata Tutor</div>
    <div class="gx-edit-grid">
        <div>
            <label class="gx-edit-label">Nama Lengkap</label>
            <input type="text" wire:model="editForm.name" class="gx-edit-input" />
        </div>
        <div>
            <label class="gx-edit-label">Spesialisasi</label>
            <input type="text" wire:model="editForm.specialization" class="gx-edit-input" placeholder="Speaking / TOEFL / Kids" />
        </div>
        <div>
            <label class="gx-edit-label">Grade Honor</label>
            <select wire:model="editForm.grade" class="gx-edit-input">
                <option value="A">Grade A (Rp 50.000/JP)</option>
                <option value="B">Grade B (Rp 35.000/JP)</option>
                <option value="C">Grade C (Rp 25.000/JP)</option>
            </select>
        </div>
        <div>
            <label class="gx-edit-label">No. HP / WhatsApp</label>
            <input type="text" wire:model="editForm.phone" class="gx-edit-input" />
        </div>
        <div style="display:flex;align-items:flex-end;">
            <label style="display:inline-flex;align-items:center;gap:0.4rem;cursor:pointer;padding:0.4rem 0;font-weight:700;font-size:0.82rem;">
                <input type="checkbox" wire:model="editForm.is_active" />
                <span>Status Tutor Aktif</span>
            </label>
        </div>
    </div>

    <div class="gx-section-title">Akun Login Tutor (Bisa Login via Username, ID Buatan / NIP, atau Email)</div>
    <div class="gx-edit-grid">
        <div>
            <label class="gx-edit-label">Username Login</label>
            <input type="text" wire:model="editForm.username" class="gx-edit-input gx-mono" placeholder="Contoh: mr_bakeri / GE48101" />
        </div>
        <div>
            <label class="gx-edit-label">ID Buatan / NIP (Bisa untuk Login)</label>
            <input type="text" wire:model="editForm.nip" class="gx-edit-input gx-mono" placeholder="Contoh: GE48101 atau ID buatan" />
        </div>
        <div>
            <label class="gx-edit-label">Email Login</label>
            <input type="email" wire:model="editForm.email" class="gx-edit-input" placeholder="nama@g-english.com" />
        </div>
        <div>
            <label class="gx-edit-label">Password Baru (Opsional)</label>
            <input type="text" wire:model="editForm.new_password" class="gx-edit-input" placeholder="Kosongkan jika tidak diganti" />
        </div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:0.5rem;margin-top:0.85rem;">
        <button type="button" wire:click="cancelInlineEdit" class="gx-btn gx-btn-cancel">Batal</button>
        <button type="button" wire:click="saveInlineEdit" wire:loading.attr="disabled" class="gx-btn gx-btn-save">Simpan Perubahan</button>
    </div>
</div>
