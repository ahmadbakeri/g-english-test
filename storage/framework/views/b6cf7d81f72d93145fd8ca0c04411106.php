<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <style>
        /* Base styles */
        .scan-page {
            max-width: 32rem;
            margin: 0 auto;
            padding: 1rem;
        }

        /* Card Container */
        .scan-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .scan-card-header {
            background: linear-gradient(135deg, #0a4282, #fbb900);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        .scan-card-header h1 {
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0 0 0.5rem 0;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .scan-card-header p {
            color: #e0f2fe;
            font-size: 0.95rem;
            margin: 0;
            opacity: 0.9;
        }

        /* Camera Wrapper */
        .scan-camera-wrapper {
            position: relative;
            width: 100%;
            background-color: #000;
            aspect-ratio: 4/3;
            overflow: hidden;
        }

        /* Video element */
        #scanVideo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* Scanner Overlay */
        .scan-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.4);
            z-index: 10;
        }

        /* Transparent cutout box */
        .scan-frame {
            position: relative;
            width: 250px;
            height: 250px;
            border-radius: 12px;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5); /* darkened outside */
            background: transparent;
            z-index: 11;
        }

        /* Corner accents */
        .scan-frame .corner {
            position: absolute;
            width: 20px;
            height: 20px;
            border-color: #0ea5e9;
            border-style: solid;
            z-index: 12;
            transition: all 0.3s ease;
        }

        .scan-frame .tl { top: -2px; left: -2px; border-width: 4px 0 0 4px; border-top-left-radius: 12px; }
        .scan-frame .tr { top: -2px; right: -2px; border-width: 4px 4px 0 0; border-top-right-radius: 12px; }
        .scan-frame .bl { bottom: -2px; left: -2px; border-width: 0 0 4px 4px; border-bottom-left-radius: 12px; }
        .scan-frame .br { bottom: -2px; right: -2px; border-width: 0 4px 4px 0; border-bottom-right-radius: 12px; }

        /* Scanning Animation Line */
        .scan-frame::before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            height: 2px;
            background: #0ea5e9;
            box-shadow: 0 0 8px #0ea5e9, 0 0 15px #0ea5e9;
            animation: scan-line 2.5s infinite cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 13;
        }

        @keyframes scan-line {
            0% { top: 10%; opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { top: 90%; opacity: 0; }
        }

        /* Status Indicator */
        .scan-status {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem;
            background: white;
            border-bottom: 1px solid #f1f5f9;
        }
        .scan-status .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ef4444; /* default red */
        }
        .scan-status.ready .dot {
            background: #22c55e;
            box-shadow: 0 0 8px #22c55e;
            animation: pulse-green 2s infinite;
        }
        .scan-status span {
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
        }

        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
            70% { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        /* Instructions list */
        .scan-instructions {
            padding: 1.5rem;
            background: #f8fafc;
            text-align: center;
        }
        .scan-instructions p {
            margin: 0 0 0.5rem 0;
            color: #334155;
            font-size: 0.95rem;
        }
        .scan-instructions .hint {
            color: #94a3b8;
            font-size: 0.8rem;
            margin: 0;
        }

        /* Toast Container */
        .scan-toast {
            position: fixed;
            bottom: -100px;
            left: 50%;
            transform: translateX(-50%);
            background: #334155;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 9999px;
            font-weight: 500;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transition: bottom 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 50;
            max-width: 90vw;
            width: max-content;
        }
        .scan-toast.show {
            bottom: 2rem;
        }
        .scan-toast.success { background: #16a34a; }
        .scan-toast.error { background: #dc2626; }
        .scan-toast.warning { background: #f59e0b; color: #fff; }

        .scan-toast .toast-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Camera error state */
        .camera-error {
            padding: 3rem 1.5rem;
            text-align: center;
            background: #fef2f2;
            color: #991b1b;
        }
        .camera-error svg {
            width: 3rem;
            height: 3rem;
            margin: 0 auto 1rem;
            color: #ef4444;
        }
        .camera-error p {
            font-size: 0.875rem;
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .scan-page { padding: 0.5rem; }
            .scan-card { border-radius: 0.75rem; }
            .scan-card-header { padding: 1rem; }
            .scan-card-header h1 { font-size: 1.1rem; }
        }
    </style>

    <!-- Custom Toast Notification -->
    <div id="scanToast" class="scan-toast">
        <div class="toast-icon" id="toastIcon"></div>
        <span id="toastMessage"></span>
    </div>

    <!-- QR Code Reader Library (lightweight) -->
    <script src="https://unpkg.com/jsqr@1.4.0/dist/jsQR.js"></script>

    <div class="scan-page" x-data="{
        video: null,
        canvas: null,
        ctx: null,
        stream: null,
        scanning: false,
        lastScanned: '',
        lastScanTime: 0,
        cameraError: false,
        
        init() {
            this.video = document.getElementById('scanVideo');
            this.canvas = document.getElementById('scanCanvas');
            this.ctx = this.canvas.getContext('2d');
            
            this.startCamera();

            Livewire.on('scan-result', (params) => {
                let p = Array.isArray(params) ? params[0] : params;
                const type = p.type || 'error';
                const message = p.message || 'Unknown';
                this.showScanToast(message, type);
            });
        },

        destroy() {
            this.stopCamera();
        },

        async startCamera() {
            this.scanning = true;
            this.cameraError = false;
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment', width: { ideal: 640 }, height: { ideal: 480 } }
                });
                this.video.srcObject = this.stream;
                this.video.setAttribute('playsinline', true);
                this.video.play();
                requestAnimationFrame(() => this.tick());
            } catch (err) {
                console.error('Camera error:', err);
                this.cameraError = true;
                this.scanning = false;
            }
        },

        stopCamera() {
            this.scanning = false;
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
            if (this.video) {
                this.video.srcObject = null;
            }
        },

        tick() {
            if (!this.scanning) return;
            
            if (this.video && this.video.readyState === this.video.HAVE_ENOUGH_DATA) {
                this.canvas.height = this.video.videoHeight;
                this.canvas.width = this.video.videoWidth;
                this.ctx.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);
                const imageData = this.ctx.getImageData(0, 0, this.canvas.width, this.canvas.height);
                
                if (typeof jsQR !== 'undefined') {
                    const code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: 'dontInvert',
                    });

                    if (code) {
                        const now = Date.now();
                        if (code.data !== this.lastScanned || (now - this.lastScanTime) > 5000) {
                            this.lastScanned = code.data;
                            this.lastScanTime = now;
                            this.handleScan(code.data);
                        }
                    }
                }
            }
            requestAnimationFrame(() => this.tick());
        },

        handleScan(decodedText) {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioCtx.createOscillator();
                const gainNode = audioCtx.createGain();
                oscillator.connect(gainNode);
                gainNode.connect(audioCtx.destination);
                oscillator.frequency.value = 1200;
                oscillator.type = 'sine';
                gainNode.gain.value = 0.3;
                oscillator.start();
                setTimeout(() => { oscillator.stop(); audioCtx.close(); }, 150);
            } catch(e) {}

            $wire.processQrCode(decodedText);
        },

        showScanToast(message, type) {
            const toast = document.getElementById('scanToast');
            const toastMsg = document.getElementById('toastMessage');
            const toastIcon = document.getElementById('toastIcon');

            toast.className = 'scan-toast ' + type;
            toastMsg.textContent = message;

            if (type === 'success') {
                toastIcon.innerHTML = '<svg style=\'width:1rem;height:1rem;\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'white\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'3\' d=\'M5 13l4 4L19 7\'/></svg>';
            } else {
                toastIcon.innerHTML = '<svg style=\'width:1rem;height:1rem;\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'white\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'3\' d=\'M6 18L18 6M6 6l12 12\'/></svg>';
            }

            toast.classList.add('show');
            setTimeout(() => { toast.classList.remove('show'); }, 3500);
        }
    }">
        <div class="scan-card">
            <div class="scan-card-header">
                <h1>📷 SCAN QR CODE</h1>
                <p><?php echo e(__('Point camera at QR Code for attendance')); ?></p>
            </div>

            <!-- Camera View -->
            <div class="scan-camera-wrapper" id="cameraContainer" x-show="!cameraError">
                <video id="scanVideo" autoplay playsinline muted></video>
                <canvas id="scanCanvas" style="display: none;"></canvas>
                <div class="scan-overlay">
                    <div class="scan-frame">
                        <div class="corner tl"></div>
                        <div class="corner tr"></div>
                        <div class="corner bl"></div>
                        <div class="corner br"></div>
                    </div>
                </div>
            </div>

            <!-- Camera Error -->
            <div id="cameraError" x-show="cameraError" style="display: none;" class="camera-error">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <p><strong><?php echo e(__('Camera unavailable.')); ?></strong></p>
                <p style="margin-top: 0.5rem; color: #b91c1c;"><?php echo e(__('Make sure camera permission is enabled in your browser.')); ?></p>
            </div>

            <div class="scan-status" :class="{ 'ready': !cameraError }" id="scanStatus">
                <div class="dot"></div>
                <span x-text="cameraError ? '❌ <?php echo e(__('Camera inactive')); ?>' : '<?php echo e(__('Camera active — ready to scan')); ?>'"></span>
            </div>

            <div class="scan-instructions">
                <p><?php echo __('Point QR Code of <strong>Student, Tutor,</strong> or <strong>Employee</strong>'); ?></p>
                <p class="hint"><?php echo e(__('Camera requires access permission (Allow) on the browser.')); ?></p>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php /**PATH C:\Users\ahmad\Downloads\Laravel\g-english\resources\views/filament/pages/scan-attendance.blade.php ENDPATH**/ ?>