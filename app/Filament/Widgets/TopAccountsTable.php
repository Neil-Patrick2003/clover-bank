<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AccountResource;
use App\Models\Account;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopAccountsTable extends BaseWidget
{
    protected static ?string $heading = 'Top Balances';

    /** Layout: half width on large screens */
    protected int|string|array $columnSpan = [
        'default' => 12,
        'lg'      => 6,
    ];

    /** Optional live refresh */
    protected static ?string $pollingInterval = '30s';

    public function table(Table $table): Table
    {
        $query = Account::query()
            ->with('user')
            ->where('status', '!=', 'closed')     // optional: exclude closed accounts
            ->orderByDesc('balance');

        return $table
            ->query($query)
            ->columns([
                // Rank in current page
                Tables\Columns\TextColumn::make('rank')
                    ->label('#')
                    ->rowIndex()              // Filament helper for 1..n per page
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('account_number')
                    ->label('Account')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Account number copied')
                    ->limit(22),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Customer')
                    ->searchable()
                    ->limit(28),

                Tables\Columns\TextColumn::make('currency')
                    ->label('Cur')
                    ->alignCenter()
                    ->toggleable(),

                // If you store 'currency' per account, format manually; else use ->money('php')
                Tables\Columns\TextColumn::make('balance')
                    ->label('Balance')
                    ->alignRight()
                    ->sortable()
                    ->formatStateUsing(fn ($state, Account $record) =>
                    ($record->currency ?? 'PHP') === 'PHP'
                        ? '₱' . number_format((float) $state, 2)
                        : number_format((float) $state, 2) . ' ' . ($record->currency ?? '')
                    ),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'open',
                        'warning' => 'frozen',
                        'gray'    => 'closed',
                    ])
                    ->icons([
                        'heroicon-m-lock-open'  => 'open',
                        'heroicon-m-lock-closed'=> 'frozen',
                        'heroicon-m-archive-box'=> 'closed',
                    ])
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open'   => 'Open',
                        'frozen' => 'Frozen',
                        'closed' => 'Closed',
                    ]),
                Tables\Filters\Filter::make('high_balance')
                    ->label('≥ ₱100k')
                    ->query(fn (Builder $q) => $q->where('balance', '>=', 100_000)),
            ])
            ->defaultSort('balance', 'desc')
            ->recordUrl(fn (Account $record) =>
            AccountResource::getUrl('edit', ['record' => $record])
            )
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Account $record) =>
                    AccountResource::getUrl('edit', ['record' => $record])
                    )
                    ->openUrlInNewTab(),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(10)
            ->striped()
            ->emptyStateHeading('No accounts to show')
            ->emptyStateDescription('Accounts will appear here once created and funded.')
            ->emptyStateIcon('heroicon-o-banknotes');
    }
}
