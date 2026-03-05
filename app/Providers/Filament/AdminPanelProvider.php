<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Admin\Filament\Pages\Dashboard;
use App\Http\Middleware\SetLocale;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('RU')
                    ->icon('heroicon-o-language')
                    ->url(fn (): string => request()->fullUrlWithQuery(['lang' => 'ru'])),
                MenuItem::make()
                    ->label('EN')
                    ->icon('heroicon-o-language')
                    ->url(fn (): string => request()->fullUrlWithQuery(['lang' => 'en'])),
                MenuItem::make()
                    ->label('HY')
                    ->icon('heroicon-o-language')
                    ->url(fn (): string => request()->fullUrlWithQuery(['lang' => 'hy'])),
            ])
            ->discoverResources(in: app_path('Admin/Filament/Resources'), for: 'App\\Admin\\Filament\\Resources')
            ->discoverPages(in: app_path('Admin/Filament/Pages'), for: 'App\\Admin\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Admin/Filament/Widgets'), for: 'App\\Admin\\Filament\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                SetLocale::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
