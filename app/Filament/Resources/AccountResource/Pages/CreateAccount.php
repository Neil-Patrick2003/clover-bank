<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\Banking\AccountNumberGenerator;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class CreateAccount extends CreateRecord
{
    protected static string $resource = AccountResource::class;

    /**
     * We handle everything (account + initial deposit) in ONE transaction.
     */
    protected function handleRecordCreation(array $data): Account
    {
        // Ensure account_number is set even if the form didn't pass it
        $data['account_number'] = $data['account_number'] ?? AccountNumberGenerator::make();
        $data['balance']        = 0;
        $data['status']         = $data['status'] ?? 'open';
        $data['currency']       = $data['currency'] ?? 'PHP';

        // default account type if not provided
        $data['account_type']   = $data['account_type'] ?? 'savings';

        // initial_deposit is a form-only field (dehydrated(false) in the form)
        $initial = (float) ($this->form->getState()['initial_deposit'] ?? 0);
        if (! is_finite($initial) || $initial < 0) {
            $initial = 0;
        }

        try {
            return DB::transaction(function () use ($data, $initial) {
                /** @var Account $account */
                $account = Account::create([
                    'user_id'        => $data['user_id'],          // or auth()->id()
                    'account_number' => $data['account_number'],
                    'account_type'   => $data['account_type'],     // ✅ real column
                    'currency'       => $data['currency'],
                    'balance'        => 0,                         // will be updated below
                    'status'         => $data['status'],
                ]);

                // Post initial deposit (if any)
                if ($initial > 0) {
                    Transaction::create([
                        'account_id'   => $account->id,
                        'type'         => 'deposit',
                        'amount'       => $initial,
                        'currency'     => $account->currency,
                        'reference_no' => (string) Str::uuid(),
                        'status'       => 'posted',
                        'remarks'      => 'Initial deposit',
                    ]);

                    $account->increment('balance', $initial);
                }

                return $account;
            });
        } catch (Throwable $e) {
            Notification::make()
                ->title('Create account failed')
                ->body($e->getMessage())
                ->danger()
                ->send();

            // Re-throw so Filament knows it failed
            throw $e;
        }
    }

    protected function getCreatedNotification(): ?Notification
    {
        // Friendlier success message that also mentions the initial deposit.
        $initial = (float) ($this->form->getState()['initial_deposit'] ?? 0);

        return Notification::make()
            ->title('Account created')
            ->body($initial > 0 ? 'Initial deposit posted successfully.' : 'No initial deposit.')
            ->success();
    }
}
