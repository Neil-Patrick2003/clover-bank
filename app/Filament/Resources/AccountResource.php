<?php

namespace App\Filament\Resources;

use App\Models\Account;
use App\Models\Transaction;
use App\Services\Banking\AccountNumberGenerator;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;
    protected static ?string $navigationGroup = 'Banking';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->relationship('user','email')
                ->searchable()
                ->required(),

            TextInput::make('account_number')
                ->label('Account #')
                ->disabled()                 // read-only in UI
                ->dehydrated()               // still submit
                ->unique(ignoreRecord: true)
                ->afterStateHydrated(function (TextInput $component, $state, $record) {
                    if (! $record && blank($state)) {
                        $component->state(AccountNumberGenerator::make());
                    }
                })
                ->helperText('Auto-generated'),

            // Create-only "Initial Deposit" (not stored in accounts table)
            TextInput::make('initial_deposit')
                ->label('Initial Deposit')
                ->numeric()->minValue(0)->step('0.01')
                ->default(0)
                ->dehydrated(false)         // don’t save into accounts
                ->visibleOn('create')
                ->helperText('Optional. Will be posted as a deposit transaction.'),

            Forms\Components\Select::make('currency')
                ->options(['PHP' => 'PHP','USD' => 'USD','EUR' => 'EUR'])
                ->default('PHP')->required(),

            Forms\Components\TextInput::make('balance')
                ->numeric()
                ->disabled()
                ->default(0)
                ->helperText('Computed via transactions'),

            Forms\Components\Select::make('status')->options([
                'open'=>'Open','frozen'=>'Frozen','closed'=>'Closed'
            ])->required()->default('open'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->label('Acct ID')->sortable(),
            Tables\Columns\TextColumn::make('user.email')->label('Owner')->searchable(),
            Tables\Columns\TextColumn::make('account_number')->searchable()->copyable(),
            Tables\Columns\TextColumn::make('currency'),
            Tables\Columns\TextColumn::make('balance')
                ->money(fn ($record) => strtolower($record->currency ?? 'php')),


        Tables\Columns\BadgeColumn::make('status')->colors([
                'success'=>'open','warning'=>'frozen','gray'=>'closed'
            ]),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->since()->label('Opened'),
        ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'open'=>'Open','frozen'=>'Frozen','closed'=>'Closed'
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Action::make('deposit')
                    ->label('Deposit')->icon('heroicon-o-arrow-down-circle')
                    ->form([
                        Forms\Components\TextInput::make('amount')->numeric()->minValue(0.01)->step('0.01')->required(),
                        Forms\Components\TextInput::make('remarks')->maxLength(255)->default('Admin deposit'),
                    ])
                    ->action(function (Account $a, array $data) {
                        try {
                            DB::transaction(function () use ($a, $data) {
                                $acc = Account::whereKey($a->id)->lockForUpdate()->first();
                                if ($acc->status !== 'open') { throw new Exception('Account not open.'); }

                                $amt = (float) $data['amount'];

                                Transaction::create([
                                    'account_id' => $acc->id,
                                    'type' => 'deposit',
                                    'amount' => $amt,
                                    'currency' => $acc->currency,
                                    'reference_no' => (string) \Str::uuid(),
                                    'status' => 'posted',
                                    'remarks' => $data['remarks'] ?? 'Admin deposit',
                                ]);

                                $acc->increment('balance', $amt);
                            });

                            Notification::make()->title('Deposit posted')->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title('Deposit failed')->body($e->getMessage())->danger()->send();
                        }
                    }),

                Action::make('withdraw')
                    ->label('Withdraw')->icon('heroicon-o-arrow-up-circle')->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('amount')->numeric()->minValue(0.01)->step('0.01')->required(),
                        Forms\Components\TextInput::make('remarks')->maxLength(255)->default('Admin withdrawal'),
                    ])
                    ->action(function (Account $a, array $data) {
                        try {
                            DB::transaction(function () use ($a, $data) {
                                $acc = Account::whereKey($a->id)->lockForUpdate()->first();
                                if ($acc->status !== 'open') { throw new Exception('Account not open.'); }

                                $amt = (float) $data['amount'];
                                if ((float) $acc->balance < $amt) { throw new Exception('Insufficient balance.'); }

                                Transaction::create([
                                    'account_id' => $acc->id,
                                    'type' => 'withdrawal',
                                    'amount' => $amt,
                                    'currency' => $acc->currency,
                                    'reference_no' => (string) \Str::uuid(),
                                    'status' => 'posted',
                                    'remarks' => $data['remarks'] ?? 'Admin withdrawal',
                                ]);

                                $acc->decrement('balance', $amt);
                            });

                            Notification::make()->title('Withdrawal posted')->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title('Withdrawal failed')->body($e->getMessage())->danger()->send();
                        }
                    }),

                Action::make('freeze')
                    ->visible(fn (Account $r) => $r->status === 'open')
                    ->requiresConfirmation()
                    ->color('warning')
                    ->action(fn (Account $r) => $r->update(['status' => 'frozen'])),

                Action::make('close')
                    ->visible(fn(Account $a) => $a->status!=='closed')
                    ->color('gray')->requiresConfirmation()
                    ->action(function(Account $a){
                        if ((float)$a->balance !== 0.0) { throw new Exception('Balance must be zero to close.'); }
                        $a->update(['status'=>'closed']);
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AccountResource\RelationManagers\TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => AccountResource\Pages\ListAccounts::route('/'),
            'create' => AccountResource\Pages\CreateAccount::route('/create'),
            'edit'   => AccountResource\Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
