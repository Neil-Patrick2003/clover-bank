<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Get user's transaction history
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'account_id' => 'sometimes|exists:accounts,id',
            'type' => 'sometimes|in:credit,debit,transfer,deposit,withdrawal,bill_payment',
            'limit' => 'sometimes|integer|min:1|max:100',
            'offset' => 'sometimes|integer|min:0',
            'from_date' => 'sometimes|date',
            'to_date' => 'sometimes|date|after_or_equal:from_date',
        ]);

        $query = Transaction::whereHas('account', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['account']);

        // Filter by account if specified
        if ($request->has('account_id')) {
            // Verify user owns this account
            $accountExists = $user->accounts()->where('id', $request->account_id)->exists();
            if (!$accountExists) {
                return response()->json(['message' => 'Account not found'], 404);
            }
            $query->where('account_id', $request->account_id);
        }

        // Filter by transaction type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);

        $transactions = $query->latest()
            ->offset($offset)
            ->limit($limit)
            ->get();

        $total = $query->count();

        return response()->json([
            'transactions' => $transactions,
            'pagination' => [
                'limit' => $limit,
                'offset' => $offset,
                'total' => $total,
            ],
        ]);
    }

    /**
     * Get specific transaction details
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $transaction = Transaction::whereHas('account', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['account'])->findOrFail($id);

        return response()->json($transaction);
    }

    /**
     * Get transaction summary/statistics
     */
    public function summary(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'account_id' => 'sometimes|exists:accounts,id',
            'period' => 'sometimes|in:today,week,month,year',
            'from_date' => 'sometimes|date',
            'to_date' => 'sometimes|date|after_or_equal:from_date',
        ]);

        $query = Transaction::whereHas('account', function($q) use ($user) {
            $q->where('user_id', $user->id);
        });

        // Filter by account if specified
        if ($request->has('account_id')) {
            $accountExists = $user->accounts()->where('id', $request->account_id)->exists();
            if (!$accountExists) {
                return response()->json(['message' => 'Account not found'], 404);
            }
            $query->where('account_id', $request->account_id);
        }

        // Filter by period
        if ($request->has('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
                case 'year':
                    $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
                    break;
            }
        } elseif ($request->has('from_date') || $request->has('to_date')) {
            if ($request->has('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->has('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }
        }

        $summary = [
            'total_transactions' => $query->count(),
            'total_credits' => $query->where('type', 'credit')->sum('amount'),
            'total_debits' => $query->where('type', 'debit')->sum('amount'),
            'total_transfers_sent' => $query->where('type', 'transfer')->where('amount', '<', 0)->sum('amount'),
            'total_transfers_received' => $query->where('type', 'transfer')->where('amount', '>', 0)->sum('amount'),
            'total_bill_payments' => $query->where('type', 'bill_payment')->sum('amount'),
            'by_type' => $query->select('type', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
                ->groupBy('type')
                ->get(),
        ];

        return response()->json($summary);
    }
}
