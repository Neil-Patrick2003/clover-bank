<?php

namespace App\Filament\Resources\TransferResource\Pages;

use App\Filament\Resources\TransferResource;
use App\Models\Account;
use App\Models\Transaction;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Str;

class CreateTransfer extends CreateRecord
{
    protected static string $resource = TransferResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return DB::transaction(function () use ($data) {

            $from = Account::lockForUpdate()->findOrFail($data['from_account_id']);
            $to   = Account::lockForUpdate()->findOrFail($data['to_account_id']);
            $amt  = (float) $data['amount'];

            // -------------------------------
            // 1. Validate account state
            // -------------------------------
            if ($from->status !== 'open') {
                Notification::make()
                    ->title('Source account is not open')
                    ->danger()
                    ->send();

                return $this->halt();
            }

            if ($to->status !== 'open') {
                Notification::make()
                    ->title('Destination account is not open')
                    ->danger()
                    ->send();

                return $this->halt();
            }

            // -------------------------------
            // 2. Currency mismatch
            // -------------------------------
            if ($from->currency !== $to->currency) {
                Notification::make()
                    ->title('Currency mismatch')
                    ->body('Both accounts must use the same currency.')
                    ->danger()
                    ->send();

                return $this->halt();
            }

            // -------------------------------
            // 3. Amount validation
            // -------------------------------
            if ($amt <= 0) {
                Notification::make()
                    ->title('Invalid amount')
                    ->body('Amount must be greater than zero.')
                    ->danger()
                    ->send();

                return $this->halt();
            }

            if ($from->balance < $amt) {
                Notification::make()
                    ->title('Insufficient Balance')
                    ->body('The source account does not have enough funds.')
                    ->danger()
                    ->send();

                return $this->halt();
            }

            // -------------------------------
            // 4. Process transfer-out
            // -------------------------------
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

            // -------------------------------
            // 5. Process transfer-in
            // -------------------------------
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

            // -------------------------------
            // 6. Create Transfer record
            // -------------------------------
            $result = static::getModel()::create([
                'from_account_id' => $from->id,
                'to_account_id'   => $to->id,
                'amount'          => $amt,
                'currency'        => $from->currency,
                'trx_out_id'      => $trxOut->id,
                'trx_in_id'       => $trxIn->id,
            ]);

            Notification::make()
                ->title('Transfer Successful')
                ->success()
                ->send();

            return $result;
        });
    }
}
