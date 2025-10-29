<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Biller;
use App\Models\BillPayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BillController extends Controller
{
    /**
     * Get available billers
     */
    public function billers(Request $request)
    {
        $request->validate([
            'category' => 'sometimes|string',
            'search' => 'sometimes|string|max:255',
        ]);

        $query = Biller::where('status', 'active');

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('biller_name', 'like', "%{$search}%")
                  ->orWhere('biller_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $billers = $query->orderBy('biller_name')->get();

        return response()->json($billers);
    }

    /**
     * Get user's bill payment history
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'account_id' => 'sometimes|exists:accounts,id',
            'biller_id' => 'sometimes|exists:billers,id',
            'status' => 'sometimes|in:pending,completed,failed,cancelled',
            'limit' => 'sometimes|integer|min:1|max:100',
            'offset' => 'sometimes|integer|min:0',
        ]);

        $query = BillPayment::whereHas('account', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['account', 'biller']);

        if ($request->has('account_id')) {
            $accountExists = $user->accounts()->where('id', $request->account_id)->exists();
            if (!$accountExists) {
                return response()->json(['message' => 'Account not found'], 404);
            }
            $query->where('account_id', $request->account_id);
        }

        if ($request->has('biller_id')) {
            $query->where('biller_id', $request->biller_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);

        $billPayments = $query->latest()
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json([
            'bill_payments' => $billPayments,
            'pagination' => [
                'limit' => $limit,
                'offset' => $offset,
                'total' => $query->count(),
            ],
        ]);
    }

    /**
     * Create a new bill payment
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'biller_id' => 'required|exists:billers,id',
            'amount' => 'required|numeric|min:0.01',
            'reference' => 'sometimes|string|max:255',
        ]);

        // Verify user owns the account
        $account = $user->accounts()->findOrFail($request->account_id);
        
        // Get biller details
        $biller = Biller::where('id', $request->biller_id)
            ->where('status', 'active')
            ->firstOrFail();

        // Check sufficient balance
        if ($account->balance < $request->amount) {
            throw ValidationException::withMessages([
                'amount' => ['Insufficient balance.'],
            ]);
        }

        // Check biller limits if any
        if ($biller->min_amount && $request->amount < $biller->min_amount) {
            throw ValidationException::withMessages([
                'amount' => ["Minimum amount for {$biller->biller_name} is {$biller->min_amount}."],
            ]);
        }

        if ($biller->max_amount && $request->amount > $biller->max_amount) {
            throw ValidationException::withMessages([
                'amount' => ["Maximum amount for {$biller->biller_name} is {$biller->max_amount}."],
            ]);
        }

        DB::beginTransaction();
        try {
            // Generate reference number
            $referenceNo = 'BILL' . now()->format('YmdHis') . rand(1000, 9999);

            // Create bill payment record
            $billPayment = BillPayment::create([
                'account_id' => $account->id,
                'biller_id' => $biller->id,
                'amount' => $request->amount,
                'reference_no' => $referenceNo,
                'customer_reference' => $request->reference,
                'status' => 'completed', // For demo purposes, assume immediate completion
                'payment_date' => now(),
            ]);

            // Debit from account
            $account->decrement('balance', $request->amount);
            
            // Create transaction record
            $transaction = Transaction::create([
                'account_id' => $account->id,
                'type' => 'bill_payment',
                'amount' => -$request->amount,
                'balance_after' => $account->fresh()->balance,
                'description' => "Bill payment to {$biller->biller_name}",
                'reference_no' => $referenceNo,
                'metadata' => [
                    'biller_name' => $biller->biller_name,
                    'biller_code' => $biller->biller_code,
                    'customer_reference' => $request->reference,
                    'bill_payment_id' => $billPayment->id,
                ],
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Bill payment completed successfully',
                'reference_no' => $referenceNo,
                'bill_payment' => $billPayment->fresh(['biller']),
                'transaction' => $transaction,
                'account_balance' => $account->fresh()->balance,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Get bill payment details
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $billPayment = BillPayment::whereHas('account', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['account', 'biller'])->findOrFail($id);

        return response()->json($billPayment);
    }

    /**
     * Get biller categories
     */
    public function categories()
    {
        $categories = Biller::where('status', 'active')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return response()->json($categories);
    }
}
