<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class DepositsVsWithdrawalsChart extends ChartWidget
{
    protected static ?string $heading = 'Cash In vs Cash Out (Last 30 Days)';

    protected function getType(): string
    {
        return 'bar';
    }

    protected int|string|array $columnSpan = [
        'default' => 12,
        'lg'      => 6,
    ];

    protected function getData(): array
    {
        $from = now()->subDays(29)->startOfDay();
        $to   = now()->endOfDay();

        // Labels
        $labels = collect(range(0, 29))
            ->map(fn ($i) => now()->subDays(29 - $i)->format('M d'))
            ->toArray();

        // Create empty buckets
        $dates = collect(range(0, 29))
            ->mapWithKeys(fn ($i) => [
                now()->subDays(29 - $i)->toDateString() => [
                    'in'  => 0,
                    'out' => 0,
                ],
            ]);

        // Get transactions
        $tx = Transaction::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereIn('type', ['deposit', 'transfer_in', 'transfer_out', 'bill_payment'])
            ->get(['type', 'amount', 'created_at']);

        // Aggregate safely (no indirect modification)
        foreach ($tx as $row) {
            $dateKey = $row->created_at->toDateString();

            if (! $dates->has($dateKey)) {
                continue;
            }

            // Get the day bucket
            $day = $dates->get($dateKey);

            if (in_array($row->type, ['deposit', 'transfer_in'])) {
                $day['in'] += (float) $row->amount;
            } else {
                $day['out'] += (float) $row->amount;
            }

            // Put back into collection
            $dates->put($dateKey, $day);
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Cash In',
                    'data'  => $dates->pluck('in')->values(),
                ],
                [
                    'label' => 'Cash Out',
                    'data'  => $dates->pluck('out')->values(),
                ],
            ],
        ];
    }
}
