<div class="report">
    <style>
        * {
            box-sizing: border-box;
        }

        .report {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .brand {
            font-weight: bold;
            font-size: 14px;
        }

        .brand-sub {
            font-size: 10px;
            color: #666;
        }

        .report-title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-top: 4px;
        }

        .filter-label {
            text-align: center;
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }

        .meta-right {
            text-align: right;
            font-size: 9px;
            margin-top: 4px;
            color: #666;
        }

        .summary {
            margin: 8px 0 12px 0;
            padding: 6px 8px;
            border-radius: 4px;
            background: #f7f9fb;
            border: 1px solid #dde3ec;
            font-size: 10px;
        }

        .summary-row {
            display: block;
        }

        .summary-label {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        thead {
            background: #edf2f7;
        }

        th, td {
            padding: 5px 6px;
            border: 1px solid #d0d7e2;
            text-align: left;
            vertical-align: top;
        }

        th {
            font-weight: bold;
        }

        tbody tr:nth-child(even) {
            background: #fafbff;
        }

        .amount-cell {
            text-align: right;
            white-space: nowrap;
        }

        .empty-state {
            margin-top: 30px;
            padding: 20px;
            border-radius: 6px;
            border: 1px dashed #cbd5e0;
            background: #f9fafb;
            text-align: center;
            font-size: 11px;
            color: #555;
        }

        .empty-title {
            font-weight: bold;
            margin-bottom: 4px;
        }

        .empty-subtitle {
            font-size: 10px;
            color: #777;
        }
    </style>

    @php
        $totalRecords = $transactions->count();
        $totalAmount  = $transactions->sum('amount');

        $firstDate = $transactions->min('created_at');
        $lastDate  = $transactions->max('created_at');

        $dateRangeText = null;
        if ($firstDate && $lastDate) {
            $dateRangeText = $firstDate->format('Y-m-d') . ' → ' . $lastDate->format('Y-m-d');
        }
    @endphp

    {{-- HEADER --}}
    <div class="header">
        <div class="brand">
            {{-- You can change this to your bank/app name --}}
            Clover Bank
        </div>
        <div class="brand-sub">
            Digital Banking Platform
        </div>

        <div class="report-title">
            Transaction Report
        </div>

        <div class="filter-label">
            {{ $filterLabel ?? 'All transactions' }}
        </div>

        <div class="meta-right">
            Generated at: {{ now()->format('Y-m-d H:i:s') }}<br>
            @if ($dateRangeText)
                Transactions from: {{ $dateRangeText }}
            @endif
        </div>
    </div>

    {{-- SUMMARY --}}
    <div class="summary">
        <div class="summary-row">
            <span class="summary-label">Total records:</span>
            {{ $totalRecords }}
        </div>
        <div class="summary-row">
            <span class="summary-label">Total amount:</span>
            {{ number_format($totalAmount, 2) }}
        </div>
    </div>

    {{-- CONTENT --}}
    @if ($transactions->isEmpty())
        <div class="empty-state">
            <div class="empty-title">
                No transactions found
            </div>
            <div class="empty-subtitle">
                There are no records for the selected period and filters.
            </div>
        </div>
    @else
        <table>
            <thead>
            <tr>
                <th>Date / Time</th>
                <th>Account #</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Currency</th>
                <th>Reference #</th>
                <th>Status</th>
                <th>Remarks</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($transactions as $t)
                <tr>
                    <td>{{ $t->created_at?->format('Y-m-d H:i') }}</td>
                    <td>{{ $t->account?->account_number }}</td>
                    <td>{{ $t->type }}</td>
                    <td class="amount-cell">{{ number_format($t->amount, 2) }}</td>
                    <td>{{ $t->currency }}</td>
                    <td>{{ $t->reference_no }}</td>
                    <td>{{ $t->status }}</td>
                    <td>{{ $t->remarks }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
