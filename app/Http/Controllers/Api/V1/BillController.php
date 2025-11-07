<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    /**
     * GET /api/v1/billers
     * Returns active billers.
     */
    public function billers(Request $req)
    {
        return Bill::query()
            ->where('status', 'active')
            ->orderBy('biller_name')
            ->get(['id', 'biller_code', 'biller_name', 'status']);
    }

    /**
     * POST /api/v1/bill-payments
     * Body: account_id, biller_id, amount, (optional) currency, (optional) reference
     */
    public function pay(Request $req)
    {
        $data = $req->validate([
            'account_id' => ['required','integer','exists:accounts,id'],
            'biller_id'  => ['required','integer','exists:bills,id'],
            'amount'     => ['required','numeric','min:0.01'],
            'currency'   => ['nullable','string','size:3'],
            'reference'  => ['nullable','string','max:160'],
        ]);

        $result = DB::transaction(function () use ($req, $data) {
            /** @var \App\Models\Account $account */
            $account = Account::query()
                ->whereKey($data['account_id'])
                ->where('user_id', $req->user()->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_if($account->status !== 'open', 422, 'Account not open');

            /** @var \App\Models\Bill|null $biller */
            $biller = Bill::query()
                ->whereKey($data['biller_id'])
                ->where('status', 'active')
                ->first();

            abort_if(! $biller, 422, 'Biller not active');

            $currency = strtoupper($data['currency'] ?? $account->currency);
            abort_if($currency !== $account->currency, 422, 'Currency mismatch');

            $amount = round((float) $data['amount'], 2);
            abort_if($amount <= 0, 422, 'Invalid amount');
            abort_if($account->balance < $amount, 422, 'Insufficient balance');

            // single reference number
            $ref = Transaction::generateRef('BIL');


            // debit the account
            $account->decrement('balance', $amount);
            $account->refresh();

            // transaction ledger (let model defaults set status; or set explicitly)
            $tx = Transaction::create([
                'account_id'   => $account->id,
                'type'         => Transaction::TYPE_BILL_PAYMENT,
                'amount'       => $amount,
                'currency'     => $currency,
                'status'       => Transaction::STATUS_POSTED,
                'reference_no' => $ref,
                'remarks'      => $data['reference'] ?? ('Payment to '.$biller->biller_name),
            ]);

            // bill payment record
            BillPayment::create([
                'account_id'     => $account->id,
                'biller_id'      => $biller->id,
                'amount'         => $amount,
                'currency'       => $currency,                  // include if your column exists / is NOT NULL
                'reference'      => $data['reference'] ?? null, // user memo/description
                'reference_no'   => $tx->reference_no,          // <-- ADD THIS
                'status'         => 'posted',
                'transaction_id' => $tx->id,
            ]);

            return [
                'reference_no' => $ref,
                'new_balance'  => (float) $account->balance,
                'biller'       => [
                    'id'   => $biller->id,
                    'name' => $biller->biller_name,
                    'code' => $biller->biller_code,
                ],
            ];
        });

        return response()->json([
            'message'      => 'Bill paid',
            'reference_no' => $result['reference_no'],
            'new_balance'  => $result['new_balance'],
            'biller'       => $result['biller'],
        ], 201);
    }


}
