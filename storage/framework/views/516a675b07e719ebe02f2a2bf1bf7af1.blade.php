
                <style>
                    @media (min-width: 1024px) {
                        .mobile-sidebar-close-btn-container { display: none !important; }
                    }
                </style>
                <template x-teleport="body">
                    <div x-show="$store.sidebar.isOpen" class="mobile-sidebar-close-btn-container" style="position: fixed; inset: 0; z-index: 9999; pointer-events: none; transition: all 0.3s ease;">
                        <button type="button" x-on:click="$store.sidebar.close()" class="hover:scale-110 transition-all duration-300" style="position: absolute; top: 16px; right: 16px; pointer-events: auto; padding: 12px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.4); color: #333; border: 1px solid rgba(0, 0, 0, 0.1); display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                            <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </template>
            