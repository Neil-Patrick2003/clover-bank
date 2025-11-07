<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Js;

class DepositsVsWithdrawalsChart extends ChartWidget
{
    protected static ?string $heading = 'Cash In vs Cash Out (Last 30 Days)';

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

        // Labels for each day
        $labels = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $labels[] = $cursor->format('M d');
            $cursor->addDay();
        }

        // Buckets per day
        $days = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $days[$cursor->toDateString()] = [
                'cash_in'  => 0.0, // deposit + transfer_in
                'cash_out' => 0.0, // transfer_out + bill_payment
            ];
            $cursor->addDay();
        }

        // Fetch relevant rows once
        $tx = Transaction::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereIn('type', ['deposit', 'transfer_in', 'transfer_out', 'bill_payment'])
            ->get(['type', 'amount', 'created_at']);

        // Aggregate in PHP
        foreach ($tx as $row) {
            $d = $row->created_at->toDateString();
            if (! isset($days[$d])) continue;

            $amt = (float) $row->amount;
            switch ($row->type) {
                case 'deposit':
                case 'transfer_in':
                    $days[$d]['cash_in']  += $amt;
                    break;
                case 'transfer_out':
                case 'bill_payment':
                    $days[$d]['cash_out'] += $amt;
                    break;
            }
        }

        // Build series
        $cashIn  = [];
        $cashOut = [];
        foreach (array_keys($days) as $d) {
            $cashIn[]  = $days[$d]['cash_in'];
            $cashOut[] = $days[$d]['cash_out'];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Cash In',
                    'data'  => $cashIn,
                    'stack' => 'a',
                    'borderWidth' => 0,
                    'barPercentage' => 0.7,
                    'categoryPercentage' => 0.7,
                ],
                [
                    'label' => 'Cash Out',
                    'data'  => $cashOut,
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
                              return label + ': ₱' + new Intl.NumberFormat().format(v);
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
                    'beginAtZero' => true,
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
