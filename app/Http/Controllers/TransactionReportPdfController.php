<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TransactionReportPdfController extends Controller
{
    public function __invoke(Request $request)
    {
        $query  = Transaction::with('account');
        $label  = 'All transactions';

        $period = $request->input('period');  // month | year | custom | null

        if ($period === 'month') {
            $year  = (int) ($request->input('year') ?? now()->year);
            $month = (int) ($request->input('month') ?? now()->month);

            $query->whereYear('created_at', $year)
                ->whereMonth('created_at', $month);

            $label = "Monthly ({$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT) . ')';
        } elseif ($period === 'year') {
            $year = (int) ($request->input('year') ?? now()->year);

            $query->whereYear('created_at', $year);

            $label = "Yearly ({$year})";
        } elseif ($period === 'custom') {
            $from = $request->input('from');
            $to   = $request->input('to');

            if ($from) {
                $query->whereDate('created_at', '>=', $from);
            }

            if ($to) {
                $query->whereDate('created_at', '<=', $to);
            }

            $label = 'Custom range';

            if ($from || $to) {
                $label .= ' ('
                    . ($from ?: '...')
                    . ' → '
                    . ($to ?: '...')
                    . ')';
            }
        }

        // Optional status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
            $label .= " | Status: {$status}";
        }

        // Optional type filter
        if ($type = $request->input('type')) {
            $query->where('type', $type);
            $label .= " | Type: {$type}";
        }

        $transactions = $query->orderByDesc('created_at')->get();

        $pdf = Pdf::loadView('reports.transactions', [
            'transactions' => $transactions,
            'filterLabel'  => $label,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('transactions-' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }
}
