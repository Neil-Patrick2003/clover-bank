<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountResource\Pages;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Banking\AccountNumberGenerator;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;
    protected static ?string $navigationGroup = 'Banking';
    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form->schema([
            /* ---------------- Owner & Type ---------------- */
            Section::make('Owner & Type')
                ->description('Choose the owner and the kind of bank account to open.')
                ->schema([
                    // Owner
                    Forms\Components\Select::make('user_id')
                        ->label('Account Owner')
                        ->placeholder('Select an owner')
                        ->searchable()
                        ->searchPrompt('Search by email, username, or name…')
                        ->noSearchResultsMessage('No matching users')
                        ->loadingMessage('Loading users…')
                        ->getSearchResultsUsing(function (string $query) {
                            return User::query()
                                ->where(fn ($q) => $q
                                    ->where('email', 'like', "%{$query}%")
                                    ->orWhere('username', 'like', "%{$query}%")
                                    ->orWhere('username', 'like', "%{$query}%")
                                )
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(function ($u) {
                                    $label = trim(($u->name ?? $u->username ?? 'User') . ' <' . $u->email . '>');
                                    return [$u->id => $label];
                                })
                                ->toArray();
                        })
                        ->getOptionLabelUsing(function ($value) {
                            $u = User::find($value);
                            if (!$u) return null;
                            return trim(($u->name ?? $u->username ?? 'User') . ' <' . $u->email . '>');
                        })
                        ->required()
                        ->columnSpanFull(),

                    // Account Type
                    ToggleButtons::make('account_type')
                        ->label('Account Type')
                        ->options([
                            'savings'  => 'Savings',
                            'checking' => 'Checking',
                            'time'     => 'Time Deposit',
                        ])
                        ->icons([
                            'savings'  => 'heroicon-m-banknotes',
                            'checking' => 'heroicon-m-building-library',
                            'time'     => 'heroicon-m-clock',
                        ])
                        ->colors([
                            'savings'  => 'success',
                            'checking' => 'info',
                            'time'     => 'warning',
                        ])
                        ->inline()
                        ->default('savings')
                        ->required(),
                ])
                ->columns(2)
                ->compact(),

            /* ---------------- Account Details ---------------- */
            Section::make('Account Details')
                ->description('Identifiers and currency settings.')
                ->schema([
                    TextInput::make('account_number')
                        ->label('Account #')
                        ->placeholder('Auto-generated')
                        ->disabled()      // read-only in UI
                        ->dehydrated()    // still submit to save
                        ->unique(ignoreRecord: true)
                        ->afterStateHydrated(function (TextInput $component, $state, $record) {
                            if (!$record && blank($state)) {
                                $component->state(AccountNumberGenerator::make());
                            }
                        })
                        ->helperText('Created automatically on save'),

                    Forms\Components\Select::make('currency')
                        ->label('Currency')
                        ->placeholder('Select currency')
                        ->options(['PHP' => 'PHP', 'USD' => 'USD', 'EUR' => 'EUR'])
                        ->default('PHP')
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->placeholder('Select status')
                        ->options([
                            'open'   => 'Open',
                            'frozen' => 'Frozen',
                            'closed' => 'Closed',
                        ])
                        ->default('open')
                        ->required(),
                ])
                ->columns(3)
                ->compact(),

            /* ---------------- Funding ---------------- */
            Section::make('Funding')
                ->description('Starting amount; will be posted as a deposit.')
                ->schema([
                    TextInput::make('initial_deposit')
                        ->label('Initial Deposit')
                        ->placeholder('0.00')
                        ->numeric()->minValue(0)->step('0.01')
                        ->default(0)
                        ->dehydrated(false)   // not stored in accounts table
                        ->visibleOn('create')
                        ->required(),         // required on create (UI)

                    TextInput::make('balance')
                        ->label('Balance')
                        ->placeholder('0.00')
                        ->numeric()
                        ->disabled()
                        ->default(0)
                        ->helperText('Computed via transactions'),
                ])
                ->columns(2)
                ->compact(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Acct ID')
                    ->sortable()
                    ->toggleable(),

                BadgeColumn::make('account_type')
                    ->label('Type')
                    ->colors([
                        'success' => 'savings',
                        'info'    => 'checking',
                        'warning' => 'time',
                    ])
                    ->icons([
                        'heroicon-m-banknotes'        => 'savings',
                        'heroicon-m-building-library' => 'checking',
                        'heroicon-m-clock'            => 'time',
                    ])
                    ->formatStateUsing(fn ($state) => [
                        'savings' => 'Savings',
                        'checking'=> 'Checking',
                        'time'    => 'Time Deposit',
                    ][$state] ?? ucfirst((string) $state))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('account_number')
                    ->label('Account #')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Account number copied'),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Owner')
                    ->searchable(),

                Tables\Columns\TextColumn::make('currency')
                    ->sortable(),

                Tables\Columns\TextColumn::make('balance')
                    ->label('Balance')
                    ->money(fn ($record) => strtolower($record->currency ?? 'php'))
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'open',
                        'warning' => 'frozen',
                        'gray'    => 'closed',
                    ])
                    ->icons([
                        'heroicon-m-check-circle' => 'open',
                        'heroicon-m-pause-circle' => 'frozen',
                        'heroicon-m-x-circle'     => 'closed',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Opened')
                    ->dateTime()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('account_type')
                    ->label('haha ')
                    ->options([
                        'savings'  => 'Savings',
                        'checking' => 'Checking',
                        'time'     => 'Time Deposit',
                    ])
                    ->placeholder('All types'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'open'   => 'Open',
                        'frozen' => 'Frozen',
                        'closed' => 'Closed',
                    ])
                    ->placeholder('Any status'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),

                    Action::make('deposit')
                        ->label('Deposit')
                        ->icon('heroicon-o-arrow-down-circle')
                        ->color('success')
                        ->modalHeading('Post Deposit')
                        ->form([
                            Forms\Components\TextInput::make('amount')
                                ->label('Amount')
                                ->placeholder('0.00')
                                ->numeric()->minValue(0.01)->step('0.01')->required(),
                            Forms\Components\TextInput::make('remarks')
                                ->label('Remarks')
                                ->placeholder('Admin deposit')
                                ->maxLength(255)->default('Admin deposit'),
                        ])
                        ->action(function (Account $a, array $data) {
                            try {
                                DB::transaction(function () use ($a, $data) {
                                    $acc = Account::whereKey($a->id)->lockForUpdate()->first();
                                    if ($acc->status !== 'open') {
                                        throw new Exception('Account not open.');
                                    }
                                    $amt = (float) $data['amount'];

                                    Transaction::create([
                                        'account_id'   => $acc->id,
                                        'type'         => 'deposit',
                                        'amount'       => $amt,
                                        'currency'     => $acc->currency,
                                        'reference_no' => (string) \Str::uuid(),
                                        'status'       => 'posted',
                                        'remarks'      => $data['remarks'] ?? 'Admin deposit',
                                    ]);

                                    $acc->increment('balance', $amt);
                                });

                                Notification::make()->title('Deposit posted')->success()->send();
                            } catch (\Throwable $e) {
                                Notification::make()->title('Deposit failed')->body($e->getMessage())->danger()->send();
                            }
                        }),

                    Action::make('withdraw')
                        ->label('Withdraw')
                        ->icon('heroicon-o-arrow-up-circle')
                        ->color('warning')
                        ->modalHeading('Post Withdrawal')
                        ->form([
                            Forms\Components\TextInput::make('amount')
                                ->label('Amount')
                                ->placeholder('0.00')
                                ->numeric()->minValue(0.01)->step('0.01')->required(),
                            Forms\Components\TextInput::make('remarks')
                                ->label('Remarks')
                                ->placeholder('Admin withdrawal')
                                ->maxLength(255)->default('Admin withdrawal'),
                        ])
                        ->action(function (Account $a, array $data) {
                            try {
                                DB::transaction(function () use ($a, $data) {
                                    $acc = Account::whereKey($a->id)->lockForUpdate()->first();
                                    if ($acc->status !== 'open') {
                                        throw new Exception('Account not open.');
                                    }

                                    $amt = (float) $data['amount'];
                                    if ((float) $acc->balance < $amt) {
                                        throw new Exception('Insufficient balance.');
                                    }

                                    Transaction::create([
                                        'account_id'   => $acc->id,
                                        'type'         => 'withdrawal',
                                        'amount'       => $amt,
                                        'currency'     => $acc->currency,
                                        'reference_no' => (string) \Str::uuid(),
                                        'status'       => 'posted',
                                        'remarks'      => $data['remarks'] ?? 'Admin withdrawal',
                                    ]);

                                    $acc->decrement('balance', $amt);
                                });

                                Notification::make()->title('Withdrawal posted')->success()->send();
                            } catch (\Throwable $e) {
                                Notification::make()->title('Withdrawal failed')->body($e->getMessage())->danger()->send();
                            }
                        }),

                    Action::make('freeze')
                        ->label('Freeze')
                        ->icon('heroicon-o-pause-circle')
                        ->visible(fn (Account $r) => $r->status === 'open')
                        ->requiresConfirmation()
                        ->color('warning')
                        ->action(fn (Account $r) => $r->update(['status' => 'frozen'])),

                    Action::make('close')
                        ->label('Close')
                        ->icon('heroicon-o-x-circle')
                        ->visible(fn (Account $a) => $a->status !== 'closed')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->action(function (Account $a) {
                            if ((float) $a->balance !== 0.0) {
                                throw new Exception('Balance must be zero to close.');
                            }
                            $a->update(['status' => 'closed']);
                        }),
                ])->icon('heroicon-m-ellipsis-horizontal'),
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
            'index'  => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit'   => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
