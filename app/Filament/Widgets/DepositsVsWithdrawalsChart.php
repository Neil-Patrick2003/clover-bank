<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Js;

class DepositsVsWithdrawalsChart extends ChartWidget
{
    protected static ?string $heading = 'Deposits vs Withdrawals (Last 30 Days)';

    // Layout: half width on large screens
    protected int|string|array $columnSpan = [
        'default' => 12,
        'lg'      => 6,
    ];

    // Optional QoL
    protected static ?string $pollingInterval = '30s';
    protected static ?string $maxHeight = '320px';

    protected function getData(): array
    {
        $from = now()->subDays(29)->startOfDay();
        $to   = now()->endOfDay();

        [$labels, $dep, $wd] = Cache::remember(
            'dep_wd_' . $from->toDateString() . '_' . $to->toDateString(),
            30,
            function () use ($from, $to) {
                $rows = Transaction::selectRaw("
                        DATE(created_at) d,
                        SUM(CASE WHEN type='deposit'    THEN amount ELSE 0 END) deposits,
                        SUM(CASE WHEN type='withdrawal' THEN amount ELSE 0 END) withdrawals
                    ")
                    ->whereBetween('created_at', [$from, $to])
                    ->whereIn('type', ['deposit', 'withdrawal'])
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('d')
                    ->get()
                    ->keyBy('d');

                $labels = []; $dep = []; $wd = [];
                $cursor = Carbon::parse($from);

                while ($cursor->lte($to)) {
                    $k = $cursor->toDateString();
                    $labels[] = $cursor->format('M d');
                    $dep[] = (float) ($rows[$k]->deposits    ?? 0);
                    $wd[]  = (float) ($rows[$k]->withdrawals ?? 0);
                    $cursor->addDay();
                }

                return [$labels, $dep, $wd];
            }
        );

        return [
            'datasets' => [
                [
                    'label' => 'Deposits',
                    'data'  => $dep,
                    'stack' => 'a',
                    'borderWidth' => 0,
                    'barPercentage' => 0.7,
                    'categoryPercentage' => 0.7,
                ],
                [
                    'label' => 'Withdrawals',
                    'data'  => $wd,
                    'stack' => 'a',
                    'borderWidth' => 0,
                    'barPercentage' => 0.7,
                    'categoryPercentage' => 0.7,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'top'],
                'tooltip' => [
                    'callbacks' => [
                        'label' => Js::from(<<<'JS'
                            function (ctx) {
                              const label = ctx.dataset?.label ?? 'Value';
                              const v = ctx.parsed?.y ?? 0;
                              return `${label}: ` + new Intl.NumberFormat().format(v);
                            }
                        JS),
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'stacked' => true,
                    'grid' => ['display' => false],
                ],
                'y' => [
                    'stacked' => true,
                    'grid' => ['color' => 'rgba(0,0,0,0.06)'],
                    'ticks' => [
                        'callback' => Js::from(<<<'JS'
                            function (v) {
                              if (v >= 1_000_000) return (Math.round(v / 100_000) / 10) + 'M';
                              if (v >= 1_000)     return (Math.round(v / 100) / 10) + 'K';
                              return v;
                            }
                        JS),
                    ],
                ],
            ],
        ];
    }
}
