<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between" x-data="pushNotifications()">
            <div>
                <h2 class="text-lg font-bold">Notifikasi Kehadiran Anak</h2>
                <p class="text-sm text-gray-500">Aktifkan notifikasi untuk menerima laporan langsung saat anak Anda absen (Masuk/Pulang).</p>
            </div>
            
            <x-filament::button color="primary" x-show="!isSubscribed" x-on:click="subscribe">
                Aktifkan Notifikasi
            </x-filament::button>
            <x-filament::button color="success" x-show="isSubscribed" disabled>
                Notifikasi Aktif
            </x-filament::button>
        </div>
    </x-filament::section>
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pushNotifications', () => ({
                isSubscribed: false,
                vapidPublicKey: '{{ env('VAPID_PUBLIC_KEY') }}',
                
                init() {
                    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
                        return;
                    }
                    
                    navigator.serviceWorker.ready.then(registration => {
                        registration.pushManager.getSubscription().then(subscription => {
                            if (subscription) {
                                this.isSubscribed = true;
                            }
                        });
                    });
                },
                
                subscribe() {
                    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
                        alert('Browser Anda tidak mendukung Web Push Notifications.');
                        return;
                    }
                    
                    Notification.requestPermission().then(permission => {
                        if (permission === 'granted') {
                            navigator.serviceWorker.ready.then(registration => {
                                const applicationServerKey = this.urlB64ToUint8Array(this.vapidPublicKey);
                                registration.pushManager.subscribe({
                                    userVisibleOnly: true,
                                    applicationServerKey: applicationServerKey
                                }).then(subscription => {
                                    this.sendSubscriptionToServer(subscription);
                                }).catch(err => {
                                    console.error('Failed to subscribe the user: ', err);
                                });
                            });
                        }
                    });
                },
                
                sendSubscriptionToServer(subscription) {
                    fetch('/api/push/subscribe', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(subscription)
                    }).then(response => {
                        if(response.ok) {
                            this.isSubscribed = true;
                            new FilamentNotification()
                                .title('Notifikasi Berhasil Diaktifkan')
                                .success()
                                .send();
                        }
                    });
                },
                
                urlB64ToUint8Array(base64String) {
                    const padding = '='.repeat((4 - base64String.length % 4) % 4);
                    const base64 = (base64String + padding)
                        .replace(/\-/g, '+')
                        .replace(/_/g, '/');

                    const rawData = window.atob(base64);
                    const outputArray = new Uint8Array(rawData.length);

                    for (let i = 0; i < rawData.length; ++i) {
                        outputArray[i] = rawData.charCodeAt(i);
                    }
                    return outputArray;
                }
            }));
        });
    </script>
</x-filament-widgets::widget>
