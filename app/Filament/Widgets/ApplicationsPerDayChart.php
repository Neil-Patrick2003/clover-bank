<?php

namespace App\Filament\Widgets;

use App\Models\CustomerApplication;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Js;

class ApplicationsPerDayChart extends ChartWidget
{
    protected static ?string $heading = 'Applications Per Day (Last 30 Days)';

    // Layout: show two charts per row on large screens
    protected int|string|array $columnSpan = [
        'default' => 12,
        'lg'      => 6,
    ];

    // Optional quality-of-life
    protected static ?string $pollingInterval = '30s';
    protected static ?string $maxHeight = '320px';

    protected function getData(): array
    {
        $from = now()->subDays(29)->startOfDay();
        $to   = now()->endOfDay();

        [$labels, $series] = Cache::remember(
            'apps_per_day_' . $from->toDateString() . '_' . $to->toDateString(),
            30,
            function () use ($from, $to) {
                $rows = CustomerApplication::selectRaw('DATE(created_at) d, COUNT(*) c')
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
                    $data[]   = (int) ($rows[$k]->c ?? 0);
                    $cursor->addDay();
                }

                return [$labels, $data];
            }
        );

        return [
            'datasets' => [[
                'label'       => 'Applications',
                'data'        => $series,
                'fill'        => true,   // subtle area fill
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
                'line'  => ['tension' => 0.35],     // smooth curve
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
                    'ticks' => [
                        'precision' => 0, // whole numbers (counts)
                    ],
                ],
            ],
        ];
    }
}
