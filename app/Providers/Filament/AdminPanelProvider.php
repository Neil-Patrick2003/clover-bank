<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\MoneyFlowChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
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

            /**
             * ======================
             *  🌿 COLOR SCHEME
             * ======================
             */
            ->colors([
                'primary' => Color::hex('#10b981'), // emerald-500
                'success' => Color::hex('#059669'), // emerald-600
                'warning' => Color::hex('#f59e0b'), // amber-500
                'danger'  => Color::hex('#dc2626'), // red-600
            ])

            /**
             * ======================
             *  🧩 DISCOVERY & ROUTES
             * ======================
             */
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')

            /**
             * ======================
             *  📊 DEFAULT DASHBOARD
             * ======================
             */
            ->pages([
                Dashboard::class
            ])
            ->widgets([
                \App\Filament\Widgets\KpiOverview::class,
                MoneyFlowChart::class,
                \App\Filament\Widgets\DailyNetFlowChart::class,
                \App\Filament\Widgets\DepositsVsWithdrawalsChart::class,
                \App\Filament\Widgets\TransfersCountChart::class,
                \App\Filament\Widgets\TopBillersBarChart::class,
                \App\Filament\Widgets\ApplicationsPerDayChart::class,
                \App\Filament\Widgets\PendingApplicationsTable::class,
                \App\Filament\Widgets\TopAccountsTable::class,
            ])

            /**
             * ======================
             *  ⚙️ MIDDLEWARE STACK
             * ======================
             */
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ])

            /**
             * ======================
             *  🎨 BRAND & UI SETTINGS
             * ======================
             */
            ->brandName('Clover Bank')
            ->favicon(asset('favicon.ico'))
            ->sidebarCollapsibleOnDesktop()
            ->darkMode(true)
            ->font('Inter')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->maxContentWidth('full')
            ->spa();
    }
}
