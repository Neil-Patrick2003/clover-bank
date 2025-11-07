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

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;
    protected static ?string $navigationGroup = 'Banking';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')->relationship('user','email')->searchable()->required(),
            TextInput::make('account_number')
                ->label('Account #')
                ->disabled()          // read-only
                ->dehydrated()        // still save to DB
                ->unique(ignoreRecord: true)
                ->afterStateHydrated(function (TextInput $component, $state, $record) {
                    // Only on create (no record yet)
                    if (! $record && blank($state)) {
                        $component->state(AccountNumberGenerator::make());
                    }
                }),
            Forms\Components\TextInput::make('currency')->default('PHP')->required(),
            Forms\Components\TextInput::make('balance')->numeric()->disabled(),
            Forms\Components\Select::make('status')->options([
                'open'=>'Open','frozen'=>'Frozen','closed'=>'Closed'
            ])->required()->default('open'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->label('Acct#')->sortable(),
            Tables\Columns\TextColumn::make('user.email')->label('Owner')->searchable(),
            Tables\Columns\TextColumn::make('account_number')->searchable(),
            Tables\Columns\TextColumn::make('currency'),
            Tables\Columns\TextColumn::make('balance')->money('php')->sortable(),
            Tables\Columns\BadgeColumn::make('status')->colors([
                'success'=>'open','warning'=>'frozen','gray'=>'closed'
            ]),
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
                    ->form([Forms\Components\TextInput::make('amount')->numeric()->minValue(0.01)->required(),])

                    ->action(function (Account $a, array $data) {
                        DB::transaction(function () use ($a, $data) {
                            if ($a->status !== 'open') { throw new Exception('Account not open.'); }
                            $amt = (float) $data['amount'];

                            Transaction::create([
                                'account_id' => $a->id,
                                'type' => 'deposit',
                                'amount' => $amt,
                                'currency' => $a->currency,
                                'reference_no' => (string) \Str::uuid(),
                                'status' => 'posted',
                                'remarks' => 'Admin deposit',
                            ]);

                            $a->increment('balance', $amt);
                        });
                    }),
                Action::make('withdraw')
                    ->label('Withdraw')->icon('heroicon-o-arrow-up-circle')->color('warning')
                    ->form([ Forms\Components\TextInput::make('amount')->numeric()->minValue(0.01)->required() ])
                    ->action(function (Account $a, array $data) {
                        DB::transaction(function () use ($a, $data) {
                            if ($a->status !== 'open') { throw new Exception('Account not open.'); }
                            $amt = (float) $data['amount'];
                            if ($a->balance < $amt) { throw new Exception('Insufficient balance.'); }

                            Transaction::create([
                                'account_id' => $a->id,
                                'type' => 'withdrawal',
                                'amount' => $amt,
                                'currency' => $a->currency,
                                'reference_no' => (string) \Str::uuid(),
                                'status' => 'posted',
                                'remarks' => 'Admin withdrawal',
                            ]);

                            $a->decrement('balance', $amt);
                        });
                    }),

                Action::make('freeze')
                    ->visible(fn (Account $record) => $record->status === 'open')
                    ->action(fn (Account $record) => $record->update(['status' => 'frozen'])),

                Action::make('close')->visible(fn(Account $a) => $a->status!=='closed')
                    ->color('gray')->requiresConfirmation()
                    ->action(function(Account $a){
                        if ($a->balance != 0) { throw new Exception('Balance must be zero to close.'); }
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
