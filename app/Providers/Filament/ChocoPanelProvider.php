<?php

namespace App\Providers\Filament;

use App\Filament\Resources\InvoiceResource\Widgets\IncomeExpensesChart;
use App\Filament\Widgets\DocumentsOverview;
use App\Filament\Widgets\InvoicesOverview;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use lockscreen\FilamentLockscreen\Http\Middleware\Locker;
use lockscreen\FilamentLockscreen\Http\Middleware\LockerTimer;
use lockscreen\FilamentLockscreen\Lockscreen;
use Spatie\Color\Hex;
use Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticationPlugin;
use Voltra\FilamentSvgAvatar\FilamentSvgAvatarPlugin;

class ChocoPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        FilamentColor::register([
            'brand' => Color::hex('#7f3b3c'),
        ]);

        FilamentView::registerRenderHook(
            PanelsRenderHook::STYLES_AFTER,
            static fn () => view('filament.styles')
        );

        return $panel
            ->default()
            ->id('choco')
            ->path('')
            ->login()
            ->colors([
                'primary' => FilamentColor::getColors()['brand']
            ])
            ->defaultThemeMode(ThemeMode::Light)
            ->darkMode(false)
            ->brandLogo(static fn () => view('filament.logo'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
                InvoicesOverview::class,
                // DocumentsOverview::class,
                IncomeExpensesChart::class
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                LockerTimer::class
            ])
            ->authMiddleware([
                Authenticate::class,
                Locker::class
            ])
            ->plugins([
                TwoFactorAuthenticationPlugin::make()
                    ->addTwoFactorMenuItem(),
                new Lockscreen,
                FilamentSvgAvatarPlugin::make()
                    ->backgroundColor(Hex::fromString('#7f3b3c'))
                    ->textColor(Hex::fromString('#efdcdc'))
            ]);
    }
}
