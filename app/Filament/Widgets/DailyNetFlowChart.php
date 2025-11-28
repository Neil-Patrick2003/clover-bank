<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class DailyNetFlowChart extends ChartWidget
{
    protected static ?string $heading = 'Daily Net Flow (Last 30 Days)';
    protected static ?string $pollingInterval = '30s';
    protected static ?string $maxHeight = '320px';

    protected int|string|array $columnSpan = [
        'default' => 12,
        'lg'      => 6,
    ];

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $from = now()->subDays(29)->startOfDay();
        $to   = now()->endOfDay();

        // Build date list + labels
        $dateRange = [];
        $labels = [];

        for ($i = 0; $i < 30; $i++) {
            $day = now()->subDays(29 - $i)->toDateString();
            $dateRange[$day] = 0.0;
            $labels[] = now()->subDays(29 - $i)->format('M d');
        }

        // SQL aggregation (fast)
        $daily = Transaction::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereIn('type', ['deposit', 'transfer_in', 'transfer_out', 'bill_payment'])
            ->selectRaw('DATE(created_at) as date, type, SUM(amount) as total')
            ->groupBy('date', 'type')
            ->get()
            ->groupBy('date');

        // Calculate net flow
        foreach ($daily as $date => $rows) {
            if (!isset($dateRange[$date])) continue;

            $in  = $rows->whereIn('type', ['deposit', 'transfer_in'])->sum('total');
            $out = $rows->whereIn('type', ['transfer_out', 'bill_payment'])->sum('total');

            $dateRange[$date] = $in - $out;
        }

        $series = array_values($dateRange);

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Net Flow',
                'data' => $series,

                // Auto color bars
                'backgroundColor' => array_map(
                    fn($v) => $v >= 0 ? 'rgba(34, 197, 94, 0.8)' : 'rgba(239, 68, 68, 0.8)',
                    $series
                ),
                'borderColor' => array_map(
                    fn($v) => $v >= 0 ? 'rgb(34, 197, 94)' : 'rgb(239, 68, 68)',
                    $series
                ),

                'borderWidth' => 1,
                'barPercentage' => 0.75,
                'categoryPercentage' => 0.75,
            ]]
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,

            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                // ❗ DEFAULT TOOLTIP (no callbacks)
                'tooltip' => [],
            ],

            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                    'ticks' => [
                        'maxRotation' => 0,
                        'autoSkip' => true,
                        'maxTicksLimit' => 10,
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'grid' => ['color' => 'rgba(0, 0, 0, 0.06)'],
                ],
            ],
        ];
    }
}
