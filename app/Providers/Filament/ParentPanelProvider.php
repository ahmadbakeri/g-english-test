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
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ParentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('parent')
            ->path('parent')
            
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
            ->brandName('G-English Parent Portal')
            ->discoverResources(in: app_path('Filament/Parent/Resources'), for: 'App\Filament\Parent\Resources')
            ->discoverPages(in: app_path('Filament/Parent/Pages'), for: 'App\Filament\Parent\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Parent/Widgets'), for: 'App\Filament\Parent\Widgets')
            ->widgets([
                \App\Filament\Parent\Widgets\ParentWelcomeWidget::class,
                \App\Filament\Parent\Widgets\ParentStatsWidget::class,
                \App\Filament\Parent\Widgets\ChildAttendanceWidget::class,
                \App\Filament\Parent\Widgets\PushNotificationWidget::class,
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
