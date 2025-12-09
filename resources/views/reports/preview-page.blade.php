<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Report Preview</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
        .status-posted { background-color: #10b981; color: white; }
        .status-pending { background-color: #f59e0b; color: white; }
        .status-failed { background-color: #ef4444; color: white; }
        .status-reversed { background-color: #6b7280; color: white; }
        .type-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .type-deposit { background-color: #dbeafe; color: #1e40af; }
        .type-withdrawal { background-color: #fef3c7; color: #92400e; }
        .type-transfer_in { background-color: #dcfce7; color: #166534; }
        .type-transfer_out { background-color: #fce7f3; color: #9d174d; }
        .type-bill_payment { background-color: #f3e8ff; color: #6b21a8; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
<div class="container mx-auto py-8 px-4">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-file-pdf mr-2 text-red-500"></i>
                    Transaction Report Preview
                </h1>
                <p class="text-gray-600">Preview your report before downloading. Check the details below.</p>
            </div>
            <div class="text-right">
                <div class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-lg">
                    <i class="fas fa-clock mr-2"></i>
                    Generated: {{ now()->format('M d, Y h:i A') }}
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Report Summary Card -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                Report Summary
            </h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Report Type:</span>
                    <span class="font-semibold text-blue-600">{{ ucfirst($report_type) }} Report</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Period:</span>
                    <span class="font-semibold">
                            @if($period == 'month')
                            {{ $month }} {{ $year }}
                        @elseif($period == 'year')
                            Year {{ $year }}
                        @elseif($period == 'custom')
                            {{ date('M d, Y', strtotime($from)) }} - {{ date('M d, Y', strtotime($to)) }}
                        @else
                            Current View
                        @endif
                        </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Status Filter:</span>
                    <span class="font-semibold">{{ ucfirst($status_filter) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Type Filter:</span>
                    <span class="font-semibold">{{ $type_filter ? str_replace('_', ' ', ucfirst($type_filter)) : 'All' }}</span>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-bar mr-2 text-green-500"></i>
                Statistics
            </h2>
            <div class="space-y-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-sm text-blue-600 mb-1">Total Records</div>
                    <div class="text-2xl font-bold text-blue-800">{{ number_format($total_records) }}</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-sm text-green-600 mb-1">Total Amount</div>
                    <div class="text-2xl font-bold text-green-800">₱{{ number_format($total_amount, 2) }}</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="text-sm text-purple-600 mb-1">Posted Amount</div>
                    <div class="text-2xl font-bold text-purple-800">₱{{ number_format($posted_amount, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Preview Actions Card -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-download mr-2 text-purple-500"></i>
                Download Options
            </h2>
            <div class="space-y-4">
                <div class="text-gray-600 mb-2">
                    <i class="fas fa-eye mr-2 text-yellow-500"></i>
                    Previewing first {{ min(10, $total_records) }} of {{ $total_records }} records
                </div>

                <div class="space-y-3">
                    <a href="{{ route('transactions.report.download') }}?{{ $download_params }}"
                       class="w-full block text-center bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-green-600 hover:to-green-700 transition duration-200">
                        <i class="fas fa-file-pdf mr-2"></i>
                        Download Full PDF ({{ $total_records }} records)
                    </a>

                    <a href="{{ url()->previous() }}"
                       class="w-full block text-center border border-gray-300 text-gray-700 font-semibold py-3 px-4 rounded-lg hover:bg-gray-50 transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Transactions
                    </a>

                    <button onclick="window.print()"
                            class="w-full no-print border border-blue-300 text-blue-700 font-semibold py-3 px-4 rounded-lg hover:bg-blue-50 transition duration-200">
                        <i class="fas fa-print mr-2"></i>
                        Print This Preview
                    </button>
                </div>

                <div class="pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        PDF will include {{ $include_columns ? 'all columns' : 'selected columns' }} and
                        {{ $include_filters ? 'current filters' : 'no filters' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Preview -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-list-alt mr-2 text-indigo-500"></i>
                Transactions Preview
            </h2>
            <p class="text-gray-600 text-sm mt-1">
                Showing {{ min(10, $total_records) }} sample records. Full PDF contains {{ $total_records }} records.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                            {{ $transaction->created_at->format('Y-m-d') }}<br>
                            <span class="text-gray-500 text-xs">{{ $transaction->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $transaction->account->account_number ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                <span class="type-badge type-{{ $transaction->type }}">
                                    {{ str_replace('_', ' ', ucfirst($transaction->type)) }}
                                </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold
                                {{ $transaction->type == 'deposit' || $transaction->type == 'transfer_in' ? 'text-green-600' : 'text-red-600' }}">
                            ₱{{ number_format($transaction->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                            {{ substr($transaction->reference_no, 0, 12) }}...
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold status-{{ $transaction->status }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ Str::limit($transaction->remarks, 30) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                            <div class="text-lg">No transactions found for the selected criteria</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($total_records > 10)
            <div class="p-4 bg-blue-50 border-t border-blue-100">
                <div class="flex items-center justify-center text-blue-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    Preview limited to 10 records. Full report contains {{ $total_records }} records.
                </div>
            </div>
        @endif
    </div>

    <!-- Report Configuration Summary -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-cogs mr-2 text-orange-500"></i>
            Report Configuration
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="mr-3">
                    <i class="fas fa-columns text-blue-500 text-lg"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Columns Included</div>
                    <div class="font-medium">{{ $include_columns ? 'All Table Columns' : 'Selected Columns Only' }}</div>
                </div>
            </div>
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="mr-3">
                    <i class="fas fa-filter text-green-500 text-lg"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Filters Applied</div>
                    <div class="font-medium">{{ $include_filters ? 'Current Filters Included' : 'No Filters Applied' }}</div>
                </div>
            </div>
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="mr-3">
                    <i class="fas fa-file-alt text-purple-500 text-lg"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Report Format</div>
                    <div class="font-medium">{{ ucfirst($report_type) }} Format</div>
                </div>
            </div>
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="mr-3">
                    <i class="fas fa-database text-red-500 text-lg"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Data Range</div>
                    <div class="font-medium">{{ $total_records }} records</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Actions -->
    <div class="no-print flex flex-col sm:flex-row justify-between items-center bg-white rounded-xl shadow-lg p-6">
        <div class="mb-4 sm:mb-0">
            <div class="text-sm text-gray-600">
                <i class="fas fa-check-circle text-green-500 mr-1"></i>
                Report is ready for download
            </div>
            <div class="text-xs text-gray-500 mt-1">
                File size: approximately {{ round($total_records * 0.5) }} KB
            </div>
        </div>

        <div class="flex space-x-4">
            <a href="{{ url()->previous() }}"
               class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition duration-200 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>

            <a href="{{ route('transactions.report.download') }}?{{ $download_params }}"
               class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transition duration-200 flex items-center shadow-lg">
                <i class="fas fa-download mr-2"></i>
                Download PDF Now
            </a>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="mt-6 text-center text-gray-500 text-sm">
        <i class="fas fa-lock mr-1"></i>
        Your data is secure. Reports are generated on-demand and not stored on our servers.
    </div>
</div>

<script>
    // Add some interactivity
    document.addEventListener('DOMContentLoaded', function() {
        // Add animation to download button
        const downloadBtn = document.querySelector('a[href*="download"]');
        if (downloadBtn) {
            downloadBtn.addEventListener('mouseover', function() {
                this.style.transform = 'translateY(-2px)';
            });
            downloadBtn.addEventListener('mouseout', function() {
                this.style.transform = 'translateY(0)';
            });
        }

        // Show a toast if coming back from error
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('error')) {
            alert('Error: ' + urlParams.get('error'));
        }
    });
</script>
</body>
</html>
