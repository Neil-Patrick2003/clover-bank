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
        $to   = now()->endOfDay();

        // Build 30-day label range
        $labels = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $labels[] = $cursor->format('M d');
            $cursor->addDay();
        }

        // Prepare daily net array
        $net = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $net[$cursor->toDateString()] = 0.0;
            $cursor->addDay();
        }

        // Get relevant transactions
        $tx = Transaction::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereIn('type', ['deposit', 'transfer_in', 'transfer_out', 'bill_payment'])
            ->get(['type', 'amount', 'created_at']);

        // Compute daily net:
        // (deposits + transfer_in) - (transfer_out + bill_payment)
        foreach ($tx as $t) {
            $d = $t->created_at->toDateString();
            $amt = (float) $t->amount;

            if (! isset($net[$d])) continue;

            if (in_array($t->type, ['deposit', 'transfer_in'])) {
                $net[$d] += $amt;
            } else {
                $net[$d] -= $amt;
            }
        }

        $series = array_values($net);

        return [
            'datasets' => [[
                'label' => 'Net Flow',
                'data'  => $series,
                'backgroundColor' => array_map(
                    fn ($v) => $v >= 0 ? 'rgba(34,197,94,0.8)' : 'rgba(239,68,68,0.8)',
                    $series
                ),
                'borderWidth' => 0,
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
                'legend' => ['display' => true, 'position' => 'top'],
                'tooltip' => [
                    'callbacks' => [
                        'label' => Js::from(<<<'JS'
                            function (ctx) {
                              const label = ctx.dataset?.label ?? 'Net';
                              const v = ctx.parsed?.y ?? 0;
                              const n = new Intl.NumberFormat().format(v);
                              return `${label}: ₱${n}`;
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
                ],
            ],
        ];
    }
}
