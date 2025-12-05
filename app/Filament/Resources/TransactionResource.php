<?php

namespace App\Filament\Resources;

use App\Models\Transaction;
use Exception;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
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
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('account.account_number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->badge(),

                Tables\Columns\TextColumn::make('amount')
                    ->money('php'),

                Tables\Columns\TextColumn::make('reference_no')
                    ->copyable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'posted',
                        'warning' => 'pending',
                        'danger'  => 'failed',
                        'gray'    => 'reversed',
                    ]),

                Tables\Columns\TextColumn::make('remarks')
                    ->limit(40),
            ])
            ->defaultSort('created_at', 'desc')

            // 🔽 HEADER ACTION: Download PDF with filters
            ->headerActions([
                Action::make('download_pdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->form([
                        Forms\Components\Select::make('period')
                            ->label('Period')
                            ->options([
                                'month'  => 'Monthly',
                                'year'   => 'Yearly',
                                'custom' => 'Custom range',
                            ])
                            ->default('month')
                            ->required()
                            ->live(), // ✅ important so other fields react when this changes

                        Forms\Components\Select::make('month')
                            ->label('Month')
                            ->options([
                                1  => 'January',
                                2  => 'February',
                                3  => 'March',
                                4  => 'April',
                                5  => 'May',
                                6  => 'June',
                                7  => 'July',
                                8  => 'August',
                                9  => 'September',
                                10 => 'October',
                                11 => 'November',
                                12 => 'December',
                            ])
                            ->default(now()->month)
                            ->visible(fn (Get $get) => $get('period') === 'month'),

                        Forms\Components\TextInput::make('year')
                            ->label('Year')
                            ->numeric()
                            ->default(now()->year)
                            ->required()
                            ->visible(fn (Get $get) => in_array($get('period'), ['month', 'year'])),

                        Forms\Components\DatePicker::make('from')
                            ->label('From date')
                            ->visible(fn (Get $get) => $get('period') === 'custom'),

                        Forms\Components\DatePicker::make('to')
                            ->label('To date')
                            ->visible(fn (Get $get) => $get('period') === 'custom'),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'posted'   => 'Posted',
                                'pending'  => 'Pending',
                                'failed'   => 'Failed',
                                'reversed' => 'Reversed',
                            ])
                            ->native(false)
                            ->placeholder('All'),

                        Forms\Components\Select::make('type')
                            ->label('Type')
                            ->options([
                                'deposit'       => 'Deposit',
                                'withdrawal'    => 'Withdrawal',
                                'transfer_in'   => 'Transfer In',
                                'transfer_out'  => 'Transfer Out',
                                'bill_payment'  => 'Bill Payment',
                            ])
                            ->native(false)
                            ->placeholder('All'),
                    ])
                    ->action(function (array $data) {
                        // Convert date objects (Carbon) or strings to Y-m-d
                        $fromRaw = $data['from'] ?? null;
                        $toRaw   = $data['to'] ?? null;

                        $from = null;
                        if ($fromRaw) {
                            $from = is_string($fromRaw)
                                ? $fromRaw
                                : $fromRaw->format('Y-m-d');
                        }

                        $to = null;
                        if ($toRaw) {
                            $to = is_string($toRaw)
                                ? $toRaw
                                : $toRaw->format('Y-m-d');
                        }

                        $params = array_filter([
                            'period' => $data['period'] ?? null,
                            'month'  => $data['month'] ?? null,
                            'year'   => $data['year'] ?? null,
                            'from'   => $from,
                            'to'     => $to,
                            'status' => $data['status'] ?? null,
                            'type'   => $data['type'] ?? null,
                        ], fn ($value) => ! is_null($value) && $value !== '');

                        return redirect()->route('transactions.report.pdf', $params);
                    }),
            ])

            ->actions([
                Action::make('reverse')
                    ->label('Reverse')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->requiresConfirmation()
                    ->visible(
                        fn (Transaction $t) => $t->status === 'posted'
                            && in_array($t->type, [
                                'deposit',
                                'withdrawal',
                                'transfer_in',
                                'transfer_out',
                                'bill_payment',
                            ])
                    )
                    ->action(function (Transaction $t) {
                        DB::transaction(function () use ($t) {
                            $acc = $t->account()->lockForUpdate()->first();

                            // compute compensating effect
                            $delta = match ($t->type) {
                                'deposit', 'transfer_in' => -$t->amount,
                                'withdrawal', 'transfer_out', 'bill_payment' => +$t->amount,
                                default => 0,
                            };

                            if ($delta === 0) {
                                throw new Exception('Unsupported reversal.');
                            }

                            // balance check (no overdraft unless allowed)
                            if ($delta < 0 && $acc->balance < abs($delta)) {
                                throw new Exception('Insufficient balance for reversal.');
                            }

                            // post compensating entry
                            $compType = in_array($t->type, ['deposit', 'transfer_in'])
                                ? 'withdrawal'
                                : 'deposit';

                            Transaction::create([
                                'account_id'   => $acc->id,
                                'type'         => $compType,
                                'amount'       => $t->amount,
                                'currency'     => $t->currency,
                                'reference_no' => (string) \Str::uuid(),
                                'status'       => 'posted',
                                'remarks'      => 'Reversal of ' . $t->id,
                            ]);

                            // update balance
                            $acc->update(['balance' => $acc->balance + $delta]);

                            // mark reversed
                            $t->update(['status' => 'reversed']);
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
