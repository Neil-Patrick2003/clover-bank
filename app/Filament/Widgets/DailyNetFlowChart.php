<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Js;

class DailyNetFlowChart extends ChartWidget
{
    protected static ?string $heading = 'Daily Net Flow (Last 30 Days)';

    // Layout suggestion: half width on large screens
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

        [$labels, $series] = Cache::remember(
            'daily_net_flow_' . $from->toDateString() . '_' . $to->toDateString(),
            30,
            function () use ($from, $to) {
                $rows = Transaction::selectRaw("
                        DATE(created_at) d,
                        SUM(CASE WHEN type='deposit' THEN amount ELSE 0 END)
                      - SUM(CASE WHEN type='transfer_out' THEN amount ELSE 0 END)
                      - SUM(CASE WHEN type='bill_payment' THEN amount ELSE 0 END) net
                    ")
                    ->whereBetween('created_at', [$from, $to])
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('d')
                    ->get()
                    ->keyBy('d');

                $labels = [];
                $data   = [];
                $cursor = Carbon::parse($from);

                while ($cursor->lte($to)) {
                    $k = $cursor->toDateString();
                    $labels[] = $cursor->format('M d');
                    $data[]   = (float) ($rows[$k]->net ?? 0);
                    $cursor->addDay();
                }

                return [$labels, $data];
            }
        );

        return [
            'datasets' => [[
                'label'        => 'Net',
                'data'         => $series,
                'type'         => 'bar',
                'borderWidth'  => 0,
                'barPercentage'=> 0.7,
                'categoryPercentage' => 0.7,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        // We'll return 'bar' here, but we also set dataset type explicitly above.
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
                              const label = ctx.dataset?.label ?? 'Net';
                              const v = ctx.parsed?.y ?? 0;
                              const n = new Intl.NumberFormat().format(v);
                              return `${label}: ${n}`;
                            }
                        JS),
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                ],
                'y' => [
                    // Center around zero so positives/negatives stand out
                    'beginAtZero' => true,
                    'grid' => ['color' => 'rgba(0,0,0,0.06)'],
                    'ticks' => [
                        'callback' => Js::from(<<<'JS'
                            function (v) {
                              if (Math.abs(v) >= 1_000_000) return (Math.round(v / 100_000) / 10) + 'M';
                              if (Math.abs(v) >= 1_000)     return (Math.round(v / 100) / 10) + 'K';
                              return v;
                            }
                        JS),
                    ],
                    // Draw a bold zero line
                    'afterFit' => Js::from(<<<'JS'
                        function(scale) { /* no-op: Chart.js v4 handles grid styling */ }
                    JS),
                ],
            ],
        ];
    }
}
