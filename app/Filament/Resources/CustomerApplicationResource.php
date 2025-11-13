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
use Illuminate\Support\Str; // Added use statement for Str::uuid()

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
                Tables\Columns\TextColumn::make('id')
                    ->label('App#')
                    ->sortable()
                    ->searchable()
                    ->color('gray'), // Use a subtle color for the ID

                Tables\Columns\TextColumn::make('applicant.email')
                    ->label('Customer')
                    ->searchable()
                    ->limit(30) // Truncate long emails
                    ->description(fn(CustomerApplication $r) => $r->applicant->username ?? null), // Add username as description

                Tables\Columns\TextColumn::make('status')->badge()->colors([
                    'gray' => 'draft',
                    'warning' => 'submitted',
                    'info' => 'in_review',
                    'success' => 'approved',
                    'danger' => 'rejected',
                    'primary' => 'withdrawn', // Added color for withdrawn
                ])->sortable(),

                Tables\Columns\TextColumn::make('channel')->sortable(),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->dateTime('M d, Y h:i A') // Cleaner date format
                    ->label('Submitted')
                    ->sortable()
                    ->color('secondary')
                    ->size('sm'), // Smaller text size

                Tables\Columns\TextColumn::make('decided_at')
                    ->dateTime('M d, Y h:i A') // Cleaner date format
                    ->label('Decided')
                    ->sortable()
                    ->color('secondary')
                    ->size('sm'), // Smaller text size
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'submitted'=>'Submitted','in_review'=>'In Review','approved'=>'Approved','rejected'=>'Rejected'
                    ])
                    ->default('submitted'), // Filter by 'submitted' by default
            ])
            ->actions([
                // 1. Edit Action
                Tables\Actions\EditAction::make()
                    ->tooltip('Edit Details'),

                // 2. Review Action
                Action::make('review')
                    ->label('') // Icon-only design
                    ->icon('heroicon-o-eye')
                    ->tooltip('Move to Review')
                    ->requiresConfirmation()
                    ->modalHeading('Mark as In Review? ')
                    ->visible(fn(CustomerApplication $r) => in_array($r->status, ['submitted','draft']))
                    // FIX: Use proper closure when requiresConfirmation() is used
                    ->action(function (CustomerApplication $r) {
                        $r->update(['status'=>'in_review']);
                    }),

                // 3. Approve Action
                Action::make('approve')
                    ->label('') // Icon-only design
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->tooltip('Approve & Open Account')
                    ->requiresConfirmation()
                    ->modalHeading('Approve application and open account?')
                    ->visible(fn(CustomerApplication $r) => in_array($r->status, ['submitted', 'in_review']))
                    ->action(function (CustomerApplication $r) {
                        try {
                            DB::transaction(function () use ($r) {
                                // ... (Approval logic remains the same) ...
                                $req = ApplicationAccount::query()
                                    ->where('application_id', $r->id)
                                    ->lockForUpdate()
                                    ->first();

                                if (! $req) {
                                    throw new \RuntimeException('No ApplicationAccount found for this application. Please fill the account request (currency, initial deposit) first.');
                                }

                                $accountNumber = \App\Services\Banking\AccountNumberGenerator::make();

                                $account = Account::create([
                                    'user_id'        => $r->user_id,
                                    'account_number' => $accountNumber,
                                    'currency'       => $req->currency ?? 'PHP',
                                    'balance'        => 0,
                                    'status'         => 'open',
                                ]);

                                $initial = (float) ($req->initial_deposit ?? 0);
                                if ($initial > 0) {
                                    Transaction::create([
                                        'account_id'   => $account->id,
                                        'type'         => 'deposit',
                                        'amount'       => $initial,
                                        'currency'     => $account->currency,
                                        'reference_no' => (string) Str::uuid(),
                                        'status'       => 'posted',
                                        'remarks'      => 'Initial deposit on approval',
                                    ]);

                                    $account->increment('balance', $initial);
                                }

                                $r->update([
                                    'status'     => 'approved',
                                    'decided_at' => now(),
                                ]);

                            });

                            \Filament\Notifications\Notification::make()
                                ->title('Application approved')
                                ->body('Account opened successfully.')
                                ->success()
                                ->send();

                        } catch (\Throwable $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Approval failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();

                            throw $e;
                        }
                    }),

                // 4. Reject Action
                Action::make('reject')
                    ->label('') // Icon-only design
                    ->icon('heroicon-o-x-circle')->color('danger')
                    ->tooltip('Reject Application')
                    ->requiresConfirmation()
                    ->modalHeading('Reject application? Please provide a reason:')

                    ->visible(fn(CustomerApplication $r) => in_array($r->status, ['submitted','in_review']))
                    ->form([ Forms\Components\Textarea::make('remarks')->required() ])
                    // FIX: Use proper closure when requiresConfirmation() is used
                    ->action(function (CustomerApplication $r, array $data) {
                        $r->update(['status'=>'rejected','remarks'=>$data['remarks'],'decided_at'=>now()]);
                    }),
            ])
            ->bulkActions([ Tables\Actions\DeleteBulkAction::make(), ]);
    }
    public static function getRelations(): array
    {
        return [
                        CustomerApplicationResource\RelationManagers\DocumentsRelationManager::class,
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
