<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    /**
     * Get user's accounts
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $accounts = $user->accounts()->with(['transactions' => function($query) {
            $query->latest()->limit(5);
        }])->get();

        return response()->json($accounts);
    }

    /**
     * Get specific account details
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $account = $user->accounts()->with(['transactions' => function($query) {
            $query->latest()->limit(20);
        }])->findOrFail($id);

        return response()->json($account);
    }

    /**
     * Resolve account by account number, email, or username
     */
    public function resolve(Request $request)
    {
        $request->validate([
            'account_number' => 'sometimes|required|string',
            'email' => 'sometimes|required|email',
            'username' => 'sometimes|required|string',
        ]);

        $account = null;

        if ($request->has('account_number')) {
            $account = Account::where('account_number', $request->account_number)
                ->where('status', 'active')
                ->first();
        } elseif ($request->has('email')) {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $account = $user->accounts()->where('status', 'active')->first();
            }
        } elseif ($request->has('username')) {
            $user = User::where('username', $request->username)->first();
            if ($user) {
                $account = $user->accounts()->where('status', 'active')->first();
            }
        }

        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        // Return limited info for security
        return response()->json([
            'id' => $account->id,
            'account_number' => $account->account_number,
            'account_holder' => $account->user->username ?? $account->user->name,
            'bank_name' => 'Clover Bank',
            'currency' => $account->currency,
        ]);
    }

    /**
     * Get account balance
     */
    public function balance(Request $request, $id)
    {
        $user = $request->user();
        $account = $user->accounts()->findOrFail($id);

        return response()->json([
            'account_id' => $account->id,
            'account_number' => $account->account_number,
            'balance' => $account->balance,
            'currency' => $account->currency,
            'status' => $account->status,
        ]);
    }

    /**
     * Get account statement/transactions
     */
    public function statement(Request $request, $id)
    {
        $user = $request->user();
        $account = $user->accounts()->findOrFail($id);

        $request->validate([
            'limit' => 'sometimes|integer|min:1|max:100',
            'offset' => 'sometimes|integer|min:0',
            'from_date' => 'sometimes|date',
            'to_date' => 'sometimes|date|after_or_equal:from_date',
        ]);

        $query = $account->transactions()->latest();

        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);

        $transactions = $query->offset($offset)->limit($limit)->get();

        return response()->json([
            'account' => [
                'id' => $account->id,
                'account_number' => $account->account_number,
                'balance' => $account->balance,
                'currency' => $account->currency,
            ],
            'transactions' => $transactions,
            'pagination' => [
                'limit' => $limit,
                'offset' => $offset,
                'total' => $account->transactions()->count(),
            ],
        ]);
    }
}
