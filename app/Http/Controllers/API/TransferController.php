<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransferController extends Controller
{
    /**
     * Get user's transfer history
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'account_id' => 'sometimes|exists:accounts,id',
            'limit' => 'sometimes|integer|min:1|max:100',
            'offset' => 'sometimes|integer|min:0',
        ]);

        $query = Transaction::whereHas('account', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('type', 'transfer')->with(['account']);

        if ($request->has('account_id')) {
            $accountExists = $user->accounts()->where('id', $request->account_id)->exists();
            if (!$accountExists) {
                return response()->json(['message' => 'Account not found'], 404);
            }
            $query->where('account_id', $request->account_id);
        }

        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);

        $transfers = $query->latest()
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json([
            'transfers' => $transfers,
            'pagination' => [
                'limit' => $limit,
                'offset' => $offset,
                'total' => $query->count(),
            ],
        ]);
    }

    /**
     * Create a new transfer
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_number' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'remarks' => 'sometimes|string|max:255',
        ]);

        // Verify user owns the source account
        $fromAccount = $user->accounts()->findOrFail($request->from_account_id);

        // Find destination account
        $toAccount = Account::where('account_number', $request->to_account_number)
            ->where('status', 'active')
            ->first();

        if (!$toAccount) {
            throw ValidationException::withMessages([
                'to_account_number' => ['Destination account not found or inactive.'],
            ]);
        }

        // Check if trying to transfer to same account
        if ($fromAccount->id === $toAccount->id) {
            throw ValidationException::withMessages([
                'to_account_number' => ['Cannot transfer to the same account.'],
            ]);
        }

        // Check sufficient balance
        if ($fromAccount->balance < $request->amount) {
            throw ValidationException::withMessages([
                'amount' => ['Insufficient balance.'],
            ]);
        }

        DB::beginTransaction();
        try {
            // Generate reference number
            $referenceNo = 'TXF' . now()->format('YmdHis') . rand(1000, 9999);

            // Debit from source account
            $fromAccount->decrement('balance', $request->amount);
            
            $debitTransaction = Transaction::create([
                'account_id' => $fromAccount->id,
                'type' => 'transfer',
                'amount' => -$request->amount,
                'balance_after' => $fromAccount->fresh()->balance,
                'description' => "Transfer to {$toAccount->account_number}",
                'reference_no' => $referenceNo,
                'metadata' => [
                    'to_account_number' => $toAccount->account_number,
                    'to_account_holder' => $toAccount->user->username ?? $toAccount->user->name,
                    'remarks' => $request->remarks,
                ],
            ]);

            // Credit to destination account
            $toAccount->increment('balance', $request->amount);
            
            $creditTransaction = Transaction::create([
                'account_id' => $toAccount->id,
                'type' => 'transfer',
                'amount' => $request->amount,
                'balance_after' => $toAccount->fresh()->balance,
                'description' => "Transfer from {$fromAccount->account_number}",
                'reference_no' => $referenceNo,
                'metadata' => [
                    'from_account_number' => $fromAccount->account_number,
                    'from_account_holder' => $fromAccount->user->username ?? $fromAccount->user->name,
                    'remarks' => $request->remarks,
                ],
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Transfer completed successfully',
                'reference_no' => $referenceNo,
                'from_account' => [
                    'id' => $fromAccount->id,
                    'account_number' => $fromAccount->account_number,
                    'balance' => $fromAccount->fresh()->balance,
                ],
                'to_account' => [
                    'account_number' => $toAccount->account_number,
                    'account_holder' => $toAccount->user->username ?? $toAccount->user->name,
                ],
                'amount' => $request->amount,
                'transaction' => $debitTransaction->fresh(),
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Get transfer details
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $transfer = Transaction::whereHas('account', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('type', 'transfer')->with(['account'])->findOrFail($id);

        return response()->json($transfer);
    }

    /**
     * Get transfer limits and fees
     */
    public function limits(Request $request)
    {
        $user = $request->user();
        
        // This would typically come from a configuration or user's KYC level
        $limits = [
            'daily_limit' => 100000.00,
            'monthly_limit' => 500000.00,
            'per_transaction_limit' => 50000.00,
            'minimum_amount' => 1.00,
            'transfer_fee' => 0.00, // Free transfers for now
            'currency' => 'PHP',
        ];

        // Get today's transfer total
        $todayTotal = Transaction::whereHas('account', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('type', 'transfer')
          ->where('amount', '<', 0) // Only outgoing transfers
          ->whereDate('created_at', today())
          ->sum('amount');

        // Get this month's transfer total
        $monthTotal = Transaction::whereHas('account', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('type', 'transfer')
          ->where('amount', '<', 0) // Only outgoing transfers
          ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
          ->sum('amount');

        $limits['daily_used'] = abs($todayTotal);
        $limits['monthly_used'] = abs($monthTotal);
        $limits['daily_remaining'] = $limits['daily_limit'] - $limits['daily_used'];
        $limits['monthly_remaining'] = $limits['monthly_limit'] - $limits['monthly_used'];

        return response()->json($limits);
    }
}
