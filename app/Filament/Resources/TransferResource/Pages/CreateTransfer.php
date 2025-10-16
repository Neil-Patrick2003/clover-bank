<?php

namespace App\Filament\Resources\TransferResource\Pages;

use App\Filament\Resources\TransferResource;
use App\Models\Account;
use App\Models\Transaction;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Exception;
use Str;

class CreateTransfer extends CreateRecord
{
    protected static string $resource = TransferResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return DB::transaction(function () use ($data) {
            /** @var Account $from */
            $from = Account::lockForUpdate()->findOrFail($data['from_account_id']);
            /** @var Account $to */
            $to   = Account::lockForUpdate()->findOrFail($data['to_account_id']);

            if ($from->status !== 'open' || $to->status !== 'open') {
                throw new Exception('Both accounts must be open.');
            }
            if ($from->currency !== $to->currency) {
                throw new Exception('Currency mismatch.');
            }
            $amt = (float) $data['amount'];
            if ($from->balance < $amt) {
                throw new Exception('Insufficient balance.');
            }

            // out
            $trxOut = Transaction::create([
                'account_id'   => $from->id,
                'type'         => 'transfer_out',
                'amount'       => $amt,
                'currency'     => $from->currency,
                'reference_no' => (string) Str::uuid(),
                'status'       => 'posted',
                'remarks'      => 'Admin transfer out',
            ]);
            $from->decrement('balance', $amt);

            // in
            $trxIn = Transaction::create([
                'account_id'   => $to->id,
                'type'         => 'transfer_in',
                'amount'       => $amt,
                'currency'     => $to->currency,
                'reference_no' => (string) Str::uuid(),
                'status'       => 'posted',
                'remarks'      => 'Admin transfer in',
            ]);
            $to->increment('balance', $amt);

            // link
            return static::getModel()::create([
                'from_account_id' => $from->id,
                'to_account_id'   => $to->id,
                'amount'          => $amt,
                'currency'        => $from->currency,
                'trx_out_id'      => $trxOut->id,
                'trx_in_id'       => $trxIn->id,
            ]);
        });
    }
}
