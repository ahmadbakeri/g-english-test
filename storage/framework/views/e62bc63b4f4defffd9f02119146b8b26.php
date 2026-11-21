<style>
    /* Sembunyikan default theme switcher yang ada di dalam menu profil */
    .fi-dropdown-list:has(.fi-theme-switcher) {
        display: none !important;
    }
</style>

<div
    x-data="{
        theme: localStorage.getItem('theme') || '<?php echo e(filament()->getDefaultThemeMode()->value); ?>',
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
    <?php if (isset($component)) { $__componentOriginalf0029cce6d19fd6d472097ff06a800a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf0029cce6d19fd6d472097ff06a800a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon-button','data' => ['icon' => 'heroicon-o-sun','color' => 'gray','xOn:click' => 'toggleTheme','xShow' => 'theme === \'dark\'','style' => 'display: none;','title' => 'Ganti tema ke Terang','class' => 'shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-sun','color' => 'gray','x-on:click' => 'toggleTheme','x-show' => 'theme === \'dark\'','style' => 'display: none;','title' => 'Ganti tema ke Terang','class' => 'shrink-0']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf0029cce6d19fd6d472097ff06a800a1)): ?>
<?php $attributes = $__attributesOriginalf0029cce6d19fd6d472097ff06a800a1; ?>
<?php unset($__attributesOriginalf0029cce6d19fd6d472097ff06a800a1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf0029cce6d19fd6d472097ff06a800a1)): ?>
<?php $component = $__componentOriginalf0029cce6d19fd6d472097ff06a800a1; ?>
<?php unset($__componentOriginalf0029cce6d19fd6d472097ff06a800a1); ?>
<?php endif; ?>
    
    <!-- Tampilkan bulan jika light atau system, agar user bisa ganti ke dark -->
    <?php if (isset($component)) { $__componentOriginalf0029cce6d19fd6d472097ff06a800a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf0029cce6d19fd6d472097ff06a800a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon-button','data' => ['icon' => 'heroicon-o-moon','color' => 'gray','xOn:click' => 'toggleTheme','xShow' => 'theme === \'light\' || theme === \'system\'','style' => 'display: none;','title' => 'Ganti tema ke Gelap','class' => 'shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-moon','color' => 'gray','x-on:click' => 'toggleTheme','x-show' => 'theme === \'light\' || theme === \'system\'','style' => 'display: none;','title' => 'Ganti tema ke Gelap','class' => 'shrink-0']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf0029cce6d19fd6d472097ff06a800a1)): ?>
<?php $attributes = $__attributesOriginalf0029cce6d19fd6d472097ff06a800a1; ?>
<?php unset($__attributesOriginalf0029cce6d19fd6d472097ff06a800a1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf0029cce6d19fd6d472097ff06a800a1)): ?>
<?php $component = $__componentOriginalf0029cce6d19fd6d472097ff06a800a1; ?>
<?php unset($__componentOriginalf0029cce6d19fd6d472097ff06a800a1); ?>
<?php endif; ?>
</div>
<?php /**PATH C:\Users\ahmad\Downloads\Laravel\g-english\resources\views/filament/components/theme-toggle.blade.php ENDPATH**/ ?>