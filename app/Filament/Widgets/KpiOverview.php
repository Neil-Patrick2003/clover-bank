<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class KpiOverview extends BaseWidget
{
    protected  ?string $heading = 'Today Overview';
    protected int|string|array $columnSpan = 'full';
    protected static ?string $pollingInterval = '30s'; // remove if you don't want live refresh

    protected function getStats(): array
    {
        $todayStart = Carbon::today();
        $ydayStart  = Carbon::yesterday();
        $ydayEnd    = Carbon::yesterday()->endOfDay();

        // helpers
        $sumToday = fn (string $type) =>
        (float) Transaction::where('type', $type)->where('created_at', '>=', $todayStart)->sum('amount');

        $sumYday = fn (string $type) =>
        (float) Transaction::where('type', $type)->whereBetween('created_at', [$ydayStart, $ydayEnd])->sum('amount');

        $fmtMoney = fn (float $v) => '₱' . number_format($v, 2);

        // today + deltas
        $depToday  = $sumToday('deposit');
        $depYday   = $sumYday('deposit');
        $depDelta  = $depToday - $depYday;

        $trfToday  = $sumToday('transfer_out');
        $trfYday   = $sumYday('transfer_out');
        $trfDelta  = $trfToday - $trfYday;

        $billToday = $sumToday('bill_payment');
        $billYday  = $sumYday('bill_payment');
        $billDelta = $billToday - $billYday;

        $usersToday = (int) User::where('created_at', '>=', $todayStart)->count();
        $usersYday  = (int) User::whereBetween('created_at', [$ydayStart, $ydayEnd])->count();
        $usersDelta = $usersToday - $usersYday;

        // tiny 7-day sparklines (cached 60s)
        [$depSpark, $trfSpark, $billSpark, $usrSpark] = Cache::remember('kpi_sparks_'.now()->format('YmdHi'), 60, function () {
            $from = now()->subDays(6)->startOfDay(); // last 7 days including today
            $dates = collect(range(0, 6))->map(fn ($i) => $from->copy()->addDays($i)->toDateString());

            $tx = Transaction::selectRaw("
                    DATE(created_at) d,
                    SUM(CASE WHEN type='deposit' THEN amount ELSE 0 END) dep,
                    SUM(CASE WHEN type='transfer_out' THEN amount ELSE 0 END) trf,
                    SUM(CASE WHEN type='bill_payment' THEN amount ELSE 0 END) bill
                ")
                ->where('created_at', '>=', $from)
                ->groupBy(DB::raw('DATE(created_at)'))
                ->get()
                ->keyBy('d');

            $users = User::selectRaw("DATE(created_at) d, COUNT(*) c")
                ->where('created_at', '>=', $from)
                ->groupBy(DB::raw('DATE(created_at)'))
                ->get()
                ->keyBy('d');

            $dep = []; $trf = []; $bill = []; $usr = [];
            foreach ($dates as $d) {
                $dep[]  = (float) ($tx[$d]->dep  ?? 0);
                $trf[]  = (float) ($tx[$d]->trf  ?? 0);
                $bill[] = (float) ($tx[$d]->bill ?? 0);
                $usr[]  = (int)   ($users[$d]->c ?? 0);
            }

            return [$dep, $trf, $bill, $usr];
        });

        $up   = 'heroicon-m-arrow-trending-up';
        $down = 'heroicon-m-arrow-trending-down';

        return [
            Stat::make('Deposits (Today)', $fmtMoney($depToday))
                ->description(($depDelta >= 0 ? '+' : '') . $fmtMoney($depDelta) . ' vs. yesterday')
                ->descriptionIcon($depDelta >= 0 ? $up : $down)
                ->color($depDelta >= 0 ? 'success' : 'danger')
                ->chart($depSpark),

            Stat::make('Transfers Out (Today)', $fmtMoney($trfToday))
                ->description(($trfDelta >= 0 ? '+' : '') . $fmtMoney($trfDelta) . ' vs. yesterday')
                ->descriptionIcon($trfDelta >= 0 ? $up : $down)
                // Higher transfers = outflow; treat increase as warning
                ->color($trfDelta >= 0 ? 'warning' : 'success')
                ->chart($trfSpark),

            Stat::make('Bill Payments (Today)', $fmtMoney($billToday))
                ->description(($billDelta >= 0 ? '+' : '') . $fmtMoney($billDelta) . ' vs. yesterday')
                ->descriptionIcon($billDelta >= 0 ? $up : $down)
                ->color('primary')
                ->chart($billSpark),

            Stat::make('New Customers', (string) $usersToday)
                ->description(($usersDelta >= 0 ? '+' : '') . $usersDelta . ' vs. yesterday')
                ->descriptionIcon($usersDelta >= 0 ? $up : $down)
                ->color($usersDelta >= 0 ? 'success' : 'gray')
                ->chart($usrSpark),
        ];
    }
}
