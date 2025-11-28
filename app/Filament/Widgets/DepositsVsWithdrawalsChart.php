<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class DepositsVsWithdrawalsChart extends ChartWidget
{
    protected static ?string $heading = 'Cash In vs Cash Out (Last 30 Days)';

    protected int|string|array $columnSpan = [
        'default' => 12,
        'lg'      => 6,
    ];

    protected static ?string $pollingInterval = '30s';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $from = now()->subDays(29)->startOfDay();
        $to   = now()->endOfDay();

        // Generate labels for the last 30 days
        $labels = collect(range(0, 29))
            ->map(fn ($i) => now()->subDays(29 - $i)->format('M d'))
            ->toArray();

        // Initialize default empty buckets
        $days = collect(range(0, 29))
            ->mapWithKeys(fn ($i) => [
                now()->subDays(29 - $i)->toDateString() => [
                    'cash_in'  => 0,
                    'cash_out' => 0,
                ],
            ]);

        // Fetch only needed rows
        $rows = Transaction::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereIn('type', ['deposit', 'transfer_in', 'transfer_out', 'bill_payment'])
            ->get(['type', 'amount', 'created_at']);

        // Aggregate properly
        foreach ($rows as $row) {
            $date = $row->created_at->toDateString();

            if (! $days->has($date)) {
                continue;
            }

            // Pull bucket
            $bucket = $days->get($date);

            // Assign
            if (in_array($row->type, ['deposit', 'transfer_in'])) {
                $bucket['cash_in'] += (float) $row->amount;
            } else {
                $bucket['cash_out'] += (float) $row->amount;
            }

            // Save modified bucket
            $days->put($date, $bucket);
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Cash In',
                    'data'  => $days->pluck('cash_in')->values(),
                    'backgroundColor' => '#22c55e', // green
                ],
                [
                    'label' => 'Cash Out',
                    'data'  => $days->pluck('cash_out')->values(),
                    'backgroundColor' => '#ef4444', // red
                ],
            ],
        ];
    }

    // Enable stacked view (optional)
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'x' => [
                    'stacked' => true,
                ],
                'y' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
