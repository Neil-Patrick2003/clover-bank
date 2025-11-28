<?php

namespace App\Filament\Widgets;

use App\Models\BillPayment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TopBillersBarChart extends ChartWidget
{
    protected static ?string $heading = 'Top Billers (Last 30 Days)';

    protected int|string|array $columnSpan = [
        'default' => 12,
        'lg'      => 6,
    ];

    protected static ?string $pollingInterval = '30s';
    protected static ?string $maxHeight = '320px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $from = now()->subDays(29)->startOfDay();
        $to   = now()->endOfDay();

        // Cache results for 30 seconds for performance
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

                $labels = $rows->map(
                    fn($r) => $r->biller?->biller_name ?? 'Biller ' . $r->biller_id
                )->toArray();

                $data = $rows->pluck('total')->map(fn($v) => (float) $v)->toArray();

                return [$labels, $data];
            }
        );

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Amount',
                    'data'  => $series,
                    'borderWidth' => 0,
                    'barPercentage' => 0.7,
                    'categoryPercentage' => 0.7,
                ]
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'indexAxis' => 'y', // horizontal bars for readability

            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],

                // DEFAULT TOOLTIP (no custom formatting)
                'tooltip' => [],
            ],

            'scales' => [
                'x' => [
                    'grid' => ['display' => true],
                ],

                'y' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }
}
