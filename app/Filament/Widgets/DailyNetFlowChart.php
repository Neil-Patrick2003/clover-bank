<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Js;

class DailyNetFlowChart extends ChartWidget
{
    protected static ?string $heading = 'Daily Net Flow (Last 30 Days)';

    protected int|string|array $columnSpan = [
        'default' => 12,
        'lg'      => 6,
    ];

    protected static ?string $pollingInterval = '30s';
    protected static ?string $maxHeight = '320px';

    protected function getData(): array
{
    $from = now()->subDays(29)->startOfDay();
    $to = now()->endOfDay();

    // Build date range
    $dateRange = [];
    $labels = [];
    $cursor = $from->copy();
    
    while ($cursor->lte($to)) {
        $dateString = $cursor->toDateString();
        $dateRange[$dateString] = 0.0;
        $labels[] = $cursor->format('M d');
        $cursor->addDay();
    }

    // Use database aggregation for better performance
    $dailyTotals = Transaction::query()
        ->whereBetween('created_at', [$from, $to])
        ->whereIn('type', ['deposit', 'transfer_in', 'transfer_out', 'bill_payment'])
        ->selectRaw('DATE(created_at) as date, type, SUM(amount) as total')
        ->groupBy('date', 'type')
        ->get()
        ->groupBy('date');

    // Calculate net flow
    foreach ($dailyTotals as $date => $transactions) {
        if (!isset($dateRange[$date])) continue;

        $inflows = $transactions->whereIn('type', ['deposit', 'transfer_in'])->sum('total');
        $outflows = $transactions->whereIn('type', ['transfer_out', 'bill_payment'])->sum('total');
        
        $dateRange[$date] = $inflows - $outflows;
    }

    $series = array_values($dateRange);

    return [
        'datasets' => [[
            'label' => 'Net Flow',
            'data' => $series,
            'backgroundColor' => array_map(
                fn ($v) => $v >= 0 ? 'rgba(34, 197, 94, 0.8)' : 'rgba(239, 68, 68, 0.8)',
                $series
            ),
            'borderColor' => array_map(
                fn ($v) => $v >= 0 ? 'rgb(34, 197, 94)' : 'rgb(239, 68, 68)',
                $series
            ),
            'borderWidth' => 1,
            'barPercentage' => 0.7,
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
        'plugins' => [
            'legend' => [
                'display' => true,
                'position' => 'top',
                'labels' => [
                    'usePointStyle' => true,
                    'padding' => 20,
                ]
            ],
            'tooltip' => [
                'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                'titleColor' => 'rgb(255, 255, 255)',
                'bodyColor' => 'rgb(255, 255, 255)',
                'borderColor' => 'rgba(255, 255, 255, 0.1)',
                'borderWidth' => 1,
                'callbacks' => [
                    'label' => Js::from(<<<'JS'
                        function (context) {
                            const label = context.dataset.label || 'Net Flow';
                            const value = context.parsed.y;
                            const formatted = new Intl.NumberFormat('en-PH', {
                                style: 'currency',
                                currency: 'PHP'
                            }).format(value);
                            return `${label}: ${formatted}`;
                        }
                    JS),
                ],
            ],
        ],
        'scales' => [
            'x' => [
                'grid' => ['display' => false],
                'ticks' => [
                    'maxRotation' => 0,
                    'autoSkip' => true,
                    'maxTicksLimit' => 10,
                ]
            ],
            'y' => [
                'beginAtZero' => true,
                'grid' => ['color' => 'rgba(0, 0, 0, 0.06)'],
                'ticks' => [
                    'callback' => Js::from(<<<'JS'
                        function (value) {
                            if (value === 0) return '₱0';
                            if (Math.abs(value) >= 1000000) {
                                return '₱' + (value / 1000000).toFixed(1) + 'M';
                            }
                            if (Math.abs(value) >= 1000) {
                                return '₱' + (value / 1000).toFixed(1) + 'K';
                            }
                            return '₱' + value;
                        }
                    JS),
                ],
            ],
        ],
    ];
}
}