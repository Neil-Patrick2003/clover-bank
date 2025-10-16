<?php

namespace App\Filament\Resources\CustomerApplicationResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Forms;
use Filament\Tables\Table;
use Filament\Forms\Form;

class AccountRequestRelationManager extends RelationManager
{
    protected static string $relationship = 'accountRequest';
    protected static ?string $title = 'Requested Account';

    public function form(Form $form): Form
    {
        return $form->schema([
            // accountRequest relation manager form
            Forms\Components\Select::make('requested_type')
                ->options(['savings'=>'Savings','current'=>'Current','time_deposit'=>'Time Deposit'])
                ->required(),
            Forms\Components\TextInput::make('currency')->default('PHP')->required(),
            Forms\Components\TextInput::make('initial_deposit')->numeric()->minValue(0)->default(0),

        ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('requested_type')->label('Type'),
                Tables\Columns\TextColumn::make('currency'),
                Tables\Columns\TextColumn::make('initial_deposit')->money('php'),
            ])
            ->headerActions([ Tables\Actions\CreateAction::make(), ])
            ->actions([ Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make(), ]);
    }
}
