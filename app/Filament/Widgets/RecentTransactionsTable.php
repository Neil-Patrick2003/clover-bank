<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentTransactionsTable extends BaseWidget
{
    protected static ?string $heading = 'Recent Transactions';

    /** Layout: half width on large screens */
    protected int|string|array $columnSpan = [
        'default' => 12,
        'lg'      => 6,
    ];

    /** Optional: live refresh */
    protected static ?string $pollingInterval = '30s';

    public function table(Table $table): Table
    {
        $query = Transaction::query()
            ->with(['account.user'])
            ->latest('created_at');

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reference_no')
                    ->label('Ref#')
                    ->copyable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'success' => 'deposit',
                        'warning' => 'withdrawal',
                        'info'    => 'transfer_in',
                        'gray'    => 'transfer_out', // outflow
                        'primary' => 'bill_payment',
                    ])
                    ->icons([
                        'heroicon-m-arrow-down-circle' => 'deposit',
                        'heroicon-m-arrow-up-circle'   => 'withdrawal',
                        'heroicon-m-arrow-down-left'   => 'transfer_in',
                        'heroicon-m-arrow-up-right'    => 'transfer_out',
                        'heroicon-m-credit-card'       => 'bill_payment',
                    ])
                    ->sortable(),

                // If your transactions table has a `currency` column, format manually:
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->alignRight()
                    ->formatStateUsing(fn ($state, Transaction $record) =>
                    ($record->currency ?? 'PHP') === 'PHP'
                        ? '₱' . number_format((float) $state, 2)
                        : number_format((float) $state, 2) . ' ' . ($record->currency ?? '')
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('account.account_number')
                    ->label('Account')
                    ->searchable(),

                Tables\Columns\TextColumn::make('account.user.email')
                    ->label('Customer')
                    ->searchable()
                    ->limit(28),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'posted',
                        'warning' => 'pending',
                        'danger'  => 'failed',
                        'gray'    => 'reversed',
                    ])
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'deposit'       => 'Deposit',
                        'withdrawal'    => 'Withdrawal',
                        'transfer_in'   => 'Transfer In',
                        'transfer_out'  => 'Transfer Out',
                        'bill_payment'  => 'Bill Payment',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'posted'   => 'Posted',
                        'pending'  => 'Pending',
                        'failed'   => 'Failed',
                        'reversed' => 'Reversed',
                    ]),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Tables\Filters\Components\DatePicker::make('from')->label('From'),
                        Tables\Filters\Components\DatePicker::make('to')->label('To'),
                    ])
                    ->query(function (Builder $q, array $data) {
                        if ($data['from'] ?? null) {
                            $q->whereDate('created_at', '>=', $data['from']);
                        }
                        if ($data['to'] ?? null) {
                            $q->whereDate('created_at', '<=', $data['to']);
                        }
                        return $q;
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (Transaction $record) =>
                // If you have an Edit page, use: TransactionResource::getUrl('edit', ['record' => $record])
            TransactionResource::getUrl('index', ['tableSearch' => $record->reference_no])
            )
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Transaction $record) =>
                    TransactionResource::getUrl('index', ['tableSearch' => $record->reference_no])
                    )
                    ->openUrlInNewTab(),
            ])
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->striped()
            ->emptyStateHeading('No recent transactions')
            ->emptyStateDescription('New transactions will show up here as they’re posted.')
            ->emptyStateIcon('heroicon-o-document-magnifying-glass');
    }
}
