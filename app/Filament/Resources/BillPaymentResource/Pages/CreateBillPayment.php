<?php

namespace App\Filament\Resources\BillPaymentResource\Pages;

use App\Filament\Resources\BillPaymentResource;
use App\Models\Account;
use App\Models\BillPayment;
use App\Models\Transaction;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Exception;
use Str;

class CreateBillPayment extends CreateRecord
{
    protected static string $resource = BillPaymentResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return DB::transaction(function () use ($data) {
            $account = Account::lockForUpdate()->findOrFail($data['account_id']);
            if ($account->status !== 'open') throw new Exception('Account not open.');
            $amt = (float) $data['amount'];
            if ($account->balance < $amt) throw new Exception('Insufficient balance.');

            // transaction row
            $trx = Transaction::create([
                'account_id'   => $account->id,
                'type'         => 'bill_payment',
                'amount'       => $amt,
                'currency'     => $account->currency,
                'reference_no' => (string) Str::uuid(),
                'status'       => 'posted',
                'remarks'      => 'Admin bill payment',
            ]);

            $account->decrement('balance', $amt);

            // bill_payments row
            return BillPayment::create([
                'account_id'   => $account->id,
                'biller_id'    => $data['biller_id'],
                'amount'       => $amt,
                'reference_no' => (string) Str::uuid(),
                'status'       => 'posted',
                'transaction_id' => $trx->id,
            ]);
        });
    }
}
