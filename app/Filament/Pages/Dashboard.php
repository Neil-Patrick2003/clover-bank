<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\MoneyFlowChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    /** Show KPI bar at the very top */
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\KpiOverview::class,
        ];
    }

    /** Main dashboard widgets (charts & tables) */
    public function getWidgets(): array
    {
        return [
            MoneyFlowChart::class,
             \App\Filament\Widgets\DailyNetFlowChart::class,
            \App\Filament\Widgets\DepositsVsWithdrawalsChart::class,
            \App\Filament\Widgets\TransfersCountChart::class,
            \App\Filament\Widgets\ApplicationsPerDayChart::class,
            \App\Filament\Widgets\TopBillersBarChart::class,
            \App\Filament\Widgets\PendingApplicationsTable::class,
            \App\Filament\Widgets\TopAccountsTable::class,
            // \App\Filament\Widgets\RecentTransactionsTable::class, // add if you want
        ];
    }

    /** 12-column responsive grid for nice layout */
    public function getColumns(): int|string|array
    {
        return [
            'default' => 12,
            'lg'      => 12,
            '2xl'     => 12,
        ];
    }
}
