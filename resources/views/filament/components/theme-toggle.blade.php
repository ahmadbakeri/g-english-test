<style>
    /* Sembunyikan default theme switcher yang ada di dalam menu profil */
    .fi-dropdown-list:has(.fi-theme-switcher) {
        display: none !important;
    }
</style>

<div
    x-data="{
        theme: localStorage.getItem('theme') || '{{ filament()->getDefaultThemeMode()->value }}',
        toggleTheme() {
            if (this.theme === 'dark') {
                this.theme = 'light';
            } else if (this.theme === 'light') {
                this.theme = 'dark';
            } else {
                this.theme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
            }
            localStorage.setItem('theme', this.theme);
            $dispatch('theme-changed', this.theme);
        }
    }"
    class="flex items-center"
>
    <!-- Tampilkan matahari jika dark, agar user bisa ganti ke light -->
    <x-filament::icon-button
        icon="heroicon-o-sun"
        color="gray"
        x-on:click="toggleTheme"
        x-show="theme === 'dark'"
        style="display: none;"
        title="Ganti tema ke Terang"
        class="shrink-0"
    />
    
    <!-- Tampilkan bulan jika light atau system, agar user bisa ganti ke dark -->
    <x-filament::icon-button
        icon="heroicon-o-moon"
        color="gray"
        x-on:click="toggleTheme"
        x-show="theme === 'light' || theme === 'system'"
        style="display: none;"
        title="Ganti tema ke Gelap"
        class="shrink-0"
    />
</div>
