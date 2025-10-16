<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerApplicationResource\Pages;
use App\Models\CustomerApplication;
use App\Models\ApplicationAccount;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\DBAL\TimestampType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;

class CustomerApplicationResource extends Resource
{
    protected static ?string $model = CustomerApplication::class;
    protected static ?string $navigationGroup = 'Onboarding';
    protected static ?string $navigationIcon = 'heroicon-o-identification';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Admin selects (or creates) the customer
            Forms\Components\Select::make('user_id')
                ->label('Customer')
                ->relationship('applicant','email') // or 'username'
                ->searchable()
                ->preload()
                ->required()
                // inline-create a new customer if needed
                ->createOptionForm([
                    Forms\Components\TextInput::make('username')->required()->maxLength(80),
                    Forms\Components\TextInput::make('email')->email()->required()->maxLength(160),
                    Forms\Components\TextInput::make('password')->password()->required()->rule('min:8'),
                    Forms\Components\Hidden::make('role')->default('customer'),
                    Forms\Components\Hidden::make('status')->default('active'),
                ])
                ->createOptionUsing(function (array $data) {
                    // hash handled by casts(['password' => 'hashed']) on User model
                    return User::create($data)->getKey();
                }),

            Forms\Components\TextInput::make('product_type')
                ->default('account_opening')->readOnly(),

            Forms\Components\Select::make('channel')
                ->options(['web'=>'Web','mobile'=>'Mobile','branch'=>'Branch'])
                ->default('branch')
                ->required(),

            Forms\Components\Select::make('status')
                ->options([
                    'draft'=>'Draft','submitted'=>'Submitted','in_review'=>'In Review',
                    'approved'=>'Approved','rejected'=>'Rejected','withdrawn'=>'Withdrawn',
                ])
                ->default('submitted')
                ->required(),

            Forms\Components\Select::make('assigned_admin_id')
                ->label('Assigned Admin')
                ->options(User::query()->where('role','admin')->pluck('email','id'))
                ->default(fn () => Auth::id())   // auto-assign current admin
                ->searchable(),

            Forms\Components\Textarea::make('remarks')->rows(2),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('App#')->sortable(),
                Tables\Columns\TextColumn::make('applicant.email')->label('Customer')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge()->colors([
                    'gray' => 'draft',
                    'warning' => 'submitted',
                    'info' => 'in_review',
                    'success' => 'approved',
                    'danger' => 'rejected',
                ])->sortable(),
                Tables\Columns\TextColumn::make('channel')->sortable(),
                Tables\Columns\TextColumn::make('submitted_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('decided_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'submitted'=>'Submitted','in_review'=>'In Review','approved'=>'Approved','rejected'=>'Rejected'
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('review')
                    ->label('Move to Review')->icon('heroicon-o-eye')
                    ->requiresConfirmation()
                    ->visible(fn(CustomerApplication $r) => in_array($r->status, ['submitted','draft']))
                    ->action(fn(CustomerApplication $r) => $r->update(['status'=>'in_review'])),
                // In CustomerApplicationResource table actions:
                Action::make('approve')
                    ->label('Approve & Open Account')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    // ->form([])  // ← remove the form entirely
                    ->action(function (CustomerApplication $r) {
                        DB::transaction(function () use ($r) {
                            $req = ApplicationAccount::where('application_id', $r->id)->firstOrFail();

                            // 1) generate account number (collision-safe)
                            $accountNumber = \App\Models\Account::generateNumber($r); // see model method below

                            // 2) create live account
                            $account = Account::create([
                                'user_id'        => $r->user_id,
                                'account_number' => $accountNumber,
                                'currency'       => $req->currency,
                                'balance'        => $req->initial_deposit,
                                'status'         => 'open',
                            ]);

                            // 3) initial deposit
                            if ($req->initial_deposit > 0) {
                                Transaction::create([
                                    'account_id'   => $account->id,
                                    'type'         => 'deposit',
                                    'amount'       => $req->initial_deposit,
                                    'currency'     => $req->currency,
                                    'reference_no' => (string) \Str::uuid(),
                                    'status'       => 'posted',
                                    'remarks'      => 'Initial deposit on approval',
                                ]);
                            }

                            // 4) mark approved
                            $r->update(['status' => 'approved', 'decided_at' => now()]);
                        });
                    }),

                Action::make('reject')
                    ->label('Reject')->icon('heroicon-o-x-circle')->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn(CustomerApplication $r) => in_array($r->status, ['submitted','in_review']))
                    ->form([ Forms\Components\Textarea::make('remarks')->required() ])
                    ->action(fn(CustomerApplication $r, array $data) => $r->update([
                        'status'=>'rejected','remarks'=>$data['remarks'],'decided_at'=>now()
                    ])),
            ])
            ->bulkActions([ Tables\Actions\DeleteBulkAction::make(), ]);
    }

    public static function getRelations(): array
    {
        return [
    //            CustomerApplicationResource\RelationManagers\DocumentsRelationManager::class,
    //            CustomerApplicationResource\RelationManagers\RequestRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomerApplications::route('/'),
            'create' => Pages\CreateCustomerApplication::route('/create'),
            'edit'   => Pages\EditCustomerApplication::route('/{record}/edit'),
        ];
    }
}
