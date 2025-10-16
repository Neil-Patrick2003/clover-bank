<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Js;

class MoneyFlowChart extends ChartWidget
{
    protected static ?string $heading = 'Money Movements (Last 30 Days)';
    protected int|string|array $columnSpan = 'full';

    /** Auto-refresh every 30s (optional, remove if you don’t want live updates) */
    protected static ?string $pollingInterval = '30s';

    /** Make the chart taller (optional) */
    protected static ?string $maxHeight = '340px';

    protected function getData(): array
    {
        $from = now()->subDays(29)->startOfDay();
        $to   = now()->endOfDay();

        // light caching to avoid re-aggregating on every request
        $cacheKey = 'money_flow_' . $from->toDateString() . '_' . $to->toDateString();
        [$labels, $dep, $trf, $bp] = Cache::remember($cacheKey, 30, function () use ($from, $to) {
            // Restrict to relevant types for a smaller scan
            $rows = Transaction::selectRaw("
                    DATE(created_at) AS d,
                    SUM(CASE WHEN type = 'deposit'       THEN amount ELSE 0 END) AS deposits,
                    SUM(CASE WHEN type = 'transfer_out'  THEN amount ELSE 0 END) AS transfers,
                    SUM(CASE WHEN type = 'bill_payment'  THEN amount ELSE 0 END) AS billpays
                ")
                ->whereBetween('created_at', [$from, $to])
                ->whereIn('type', ['deposit', 'transfer_out', 'bill_payment'])
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('d')
                ->get()
                ->keyBy('d');

            $labels = [];
            $dep = $trf = $bp = [];

            $cursor = Carbon::parse($from);
            while ($cursor->lte($to)) {
                $k = $cursor->toDateString();
                $labels[] = $cursor->format('M d');

                $r = $rows->get($k);
                $dep[] = (float) ($r->deposits  ?? 0);
                $trf[] = (float) ($r->transfers ?? 0);
                $bp[]  = (float) ($r->billpays  ?? 0);

                $cursor->addDay();
            }

            return [$labels, $dep, $trf, $bp];
        });

        return [
            'datasets' => [
                ['label' => 'Deposits',      'data' => $dep],
                ['label' => 'Transfers Out', 'data' => $trf],
                ['label' => 'Bill Payments',  'data' => $bp],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    /** Nicer look & readable axes/tooltips */

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'elements' => [
                'line'  => ['tension' => 0.4],
                'point' => ['radius' => 3],
            ],
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'top'],
                'tooltip' => [
                    'callbacks' => [
                        'label' => Js::from(<<<'JS'
                        function (ctx) {
                          const label = ctx.dataset?.label ?? 'Value';
                          const v = ctx.parsed?.y ?? 0;
                          return label + ': ' + new Intl.NumberFormat().format(v);
                        }
                    JS),
                    ],
                ],
            ],
            'scales' => [
                'x' => ['grid' => ['display' => false]],
                'y' => [
                    'grid' => ['color' => 'rgba(0,0,0,0.08)'],
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
