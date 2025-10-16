<?php

namespace App\Filament\Resources;

use App\Models\Account;
use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\Transaction;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Str;

class BillPaymentResource extends Resource
{
    protected static ?string $model = BillPayment::class;
    protected static ?string $navigationGroup = 'Payments';
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('account_id')->relationship('account','account_number')->searchable()->required(),
            Forms\Components\Select::make('biller_id')->relationship('biller','biller_name')->searchable()->required(),
            Forms\Components\TextInput::make('amount')->numeric()->minValue(0.01)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('account.account_number')->label('Account'),
            Tables\Columns\TextColumn::make('biller.biller_name')->label('Biller'),
            Tables\Columns\TextColumn::make('amount')->money('php'),
            Tables\Columns\TextColumn::make('reference_no')->copyable(),
            Tables\Columns\BadgeColumn::make('status')->colors([
                'success'=>'posted','warning'=>'pending','danger'=>'failed','gray'=>'reversed'
            ]),
            Tables\Columns\TextColumn::make('created_at')->dateTime(),
        ])->defaultSort('id','desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => BillPaymentResource\Pages\ListBillPayments::route('/'),
            'create' => BillPaymentResource\Pages\CreateBillPayment::route('/create'),
        ];
    }
}
