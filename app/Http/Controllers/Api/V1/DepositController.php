<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DepositController extends Controller
{
    public function store(Request $req)
    {
        $data = $req->validate([
            'account_id' => ['required','integer','exists:accounts,id'],
            'amount'     => ['required','numeric','min:0.01'],
            'currency'   => ['nullable','string','size:3'],
            'remarks'    => ['nullable','string','max:255'],
        ]);

        $account = Account::lockForUpdate()->findOrFail($data['account_id']);
        abort_unless($account->user_id === $req->user()->id, 403);
        abort_if($account->status !== 'open', 422, 'Account not open');

        $currency = strtoupper($data['currency'] ?? $account->currency);
        abort_if($currency !== $account->currency, 422, 'Currency mismatch');

        $amount = round((float) $data['amount'], 2);
        abort_if($amount <= 0, 422, 'Invalid amount');

        $ref = (string) Str::uuid();

        DB::transaction(function () use ($account, $amount, $currency, $ref, $data) {
            $account->increment('balance', $amount);

            Transaction::create([
                'account_id'   => $account->id,
                'type'         => 'deposit',
                'amount'       => $amount,
                'currency'     => $currency,
                'status'       => 'posted',
                'reference_no' => $ref,
                'remarks'      => $data['remarks'] ?? 'Top-up',
            ]);
        });

        return response()->json([
            'message'      => 'Deposit posted',
            'reference_no' => $ref,
            'new_balance'  => (float) $account->fresh()->balance,
        ], 201);
    }
}
