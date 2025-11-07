<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransferController extends Controller
{
    public function store(Request $req)
    {
        $data = $req->validate([
            'from_account_id'   => ['required','integer','exists:accounts,id'],
            'to_account_number' => ['required','string','max:32'],
            'amount'            => ['required','numeric','min:0.01'],
            'currency'          => ['nullable','string','size:3'],
            'remarks'           => ['nullable','string','max:255'],
        ]);

        $from = Account::lockForUpdate()->findOrFail($data['from_account_id']);
        abort_unless($from->user_id === $req->user()->id, 403);
        abort_if($from->status !== 'open', 422, 'Source account not open');

        $to = Account::where('account_number', $data['to_account_number'])->lockForUpdate()->first();
        abort_if(! $to, 422, 'Destination not found');
        abort_if($to->status !== 'open', 422, 'Destination account not open');

        $currency = strtoupper($data['currency'] ?? $from->currency);
        abort_if($currency !== $from->currency || $currency !== $to->currency, 422, 'Currency mismatch');

        $amount = round((float) $data['amount'], 2);
        abort_if($amount <= 0, 422, 'Invalid amount');
        abort_if($from->balance < $amount, 422, 'Insufficient balance');

        // shared transfer identifier
        $transfer_id = (string) Str::uuid();

        DB::transaction(function () use ($from, $to, $amount, $currency, $transfer_id, $data) {
            // debit
            $from->decrement('balance', $amount);
            Transaction::create([
                'account_id'   => $from->id,
                'type'         => 'transfer_out',
                'amount'       => $amount,
                'currency'     => $currency,
                'status'       => 'posted',
                'reference_no' => (string) Str::uuid(),
                'remarks'      => $data['remarks'] ?? 'Transfer out',
                'meta'         => ['transfer_id' => $transfer_id],
            ]);

            // credit
            $to->increment('balance', $amount);
            Transaction::create([
                'account_id'   => $to->id,
                'type'         => 'transfer_in',
                'amount'       => $amount,
                'currency'     => $currency,
                'status'       => 'posted',
                'reference_no' => (string) Str::uuid(),
                'remarks'      => $data['remarks'] ?? 'Transfer in',
                'meta'         => ['transfer_id' => $transfer_id],
            ]);
        });

        return response()->json([
            'message'      => 'Transfer posted',
            'transfer_id'  => $transfer_id,
        ], 201);
    }

}
