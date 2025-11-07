<?php

namespace App\Filament\Resources;

use App\Models\Account;
use App\Models\Transaction;
use Exception;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\DB;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationGroup = 'Banking';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function table(Table $table): Table
    {
        
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('account.account_number')->searchable(),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('amount')->money('php'),
                Tables\Columns\TextColumn::make('reference_no')->copyable(),
                Tables\Columns\BadgeColumn::make('status')->colors([
                    'success'=>'posted','warning'=>'pending','danger'=>'failed','gray'=>'reversed'
                ]),
                Tables\Columns\TextColumn::make('remarks')->limit(40),
            ])
            ->defaultSort('created_at','desc')
            ->actions([
                Action::make('reverse')
                    ->label('Reverse')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->requiresConfirmation()
                    ->visible(fn(Transaction $t) => $t->status === 'posted' && in_array($t->type, [
                            'deposit','withdrawal','transfer_in','transfer_out','bill_payment'
                        ]))
                    ->action(function (Transaction $t) {
                        DB::transaction(function () use ($t) {
                            $acc = $t->account()->lockForUpdate()->first();

                            // compute compensating effect
                            $delta = match($t->type) {
                                'deposit','transfer_in'  => -$t->amount,
                                'withdrawal','transfer_out','bill_payment' => +$t->amount,
                                default => 0,
                            };
                            if ($delta === 0) { throw new Exception('Unsupported reversal.'); }

                            // balance check (no overdraft unless allowed)
                            if ($delta < 0 && $acc->balance < abs($delta)) {
                                throw new Exception('Insufficient balance for reversal.');
                            }

                            // post compensating entry
                            $compType = in_array($t->type, ['deposit','transfer_in']) ? 'withdrawal' : 'deposit';
                            Transaction::create([
                                'account_id'   => $acc->id,
                                'type'         => $compType,
                                'amount'       => $t->amount,
                                'currency'     => $t->currency,
                                'reference_no' => (string) \Str::uuid(),
                                'status'       => 'posted',
                                'remarks'      => 'Reversal of '.$t->id,
                            ]);

                            // update balance
                            $acc->update(['balance' => $acc->balance + $delta]);
                            // mark reversed
                            $t->update(['status'=>'reversed']);
                        });
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => TransactionResource\Pages\ListTransactions::route('/'),
        ];
    }
}
