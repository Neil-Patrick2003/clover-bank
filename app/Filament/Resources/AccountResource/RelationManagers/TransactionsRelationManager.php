<?php

namespace App\Filament\Resources\AccountResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';
    protected static ?string $title = 'Transactions';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            Tables\Columns\TextColumn::make('type')->badge(),
            Tables\Columns\TextColumn::make('amount')->money('php'),
            Tables\Columns\TextColumn::make('currency'),
            Tables\Columns\TextColumn::make('reference_no')->copyable(),
            Tables\Columns\BadgeColumn::make('status')->colors([
                'success'=>'posted','warning'=>'pending','danger'=>'failed','gray'=>'reversed'
            ]),
            Tables\Columns\TextColumn::make('remarks')->limit(40),
        ])->defaultSort('created_at','desc');
    }
}
