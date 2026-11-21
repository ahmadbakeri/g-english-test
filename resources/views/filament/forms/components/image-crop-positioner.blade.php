<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $imageField = $getImageField();
        $record = $getRecord();
        $imageUrl = null;
        if ($record && $record->$imageField) {
            $imageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($record->$imageField);
        }
    @endphp

    <div x-data="{
        showCropper: false,
        cropX: 50,
        cropY: 50,
        state: $wire.$entangle('{{ $getStatePath() }}', true),
        
        init() {
            if (this.state) {
                try {
                    let parsed = JSON.parse(this.state);
                    this.cropX = parsed.x !== undefined ? parsed.x : 50;
                    this.cropY = parsed.y !== undefined ? parsed.y : 50;
                } catch(e) {}
            }
            
            $watch('cropX', val => this.updateState());
            $watch('cropY', val => this.updateState());
        },
        
        updateState() {
            this.state = JSON.stringify({ x: parseInt(this.cropX), y: parseInt(this.cropY) });
        }
    }">
        @if($imageUrl)
            <!-- Toggle Button -->
            <button type="button" @click="showCropper = !showCropper" class="mt-2 flex items-center gap-2 px-4 py-2 text-sm font-medium text-genza-blue bg-blue-50 border border-blue-200 rounded-full hover:bg-blue-100 transition-colors">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-text="showCropper ? 'Tutup Pengatur Crop' : 'Atur Ulang Posisi Crop (Non-Destruktif)'"></span>
            </button>
            
            <!-- Cropper Panel -->
            <div x-show="showCropper" x-transition style="display: flex; gap: 2rem; align-items: flex-start; margin-top: 1rem; padding: 1.5rem; background: white; border: 1px solid #e2e8f0; border-radius: 0.75rem; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1);">
                <!-- Preview Circle -->
                <div style="width: 150px; height: 150px; border-radius: 50%; overflow: hidden; border: 4px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); flex-shrink: 0; background: #e2e8f0;">
                    <img src="{{ $imageUrl }}" style="width: 100%; height: 100%; object-fit: cover;" :style="'object-position: ' + cropX + '% ' + cropY + '%;'" />
                </div>
                
                <!-- Controls -->
                <div style="flex-grow: 1; display: flex; flex-direction: column; gap: 1rem; justify-content: center;">
                    <div>
                        <div style="display: flex; justify-content: space-between;">
                            <label style="font-size: 0.875rem; font-weight: 600; color: #475569;">Geser Horizontal (Kiri/Kanan)</label>
                            <span style="font-size: 0.875rem; color: #64748b; background: #f1f5f9; padding: 0.125rem 0.5rem; border-radius: 999px;" x-text="cropX + '%'"></span>
                        </div>
                        <input type="range" min="0" max="100" x-model="cropX" style="width: 100%; margin-top: 0.5rem; cursor: pointer;">
                    </div>
                    <div>
                        <div style="display: flex; justify-content: space-between;">
                            <label style="font-size: 0.875rem; font-weight: 600; color: #475569;">Geser Vertikal (Atas/Bawah)</label>
                            <span style="font-size: 0.875rem; color: #64748b; background: #f1f5f9; padding: 0.125rem 0.5rem; border-radius: 999px;" x-text="cropY + '%'"></span>
                        </div>
                        <input type="range" min="0" max="100" x-model="cropY" style="width: 100%; margin-top: 0.5rem; cursor: pointer;">
                    </div>
                </div>
            </div>
        @else
            <div style="padding: 1rem; background: #f8fafc; border-radius: 0.5rem; color: #64748b; font-size: 0.875rem; text-align: center; border: 1px dashed #cbd5e1; margin-top: 0.5rem;">
                👉 <strong>Simpan data terlebih dahulu</strong> untuk mengaktifkan pengaturan posisi crop foto.
            </div>
        @endif
    </div>
</x-dynamic-component>
