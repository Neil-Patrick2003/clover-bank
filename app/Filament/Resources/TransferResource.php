<?php

namespace App\Filament\Resources;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Transfer;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class TransferResource extends Resource
{
    protected static ?string $model = Transfer::class;
    protected static ?string $navigationGroup = 'Banking';
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('from_account_id')->label('From')
                ->relationship('fromAccount','account_number')->searchable()->required(),
            Forms\Components\Select::make('to_account_id')->label('To')
                ->relationship('toAccount','account_number')->searchable()->required(),
            Forms\Components\TextInput::make('amount')->numeric()->minValue(0.01)->required(),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('fromAccount.account_number')->label('From'),
            Tables\Columns\TextColumn::make('toAccount.account_number')->label('To'),
            Tables\Columns\TextColumn::make('amount')->money('php'),
            Tables\Columns\TextColumn::make('currency'),
            Tables\Columns\TextColumn::make('created_at')->dateTime(),
        ])
            ->defaultSort('id','desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => TransferResource\Pages\ListTransfers::route('/'),
            'create' => TransferResource\Pages\CreateTransfer::route('/create'),
        ];
    }
}
