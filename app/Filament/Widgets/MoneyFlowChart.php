<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Js;

class MoneyFlowChart extends ChartWidget
{
    protected static ?string $heading = 'Money Movements (Last 30 Days)';
    protected int|string|array $columnSpan = 'full';
    protected static ?string $pollingInterval = '30s';
    protected static ?string $maxHeight = '340px';

    protected function getData(): array
    {
        $from = now()->subDays(29)->startOfDay();
        $to   = now()->endOfDay();

        // Build the label range first
        $labels = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $labels[] = $cursor->format('M d');
            $cursor->addDay();
        }

        // Initialize daily buckets
        $days = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $days[$cursor->toDateString()] = [
                'deposit'      => 0.0,
                'transfer_in'  => 0.0,
                'transfer_out' => 0.0,
                'bill_payment' => 0.0,
            ];
            $cursor->addDay();
        }

        // Fetch only needed fields and types
        $tx = Transaction::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereIn('type', ['deposit', 'transfer_in', 'transfer_out', 'bill_payment'])
            ->get(['type','amount','created_at']);

        // Aggregate in PHP
        foreach ($tx as $row) {
            $d = $row->created_at->toDateString();
            if (isset($days[$d])) {
                $days[$d][$row->type] += (float) $row->amount;
            }
        }

        // Unpack into series
        $dep = $trfIn = $trfOut = $bp = [];
        foreach (array_keys($days) as $d) {
            $dep[]   = $days[$d]['deposit'];
            $trfIn[] = $days[$d]['transfer_in'];
            $trfOut[]= $days[$d]['transfer_out'];
            $bp[]    = $days[$d]['bill_payment'];
        }

        return [
            'datasets' => [
                ['label' => 'Deposits',       'data' => $dep],
                ['label' => 'Transfers In',   'data' => $trfIn],
                ['label' => 'Transfers Out',  'data' => $trfOut],
                ['label' => 'Bill Payments',  'data' => $bp],
            ],
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
            'elements' => [
                'line'  => ['tension' => 0.35],
                'point' => ['radius' => 2.5],
            ],
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'top'],
                'tooltip' => [
                    'callbacks' => [
                        'label' => \Illuminate\Support\Js::from(<<<'JS'
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
                'x' => ['grid' => ['display' => false]],
                'y' => [
                    'beginAtZero' => true,
                    'grid' => ['color' => 'rgba(0,0,0,0.08)'],
                    // comment the "suggestedMax" if you prefer auto-scaling
                    // 'suggestedMax' => 1000, // e.g. ₱1,000 for demo; adjust to your range
                ],
            ],
        ];
    }

}
