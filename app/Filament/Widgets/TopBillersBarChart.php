<?php

namespace App\Filament\Widgets;

use App\Models\BillPayment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Js;

class TopBillersBarChart extends ChartWidget
{
    protected static ?string $heading = 'Top Billers (Last 30 Days)';

    // Layout: show side-by-side with another chart on large screens
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
            'top_billers_' . $from->toDateString() . '_' . $to->toDateString(),
            30,
            function () use ($from, $to) {
                $rows = BillPayment::select('biller_id', DB::raw('SUM(amount) as total'))
                    ->whereBetween('created_at', [$from, $to])
                    ->groupBy('biller_id')
                    ->with('biller')
                    ->orderByDesc('total')
                    ->limit(10)
                    ->get();

                $labels = $rows->map(fn ($r) => $r->biller?->biller_name ?? ('Biller ' . $r->biller_id))->toArray();
                $data   = $rows->pluck('total')->map(fn ($v) => (float) $v)->toArray();

                return [$labels, $data];
            }
        );

        return [
            'datasets' => [[
                'label'           => 'Amount',
                'data'            => $series,
                'borderWidth'     => 0,
                'barPercentage'   => 0.7,
                'categoryPercentage' => 0.7,
            ]],
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
            'indexAxis' => 'y', // horizontal bars for readability
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'top'],
                'tooltip' => [
                    'callbacks' => [
                        'label' => Js::from(<<<'JS'
                            function (ctx) {
                              const label = ctx.dataset?.label ?? 'Amount';
                              const v = ctx.parsed?.x ?? 0;
                              return `${label}: ` + new Intl.NumberFormat().format(v);
                            }
                        JS),
                    ],
                ],
            ],
            'scales' => [
                'x' => [
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
                'y' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }
}
