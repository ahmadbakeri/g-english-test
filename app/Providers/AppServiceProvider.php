<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Filament\Tables\Table;
use Filament\Actions\CreateAction as PageCreateAction;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            \Filament\Http\Responses\Auth\Contracts\LoginResponse::class,
            \App\Http\Responses\LoginResponse::class
        );
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https' || str_contains(request()->header('Host', ''), 'trycloudflare.com')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Disable clickable rows globally so users must click the explicit action buttons
        Table::configureUsing(function (Table $table): void {
            $table->recordUrl(null);
            $table->recordAction(null);
        });

        Gate::before(function ($user, $ability) {
            return $user->isAdmin() ? true : null;
        });
        \App\Models\Journal::observe(\App\Observers\JournalObserver::class);

        // Override LanguageSwitchComponent for smooth SPA transition
        \Livewire\Livewire::component('language-switch-component', \App\Livewire\CustomLanguageSwitchComponent::class);

        \BezhanSalleh\LanguageSwitch\LanguageSwitch::configureUsing(function (\BezhanSalleh\LanguageSwitch\LanguageSwitch $switch) {
            $switch
                ->locales(['id', 'en', 'zh_CN', 'zh_PY', 'ja', 'ja_RM', 'ar', 'ar_RM'])
                ->userPreferredLocale(fn (): string => request()->cookie('filament_language_switch_locale') ?: 'id')
                ->labels([
                    'id' => 'Indonesia',
                    'en' => 'English',
                    'zh_CN' => 'Mandarin Simplified (Hanzi)',
                    'zh_PY' => 'Mandarin Simplified (Pinyin)',
                    'ja' => 'Japanese (Kanji)',
                    'ja_RM' => 'Japanese (Romaji)',
                    'ar' => 'Arabic (Saudi Arabia)',
                    'ar_RM' => 'Arabic (Romaji / Arabizi)'
                ])
                ->renderHook(\Filament\View\PanelsRenderHook::USER_MENU_BEFORE)
                ->circular();
        });

        // Globally translate Filament modal action buttons to use JSON translations
        \Filament\Actions\CreateAction::configureUsing(function (\Filament\Actions\CreateAction $action) {
            $action->modalSubmitActionLabel(__('Create'))
                   ->modalCancelActionLabel(__('Cancel'))
                   ->createAnotherAction(fn ($a) => $a?->label(__('Create & create another')));
        });

        // Global CSS for Hamburger, Topbar Icons, and Back Button
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::HEAD_END,
            fn (): string => '<style>
                .fi-topbar button { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important; }
                .fi-topbar .fi-icon-btn, .fi-topbar .fi-topbar-item { 
                    background-color: rgba(var(--gray-500), 0.05) !important;
                    border: 1px solid rgba(var(--gray-500), 0.1) !important;
                    border-radius: 9999px !important;
                    padding: 0.5rem !important;
                    box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;
                }
                .fi-topbar .fi-icon-btn:hover, .fi-topbar .fi-topbar-item:hover { 
                    transform: scale(1.1); 
                    background-color: rgba(var(--primary-500), 0.1) !important; 
                    border-color: rgba(var(--primary-500), 0.3) !important;
                    color: rgb(var(--primary-600)) !important;
                    box-shadow: 0 4px 10px rgba(var(--primary-500), 0.15) !important;
                }
                .fi-topbar svg { stroke-width: 2 !important; }
                
                .hide-edit-btn .filepond--action-edit-item { display: none !important; }
                
                .custom-back-btn { display: inline-flex; align-items: center; justify-content: center; width: 1.5rem; height: 1.5rem; border-radius: 50%; color: rgb(var(--gray-700)); background-color: rgba(255, 255, 255, 0.8); backdrop-filter: blur(8px); box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: 1px solid rgba(var(--gray-300), 0.5); margin-bottom: 0.5rem; }
                .dark .custom-back-btn { color: rgb(var(--gray-200)); background-color: rgba(var(--gray-800), 0.8); border-color: rgba(var(--gray-700), 0.5); }
                .custom-back-btn:hover { transform: scale(1.1) translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,0.15); color: rgb(var(--primary-600)); border-color: rgba(var(--primary-300), 0.8); background-color: rgba(255, 255, 255, 1); }
            </style>'
        );

        // Global Back Button on Create/Edit/View pages
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::PAGE_START,
            function (): string {
                $routeName = request()->route()?->getName() ?? '';
                if (Str::endsWith($routeName, ['.create', '.edit', '.view', 'journal-details'])) {
                    return \Illuminate\Support\Facades\Blade::render('
                        <button onclick="window.history.back()" class="custom-back-btn">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 20 20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        </button>
                    ');
                }
                return "";
            }
        );


        // Add close button to mobile sidebar with improved Glassmorphism
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::SIDEBAR_START,
            fn (): string => \Illuminate\Support\Facades\Blade::render('
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
            ')
        );

        // Beautify Add (Create) buttons globally
        PageCreateAction::configureUsing(function (PageCreateAction $action) {
            $action->button()->icon('heroicon-o-plus-circle')->extraAttributes(['class' => 'shadow-md hover:scale-105 transition-transform duration-300 rounded-full ring-2 ring-primary-500/50']);
        });
    }
}
