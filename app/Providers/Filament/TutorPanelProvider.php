<?php
namespace App\Providers\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Navigation\NavigationGroup;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
class TutorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('tutor')
            ->path('tutor')
            ->brandName('G-English')
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('3rem')
            ->font('Outfit')
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
            ->favicon(asset('images/logo.png'))
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn (): string => Blade::render('@include("filament.components.theme-toggle")')
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render('@include("filament.components.animations")')
            )
            ->spa()
            ->globalSearch(false)
            ->navigationGroups([
                NavigationGroup::make('Administration')
                    ->label(fn (): string => __('Administration')),
                NavigationGroup::make('Academic')
                    ->label(fn (): string => __('Academic')),
                NavigationGroup::make('Evaluation')
                    ->label(fn (): string => __('Evaluation')),
                NavigationGroup::make('Accounting')
                    ->label(fn (): string => __('Accounting')),
                NavigationGroup::make('Reports')
                    ->label(fn (): string => __('Reports')),
                NavigationGroup::make('Master Data')
                    ->label(fn (): string => __('Master Data')),
                NavigationGroup::make('System')
                    ->label(fn (): string => __('System')),
            ])
            ->discoverResources(in: app_path('Filament/Tutor/Resources'), for: 'App\Filament\Tutor\Resources')
            ->discoverPages(in: app_path('Filament/Tutor/Pages'), for: 'App\Filament\Tutor\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Tutor/Widgets'), for: 'App\Filament\Tutor\Widgets')
            ->widgets([])
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
            ->authMiddleware([
                'auth',
            ])
            ->profile(\App\Filament\Pages\Auth\EditProfile::class);
    }
}
