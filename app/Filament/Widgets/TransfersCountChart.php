<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Js;

class TransfersCountChart extends ChartWidget
{
    protected static ?string $heading = 'Transfers (Count per Day)';

    // Layout: pair nicely with another chart
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
            'transfers_count_' . $from->toDateString() . '_' . $to->toDateString(),
            30,
            function () use ($from, $to) {
                $rows = Transaction::selectRaw('DATE(created_at) d, COUNT(*) c')
                    ->whereBetween('created_at', [$from, $to])
                    ->whereIn('type', ['transfer_out', 'transfer_in'])
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
                    $data[]   = (int) ($rows[$k]->c ?? 0);
                    $cursor->addDay();
                }

                return [$labels, $data];
            }
        );

        return [
            'datasets' => [[
                'label'       => 'Transfers',
                'data'        => $series,
                'fill'        => true,   // subtle area
                'borderWidth' => 2,
                'pointRadius' => 3,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'interaction' => ['mode' => 'index', 'intersect' => false],
            'elements' => [
                'line'  => ['tension' => 0.35], // smooth curve
                'point' => ['radius' => 3],
            ],
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'top'],
                'tooltip' => [
                    'callbacks' => [
                        'label' => Js::from(<<<'JS'
                            function (ctx) {
                              const label = ctx.dataset?.label ?? 'Transfers';
                              const v = ctx.parsed?.y ?? 0;
                              return `${label}: ${v}`;
                            }
                        JS),
                    ],
                ],
            ],
            'scales' => [
                'x' => ['grid' => ['display' => false]],
                'y' => [
                    'grid' => ['color' => 'rgba(0,0,0,0.06)'],
                    'ticks' => ['precision' => 0], // whole numbers
                ],
            ],
        ];
    }
}
