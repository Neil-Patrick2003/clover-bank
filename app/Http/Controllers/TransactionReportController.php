<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionReportController extends Controller
{
    public function preview(Request $request)
    {
        $data = session()->get('pdf_report_data');

        if (!$data) {
            return redirect()->back()->with('error', 'No report data found.');
        }

        // Build query based on data
        $query = Transaction::query();

        if ($data['period'] === 'month' && isset($data['month']) && isset($data['year'])) {
            $query->whereYear('created_at', $data['year'])
                ->whereMonth('created_at', $data['month']);
        } elseif ($data['period'] === 'year' && isset($data['year'])) {
            $query->whereYear('created_at', $data['year']);
        } elseif ($data['period'] === 'custom' && isset($data['from']) && isset($data['to'])) {
            $query->whereBetween('created_at', [$data['from'], $data['to']]);
        } elseif ($data['period'] === 'current') {
            // Apply current table filters here
            // You would need to get the current filter state from session or request
        }

        if (!empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        if (!empty($data['type'])) {
            $query->where('type', $data['type']);
        }

        $transactions = $query->with('account')->latest()->get();

        $pdfData = [
            'transactions' => $transactions,
            'report_type' => $data['report_type'],
            'period' => $data['period'],
            'filters' => array_filter([
                'status' => $data['status'] ?? null,
                'type' => $data['type'] ?? null,
                'month' => $data['month'] ?? null,
                'year' => $data['year'] ?? null,
                'from' => $data['from'] ?? null,
                'to' => $data['to'] ?? null,
            ]),
            'include_columns' => $data['include_columns'],
            'include_filters' => $data['include_filters'],
        ];

        // Generate PDF for preview
        $pdf = Pdf::loadView('reports.transactions.preview', $pdfData);

        return view('reports.transactions.preview-page', [
            'pdfData' => $pdfData,
            'downloadUrl' => route('transactions.report.download', $data),
        ]);
    }

    public function download(Request $request)
    {
        $data = $request->all();

        // Build query (same logic as preview)
        $query = Transaction::query();

        if ($data['period'] === 'month' && isset($data['month']) && isset($data['year'])) {
            $query->whereYear('created_at', $data['year'])
                ->whereMonth('created_at', $data['month']);
        } elseif ($data['period'] === 'year' && isset($data['year'])) {
            $query->whereYear('created_at', $data['year']);
        } elseif ($data['period'] === 'custom' && isset($data['from']) && isset($data['to'])) {
            $query->whereBetween('created_at', [$data['from'], $data['to']]);
        } elseif ($data['period'] === 'current') {
            // Apply current table filters
        }

        if (!empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        if (!empty($data['type'])) {
            $query->where('type', $data['type']);
        }

        $transactions = $query->with('account')->latest()->get();

        $pdfData = [
            'transactions' => $transactions,
            'report_type' => $data['report_type'],
            'period' => $data['period'],
            'filters' => array_filter([
                'status' => $data['status'] ?? null,
                'type' => $data['type'] ?? null,
                'month' => $data['month'] ?? null,
                'year' => $data['year'] ?? null,
                'from' => $data['from'] ?? null,
                'to' => $data['to'] ?? null,
            ]),
            'include_columns' => $data['include_columns'],
            'include_filters' => $data['include_filters'],
        ];

        $pdf = Pdf::loadView('reports.transactions.pdf', $pdfData);

        $filename = 'transactions-report-' . date('Y-m-d-H-i-s') . '.pdf';

        return $pdf->download($filename);
    }
}
