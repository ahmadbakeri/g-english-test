<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SchoolPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('school')
            ->path('school')
            ->colors([
                'primary' => Color::hex('#0a4282'),
                'secondary' => Color::hex('#fbb900'),
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            ->databaseNotifications()
            ->font('Outfit')
            ->spa()
            ->globalSearch(false)
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('3rem')
            ->brandName('G-English Brain Champion')
            ->discoverResources(in: app_path('Filament/School/Resources'), for: 'App\\Filament\\School\\Resources')
            ->discoverPages(in: app_path('Filament/School/Pages'), for: 'App\\Filament\\School\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/School/Widgets'), for: 'App\\Filament\\School\\Widgets')
            ->widgets([
                \App\Filament\School\Widgets\SchoolWelcomeWidget::class,
                \App\Filament\School\Widgets\SchoolQuotaOverviewWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): string => \Illuminate\Support\Facades\Blade::render('@include("filament.components.animations")')
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::USER_MENU_BEFORE,
                fn (): string => \Illuminate\Support\Facades\Blade::render('@include("filament.components.theme-toggle")')
            )
            ->profile(\App\Filament\Pages\Auth\EditProfile::class)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
